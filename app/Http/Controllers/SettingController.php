<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman settings
     */
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        try {
            foreach ($request->settings as $key => $value) {
                $setting = Setting::where('key', $key)->first();

                if (!$setting) {
                    continue;
                }

                // Handle array/json types
                if ($setting->type === 'array' || $setting->type === 'json') {
                    // Convert value to array if it's a string
                    if (is_string($value)) {
                        $value = json_decode($value, true) ?: [];
                    }
                    $value = json_encode($value);
                }

                $setting->update(['value' => $value]);

                // Clear cache
                Cache::forget("setting_{$key}");
            }

            return redirect()->route('settings.index')
                           ->with('success', 'Pengaturan berhasil disimpan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Reset settings to default
     */
    public function reset()
    {
        try {
            // Reset work_days
            Setting::where('key', 'work_days')
                   ->update(['value' => json_encode([1, 2, 3, 4, 5, 6])]);

            // Reset other settings
            Setting::where('key', 'work_hours_per_day')
                   ->update(['value' => '8']);

            Setting::where('key', 'pulang_awal_deduction')
                   ->update(['value' => '10000']);

            // Clear all settings cache
            Cache::flush();

            return redirect()->route('settings.index')
                           ->with('success', 'Pengaturan berhasil direset ke default.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reset pengaturan: ' . $e->getMessage());
        }
    }
}
