<?php

namespace App\Http\Controllers;

use App\Enums\Service;
use App\Enums\ServiceStatus;
use App\Models\ServiceRequest;
use App\Models\UnavailableWeek;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceRequestController extends Controller
{
    public function create(Request $request)
    {
        $serviceParam = $request->query('service') ?? $request->query('service_type');
        $selectedService = null;

        if (is_string($serviceParam)) {
            $selectedService = Service::tryFrom($serviceParam)?->value;

            if (! $selectedService) {
                foreach (Service::cases() as $case) {
                    if (strcasecmp($case->name, $serviceParam) === 0 || strcasecmp($case->value, $serviceParam) === 0) {
                        $selectedService = $case->value;
                        break;
                    }
                }
            }
        }

        if (! $selectedService) {
            $selectedService = Service::ENKEL_SERVICE->value;
        }

        return view('service.create', [
            'weeks' => $this->availableWeeks(),
            'serviceTypes' => Service::cases(),
            'selectedService' => $selectedService,
        ]);
    }

    public function store(Request $request)
    {
        $weeks = $this->availableWeeks();

        $request->merge(['wants_pickup' => $request->boolean('wants_pickup')]);

        $data = $request->validate([
            'service_type' => ['required', Rule::enum(Service::class)],
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|string|max:40',
            'message' => 'required|string|max:4000',
            'week_start' => ['required', 'date', Rule::in(array_keys($weeks))],
            'wants_pickup' => 'boolean',
            'address' => 'nullable|required_if:wants_pickup,true|string|max:190',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|max:5120',
            'website' => 'nullable|max:0',
        ], [
            'message.required' => 'Du må beskrive hva som skal gjøres eller fikses på sykkelen.',
            'week_start.required' => 'Du må velge en uke for service.',
            'week_start.in' => 'Den valgte uken er dessverre ikke ledig lenger. Velg en annen uke.',
        ]);

        $serviceRequest = ServiceRequest::create([
            'service_type' => $data['service_type'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'week_start' => $data['week_start'],
            'wants_pickup' => $data['wants_pickup'],
            'address' => $data['wants_pickup'] ? ($data['address'] ?? null) : null,
            'status' => ServiceStatus::PENDING,
        ]);

        if ($request->hasFile('images')) {
            $paths = [];

            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('service-requests/'.$serviceRequest->id, 'public');
            }

            $serviceRequest->update(['images' => $paths]);
        }

        return back()->with('status', 'Takk! Vi har mottatt serviceforespørselen din og gir deg beskjed så snart den er behandlet.');
    }

    /**
     * Upcoming bookable weeks (one bike accepted per week), keyed by the
     * Monday date (Y-m-d) with a human-readable Norwegian label.
     *
     * @return array<string, string>
     */
    private function availableWeeks(int $count = 12): array
    {
        $taken = ServiceRequest::query()
            ->where('status', ServiceStatus::APPROVED)
            ->pluck('week_start')
            ->map(fn ($date): string => CarbonImmutable::parse($date)->format('Y-m-d'))
            ->all();

        $unavailable = UnavailableWeek::query()
            ->pluck('week_start')
            ->map(fn ($date): string => CarbonImmutable::parse($date)->format('Y-m-d'))
            ->all();

        $blocked = array_unique(array_merge($taken, $unavailable));

        $week = CarbonImmutable::now()->startOfWeek()->addWeek();
        $weeks = [];

        while (count($weeks) < $count) {
            $key = $week->format('Y-m-d');

            if (! in_array($key, $blocked, true)) {
                $weeks[$key] = sprintf(
                    'Uke %d (%s–%s)',
                    $week->isoWeek(),
                    $week->format('d.m'),
                    $week->endOfWeek()->format('d.m'),
                );
            }

            $week = $week->addWeek();
        }

        return $weeks;
    }
}
