<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Auth\Notifications\ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.url')."/reset-password/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $settings = \App\Models\Setting::where('group', 'mail')->get()->pluck('value', 'key');
            
            if ($settings->isNotEmpty()) {
                config([
                    'mail.default' => $settings['mail_mailer'] ?? 'smtp',
                    'mail.mailers.smtp.host' => $settings['mail_host'],
                    'mail.mailers.smtp.port' => $settings['mail_port'],
                    'mail.mailers.smtp.username' => $settings['mail_username'],
                    'mail.mailers.smtp.password' => $settings['mail_password'],
                    'mail.mailers.smtp.encryption' => $settings['mail_encryption'],
                    'mail.from.address' => $settings['mail_from_address'],
                    'mail.from.name' => $settings['mail_from_name'],
                ]);
            }
        }
    }
}
