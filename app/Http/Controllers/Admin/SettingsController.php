<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Show the Instagram settings form.
     */
    public function instagram()
    {
        $instagramUrl    = config('services.instagram.url', env('INSTAGRAM_URL', ''));
        $instagramHandle = config('services.instagram.handle', env('INSTAGRAM_HANDLE', ''));

        return view('admin.settings.instagram', compact('instagramUrl', 'instagramHandle'));
    }

    /**
     * Update Instagram settings in the .env file.
     */
    public function updateInstagram(Request $request)
    {
        $validated = $request->validate([
            'instagram_url'    => 'required|url|max:255',
            'instagram_handle' => 'required|string|max:100',
        ]);

        $this->updateEnv([
            'INSTAGRAM_URL'    => $validated['instagram_url'],
            'INSTAGRAM_HANDLE' => $validated['instagram_handle'],
        ]);

        // Clear config cache so changes take effect immediately
        Artisan::call('config:clear');

        return back()->with('success', 'Instagram settings updated successfully.');
    }

    /**
     * Write key=value pairs to the .env file.
     */
    private function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replace  = "{$key}={$value}";

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replace, $content);
            } else {
                $content .= "\n{$replace}";
            }
        }

        file_put_contents($envPath, $content);
    }
}
