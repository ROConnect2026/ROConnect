<?php

namespace App\Providers;

use App\Services\Translation\Contracts\TranslationProviderInterface;
use App\Services\Translation\Providers\TranslateApiService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TranslationProviderInterface::class, function (): TranslationProviderInterface {
            return new TranslateApiService(
                baseUrl: (string) config('services.translation.base_url'),
                apiKey: (string) config('services.translation.api_key'),
                timeoutSeconds: (int) config('services.translation.timeout', 8),
                verifySsl: (bool) config('services.translation.verify_ssl', true),
                caBundlePath: config('services.translation.ca_bundle'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        $this->configureDefaults();

        if (config('app.env') !== 'local' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
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
}
