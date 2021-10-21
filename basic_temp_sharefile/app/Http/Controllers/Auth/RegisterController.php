<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class RegisterController extends Controller
{
    public function showregister()
    {
        if (env('AUTH_ALLOW_REGISTER_SELF', FALSE) == FALSE) {
            return redirect()->route('login');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (env('AUTH_ALLOW_REGISTER_SELF', FALSE) == FALSE) {
            return redirect()->route('login');
        }
        $validator = Validator::make(
            $request->all(),
            [
                'fullname' => 'required|string',
                'username' => 'required|unique:users,username|string',
                'password' => 'required|min:8|same:password_confirm',
                'password_confirm' => 'required|min:8|same:password',
                'captcha' => 'required|captcha',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        } else {
            User::create([
                'name' => $request->input('fullname'),
                'username' => $request->input('username'),
                'password' => Hash::make($request->input('password')),
                'user_photo' => 'no-image',
                'role_access' => 'member',
                'is_active' => TRUE,
                'is_email_verify' => FALSE,
            ]);
            return redirect()->route('login')->with('auth_msg', '<div class="alert alert-success text-center alert-dismissible fade show" role="alert">Congratulations, Your account has been created please login.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        }
    }
}
