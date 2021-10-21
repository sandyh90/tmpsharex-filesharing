<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Session_Manage;

use Intervention\Image\Facades\Image;

class MySettings extends Controller
{
    public function mysettings()
    {
        $session = new Session_Manage;
        $data = [
            'session_list' => $session->get_session_data(Auth::user()->id)
        ];
        return view('user.my_settings', $data);
    }

    public function update_profile(Request $request)
    {
        if ($request->isMethod('POST')) {
            $user_data = User::where('id', Auth::user()->id)->first();

            $validator = Validator::make(
                $request->all(),
                [
                    'fullname' => 'required|string',
                    'username' => 'required|unique:users,username',
                    'user_photo' => 'image|mimetypes:image/jpg,image/jpeg,image/png|max:2048',
                ]
            );

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {

                //Processing photo if user upload new photo file

                if ($request->hasFile('user_photo')) {
                    if ($user_data['user_photo'] != 'no-image') {
                        if (File::exists(storage_path('app/system/profile/' . $user_data['user_photo']))) {
                            File::delete(storage_path('app/system/profile/' . $user_data['user_photo']));
                        }
                    }

                    $gen_resultcode = Str::random(16) . time();
                    $storage_dir = storage_path('app/system/profile');
                    if (!file_exists($storage_dir)) {
                        @mkdir($storage_dir);
                    }
                    $save_path = $request->file('user_photo')->move($storage_dir, 'avatar_' . $gen_resultcode . '.png');

                    Image::make($save_path)->fit(500, 500)->save();


                    $photo_file = 'avatar_' . $gen_resultcode . '.png';
                } else {
                    $photo_file = $user_data['user_photo'];
                }

                $db_user = User::where('id', $user_data['id']);

                $db_user->update(
                    [
                        'name' => $request->input('fullname'),
                        'username' => $request->input('username'),
                        'user_photo' => $photo_file,
                    ]
                );
            }
            return redirect()->back()->with('setting_msg', '<div class="alert alert-success text-center alert-dismissible fade show" role="alert">Your profile successfully change.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        }
    }

    public function update_password(Request $request)
    {
        if ($request->isMethod('POST')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'old_password' => 'required',
                    'new_password' => 'required|min:8|same:new_password_confirm',
                    'new_password_confirm' => 'required|min:8|same:new_password',
                ]
            );

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {
                $matchpass = [
                    'old' => $request->input('old_password'),
                    'new' => $request->input('new_password')
                ];
                if ($matchpass['old'] == $matchpass['new']) {
                    return redirect()->back()->with('setting_msg', '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert">New password must be different from old password.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } elseif (!Hash::check($matchpass['old'], Auth::user()->password)) {
                    return redirect()->back()->with('setting_msg', '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert">Old password not match.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } else {
                    User::where('id', Auth::user()->id)->update(['password' => Hash::make($request->input('new_password'))]);
                    Auth::logoutOtherDevices($request->input('old_password'));
                    return redirect()->back()->with('setting_msg', '<div class="alert alert-success text-center alert-dismissible fade show" role="alert">Your password has been change.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            }
        }
    }

    public function logout_all_session(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'logout_password' => 'required'
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('setting_msg', '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert">' . $validator->errors()->first() . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        } else {
            if (Hash::check($request->input('logout_password'), Auth::user()->password)) {
                $check_data = Session_Manage::where('user_id', Auth::user()->id)->first();

                if ($check_data) {
                    Auth::logoutOtherDevices($request->input('logout_password'));
                    Session_Manage::where('user_id', Auth::user()->id)->delete();
                    return redirect()->route('login');
                }
            } else {
                return redirect()->back()->with('setting_msg', '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert">Your input password wrong.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            }
        }
    }
}
