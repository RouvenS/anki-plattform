<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired link.');
        }

        $user = User::findOrFail($id);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return view('auth.verify-email-success');
    }
}