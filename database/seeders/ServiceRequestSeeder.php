<?php

namespace Database\Seeders;

use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use App\Models\ServiceRequest;
use Carbon\CarbonImmutable;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class ServiceRequestSeeder extends Seeder
{
    private const TOTAL_REQUESTS = 50;

    public function run(): void
    {
        foreach ($this->requests() as $data) {
            $createdAt = $data['created_at'] ?? now();
            unset($data['created_at']);

            $request = ServiceRequest::updateOrCreate(
                [
                    'email' => $data['email'],
                ],
                $data,
            );

            $request->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->saveQuietly();
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function requests(): array
    {
        $faker = FakerFactory::create('nb_NO');
        $faker->seed(54321);

        $messages = $this->messagePool();
        $addresses = $this->addressPool();
        $serviceTypes = ServiceType::cases();

        // 25 weeks range: from -12 weeks ago to +12 weeks in future
        $startOfWeek = CarbonImmutable::now()->startOfWeek();

        $requests = [];
        $approvedWeeks = [];

        for ($i = 0; $i < self::TOTAL_REQUESTS; $i++) {
            $weekOffset = $faker->numberBetween(-12, 12);
            $weekStart = $startOfWeek->addWeeks($weekOffset)->format('Y-m-d');

            // Max 1 approved per week
            if (! in_array($weekStart, $approvedWeeks, true) && $faker->boolean(40)) {
                $status = ServiceStatus::APPROVED;
                $approvedWeeks[] = $weekStart;
            } else {
                $status = $faker->randomElement([ServiceStatus::PENDING, ServiceStatus::DENIED]);
            }

            $wantsPickup = $faker->boolean(35);
            $address = $wantsPickup ? $faker->randomElement($addresses) : null;
            $serviceType = $faker->randomElement($serviceTypes);

            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $name = "{$firstName} {$lastName}";
            $email = strtolower("{$firstName}.{$lastName}.{$i}@example.no");

            // Creation timestamp correlated with week offset
            $createdAt = $startOfWeek->addWeeks($weekOffset)->subDays($faker->numberBetween(1, 10));

            $requests[] = [
                'service_type' => $serviceType,
                'name' => $name,
                'email' => $email,
                'phone' => $faker->boolean(85) ? $faker->phoneNumber() : null,
                'message' => $faker->randomElement($messages),
                'week_start' => $weekStart,
                'wants_pickup' => $wantsPickup,
                'address' => $address,
                'status' => $status,
                'images' => null,
                'created_at' => $createdAt,
            ];
        }

        return $requests;
    }

    /**
     * @return array<int, string>
     */
    private function messagePool(): array
    {
        return [
            'Girene hopper over kjede under belastning. Ønsker en enkel service og sjekk av kjede.',
            'Bremsene skriker veldig og tar dårlig. Ønsker sjekk av klosser og skiver.',
            'Sykkelen har stått i boden i hele vinter, trenger en vårklargjøring før sesongen.',
            'Kjedet datt av og bakgiret ser skjevt ut etter et uheldig velt.',
            'Knirking i kranklageret når jeg trår hardt i motbakker.',
            'Punktering på bakhjulet og slitte dekk foran og bak. Trenger nye dekk og slanger.',
            'Ønsker en full service med grundig vask og rens av drivverk før sykkelferien.',
            'Kombi-pedalene er løse og styret sitter litt skjevt etter transport.',
            'Bremsehåndtaket går helt inn til styret uten særlig virkning. Trenger lufting/justering.',
            'Vil ha en generell sikkerhetssjekk og smøring før daglig jobbpendling.',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function addressPool(): array
    {
        return [
            'Storgata 14, 8006 Bodø',
            'Stasjonsveien 5, 8200 Fauske',
            'Sjøgata 12, 8250 Rognan',
            'Parkveien 8, 8005 Bodø',
            'Kjerringøyveien 42, 8011 Bodø',
            'Torggata 3, 8200 Fauske',
            'Prinsens gate 22, 8003 Bodø',
            'Jernbanegata 9, 8250 Rognan',
            'Dronningens gate 15, 8006 Bodø',
            'Sollia 7, 8200 Fauske',
        ];
    }
}
