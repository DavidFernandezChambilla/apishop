<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->value];
        });
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                // If it's a file (like the QR), store it
                $path = $request->file($key)->store('settings', 'public');
                $value = $path; // Guardamos solo el path relativo

                // Delete old image if exists
                $oldSetting = Setting::where('key', $key)->first();
                if ($oldSetting && $oldSetting->value) {
                    // Limpiamos el path por si era una URL absoluta antigua
                    $oldPath = $oldSetting->value;
                    if (str_contains($oldPath, '/storage/')) {
                        $parts = explode('/storage/', $oldPath);
                        $oldPath = end($parts);
                    }
                    Storage::disk('public')->delete($oldPath);
                }
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json(['message' => 'Configuración actualizada']);
    }
}
