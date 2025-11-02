<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $appUrl = config('app.url');

        $corsOrigins = ['http://localhost'];

        if ($appUrl) {
            $normalizedAppUrl = filter_var($appUrl, FILTER_VALIDATE_URL) ? rtrim($appUrl, '/') : null;

            if ($normalizedAppUrl && !in_array($normalizedAppUrl, $corsOrigins, true)) {
                $corsOrigins[] = $normalizedAppUrl;
            }
        }

        $ankiConnectConfig = [
            'apiKey' => $user->anki_api_key,
            'apiLogPath' => null,
            'ignoreOriginList' => [],
            'webBindAddress' => '127.0.0.1',
            'webBindPort' => 8765,
            'webCorsOriginList' => $corsOrigins,
        ];

        $ankiConnectConfigJson = json_encode($ankiConnectConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return view('settings', [
            'user' => $user,
            'ankiConnectConfigJson' => $ankiConnectConfigJson,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'openai_api_key' => 'nullable|string|max:255',
            'anki_api_key' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->openai_api_key = filled($validated['openai_api_key'] ?? null)
            ? $validated['openai_api_key']
            : null;
        $user->anki_api_key = filled($validated['anki_api_key'] ?? null)
            ? $validated['anki_api_key']
            : null;
        $user->save();

        return redirect()->route('settings')->with('success', 'API keys saved successfully.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('settings')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('settings')->with('success', 'Password updated successfully.');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password does not match.']);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
