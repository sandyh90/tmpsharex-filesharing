<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

use App\Models\User;

use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;

class UserManage extends Controller
{
    public function manage_user()
    {
        return view('admin.user_manage');
    }

    /**
     * Ajax Function Section
     */

    public function add_user(Request $request)
    {
        $responses = [
            'csrftoken' => csrf_token(),
            'messages' => [],
            'success' => FALSE
        ];

        $validator = Validator::make(
            $request->all(),
            [
                'fullname_user' => 'required|string|max:255',
                'username_user' => 'required|unique:users,username',
                'password_user' => 'required|min:8',
                'role_user' => 'required|in:administrator,member',
                'active_user' => 'required',
                'user_photo' => 'image|mimetypes:image/jpg,image/jpeg,image/png|max:2048',
            ]
        );

        if ($validator->fails()) {
            $responses['messages'] = $validator->errors()->all();
        } else {
            $responses = [
                'csrftoken' => csrf_token(),
                'messages' => '<div class="alert alert-success">Add user successfully</div>',
                'success' => TRUE
            ];

            //Processing photo if user upload photo file

            if ($request->hasFile('user_photo')) {
                $gen_resultcode = Str::random(16) . time();
                $storage_dir = storage_path('app/public/profile');
                if (!file_exists($storage_dir)) {
                    @mkdir($storage_dir);
                }
                $save_path = $request->file('user_photo')->move($storage_dir, 'avatar_' . $gen_resultcode . '.png');

                Image::make($save_path)->fit(500, 500)->save();

                $photo_file = 'avatar_' . $gen_resultcode . '.png';
            } else {
                $photo_file = 'no-image';
            }

            User::create([
                'name' => $request->input('fullname_user'),
                'username' => $request->input('username_user'),
                'password' => Hash::make($request->input('password_user')),
                'role_access' => $request->input('role_user'),
                'is_active' => $request->boolean('active_user'),
                'user_photo' => $photo_file,
            ])->save();
        }

        return response()->json($responses);
    }

    public function delete_user(Request $request)
    {
        if ($request->isMethod('post')) {
            $user_data = User::where('id', $request->input('id_user'))->first();

            if ($user_data && $user_data['id'] != Auth::user()->id) {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'alert' => [
                        'icon' => 'success',
                        'title' => 'Delete User Success',
                        'text' => 'You has been deleted user!',
                    ]
                ];

                if ($user_data['user_photo'] != 'no-image') {
                    if (File::exists(storage_path('app/public/profile/' . $user_data['user_photo']))) {
                        File::delete(storage_path('app/public/profile/' . $user_data['user_photo']));
                    }
                }

                User::where('id', $user_data['id'])
                    ->delete();
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'error',
                        'title' => 'Delete User Failed',
                        'text' => 'This user can\'t be deleted!',
                    ]
                ];
            }
        }
        return response()->json($responses);
    }

    public function edit_user(Request $request)
    {
        $method = $request->query('fetch');
        if ($method == "show") {
            $data['user_data'] = User::where('id', $request->input('id_user'))->first();

            $responses = [
                'csrftoken' => csrf_token(),
                'html' => view('layouts.modal_layouts.user_edit', $data)->render()
            ];
        } elseif ($method == "edit") {
            if ($request->isMethod('post')) {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'messages' => [],
                    'success' => FALSE
                ];

                $user_data = User::where('id', $request->input('id_user'))->first();

                $validator = Validator::make(
                    $request->all(),
                    [
                        'name_user' => 'required|string|max:255',
                        'username_user' => ($user_data['username'] == $request->input('username_user') ? 'required' : 'required|unique:users,username'),
                        'password_user' => ($request->filled('password_user') ? 'required|min:8' : ''),
                        'role_user' => 'required|in:administrator,member',
                        'user_status' => 'required',
                        'user_photo' => 'image|mimetypes:image/jpg,image/jpeg,image/png|max:2048',
                    ]
                );

                if ($validator->fails()) {
                    $responses['messages'] = $validator->errors()->all();
                } else {
                    $responses = [
                        'csrftoken' => csrf_token(),
                        'messages' => '<div class="alert alert-success">Change user successfully</div>',
                        'success' => TRUE
                    ];

                    if ($request->hasFile('user_photo')) {
                        if ($user_data['user_photo'] != 'no-image') {
                            if (File::exists(storage_path('app/public/profile/' . $user_data['user_photo']))) {
                                File::delete(storage_path('app/public/profile/' . $user_data['user_photo']));
                            }
                        }

                        $gen_resultcode = Str::random(16) . time();
                        $storage_dir = storage_path('app/public/profile');
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

                    if ($request->filled('password_user')) {
                        $db_user->update(['password' => Hash::make($request->input('password_user'))]);
                    }
                    $db_user->update(
                        [
                            'name' => $request->input('fullname_user'),
                            'username' => $request->input('username_user'),
                            'role_access' => $request->input('role_user'),
                            'is_active' => $request->boolean('user_status'),
                            'user_photo' => $photo_file
                        ]
                    );
                }
            }
        }

        return response()->json($responses);
    }

    public function get_user()
    {
        return DataTables::of(User::get())
            ->addIndexColumn()
            ->editColumn('role_access', function ($data) {
                return $data->role_access == 'administrator' ? '<span class="badge bg-danger">Administrator</span>' : ($data->role_access == 'member' ? '<span class="badge bg-success">Member</span>' : '<span class="badge bg-secondary">Undefined</span>');
            })
            ->editColumn('is_active', function ($data) {
                return ($data->is_active == TRUE ? '<div class="badge bg-success">Active</div>' : ($data->is_active == FALSE ? '<div class="badge bg-danger">Deactive</div>' : '<span class="badge bg-secondary">Undefined</span>'));
            })
            ->editColumn('user_photo', function ($data) {
                return ($data->user_photo != 'no-image' ? '<img class="rounded-circle" src="' . route('user_img', ['name' => $data->user_photo]) . '"alt="Avatar" style="width: 65px;">' : '<img class="rounded-circle" src="' . asset('assets/img/no-avatar.png') . '"alt="Avatar" style="width: 65px;">');
            })
            ->editColumn('created_at', function ($data) {
                return Carbon::parse($data->created_at)->format('j F Y H:i:s');
            })
            ->addColumn('actions', function ($data) {
                return '<div class="btn-group">
                    <div class="btn btn-warning edit-user-data" data-user-id="' . $data->id . '" data-toggle="tooltip" title="Edit User Account"><span
                        class="bi bi-pencil"></span></div>
                    <div class="btn btn-danger delete-user-data" data-user-id="' . $data->id . '" data-toggle="tooltip" title="Delete User Account"><span
                        class="bi bi-trash"></span></div>
                </div>
            ';
            })
            ->rawColumns(['actions', 'role_access', 'user_photo', 'is_active'])
            ->make(true);
    }
}
