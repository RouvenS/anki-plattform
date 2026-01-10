<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Verified;
use App\Mail\VerifyEmail;

use Illuminate\Support\Facades\Config;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired link.');
        }

        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid link structure.');
        }

        if (! $user->hasVerifiedEmail()) {
            if ($user->free_cards_remaining == 0) {
                $user->free_cards_remaining = Config::get('trial.free_cards_total', 10);
                $user->save();
            }
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return view('auth.verify-email-success');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home'));
        }

        $url = URL::temporarySignedRoute(
            'email.verify',
            now()->addHours(12),
            [
                'id' => $request->user()->getKey(),
                'hash' => sha1($request->user()->getEmailForVerification()),
            ]
        );

        Mail::to($request->user())->send(new VerifyEmail($url));

        return back()->with('success', 'Verification link sent!');
    }
}