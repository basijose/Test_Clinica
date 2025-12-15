<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => 'nullable',
        ]);

        foreach ($data['settings'] as $item) {
            Setting::where('key', $item['key'])->update(['value' => $item['value']]);
        }

        return response()->json(['message' => 'Configuración actualizada correctamente']);
    }

    public function testEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            // Force reload config from DB to ensure latest settings are used
            $this->applyMailConfig();

            Mail::raw('Este es un correo de prueba desde el Panel CNF.', function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Prueba de Configuración SMTP');
            });

            return response()->json(['message' => 'Email enviado correctamente']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al enviar email: ' . $e->getMessage()], 500);
        }
    }

    private function applyMailConfig()
    {
        $settings = Setting::where('group', 'mail')->get()->pluck('value', 'key');
        
        if ($settings->isNotEmpty()) {
            Config::set('mail.default', $settings['mail_mailer'] ?? 'smtp');
            Config::set('mail.mailers.smtp.host', $settings['mail_host']);
            Config::set('mail.mailers.smtp.port', $settings['mail_port']);
            Config::set('mail.mailers.smtp.username', $settings['mail_username']);
            Config::set('mail.mailers.smtp.password', $settings['mail_password']);
            Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption']);
            Config::set('mail.from.address', $settings['mail_from_address']);
            Config::set('mail.from.name', $settings['mail_from_name']);
        }
    }
}
