<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Transformers\SuccessResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  LoginRequest $request
     * @return SuccessResource
     */
    public function store(LoginRequest $request): SuccessResource
    {
        $request->authenticate();

        $request->session()->regenerate();

        return new SuccessResource();
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return SuccessResource
     */
    public function destroy(Request $request): SuccessResource
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return new SuccessResource();
    }
}
