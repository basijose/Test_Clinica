<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'mail_mailer', 'value' => 'smtp', 'group' => 'mail', 'type' => 'text', 'description' => 'Protocolo de envío (smtp, log)'],
            ['key' => 'mail_host', 'value' => 'smtp.gmail.com', 'group' => 'mail', 'type' => 'text', 'description' => 'Servidor SMTP'],
            ['key' => 'mail_port', 'value' => '587', 'group' => 'mail', 'type' => 'number', 'description' => 'Puerto SMTP'],
            ['key' => 'mail_username', 'value' => '', 'group' => 'mail', 'type' => 'text', 'description' => 'Usuario SMTP'],
            ['key' => 'mail_password', 'value' => '', 'group' => 'mail', 'type' => 'password', 'description' => 'Contraseña SMTP'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'group' => 'mail', 'type' => 'text', 'description' => 'Encriptación (tls, ssl)'],
            ['key' => 'mail_from_address', 'value' => 'no-reply@firu.com.ar', 'group' => 'mail', 'type' => 'text', 'description' => 'Email del remitente'],
            ['key' => 'mail_from_name', 'value' => 'Panel CNF', 'group' => 'mail', 'type' => 'text', 'description' => 'Nombre del remitente'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
