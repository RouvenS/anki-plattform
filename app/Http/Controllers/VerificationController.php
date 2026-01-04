<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;

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
            $user->markEmailAsVerified();
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
            now()->addMinutes(60),
            [
                'id' => $request->user()->getKey(),
                'hash' => sha1($request->user()->getEmailForVerification()),
            ]
        );

        Mail::to($request->user())->send(new VerifyEmail($url));

        return back()->with('success', 'Verification link sent!');
    }
}