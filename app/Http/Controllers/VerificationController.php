<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;

class VerificationController extends Controller
{
    public function verify(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired link.');
        }

        $user = User::findOrFail($id);

        // Verify that the hash matches the user's email to prevent
        // one user from verifying another user's email address
        if (! hash_equals((string) $request->query('hash'), hash('sha256', $user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
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
                'id' => $request->user()->id,
                'hash' => hash('sha256', $request->user()->getEmailForVerification())
            ]
        );

        Mail::to($request->user())->send(new VerifyEmail($url));

        return back()->with('success', 'Verification link sent!');
    }
}