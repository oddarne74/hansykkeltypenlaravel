<?php

namespace App\Providers;

use App\Enums\BikeStatus;
use App\Models\Bike;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureViewData();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureViewData(): void
    {
        View::composer('*', function (\Illuminate\View\View $view): void {
            static $hasBikesForSale = null;

            if ($hasBikesForSale === null) {
                try {
                    $hasBikesForSale = Bike::published()
                        ->where('status', BikeStatus::FOR_SALE)
                        ->exists();
                } catch (Throwable) {
                    $hasBikesForSale = true;
                }
            }

            $view->with('hasBikesForSale', $hasBikesForSale);
        });
    }
}
