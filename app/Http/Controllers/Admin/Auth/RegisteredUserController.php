<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Models\User;
use App\Transformers\SuccessResource;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  RegisterRequest $request
     * @return SuccessResource
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): SuccessResource
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::guard('admin')->login($user);

        return new SuccessResource();
    }
}
