<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Upload_Files;

class Dashboard extends Controller
{
    public function dashboard()
    {
        $data = [
            'users_count' => User::count(),
            'files_count' => [
                'base_count' => Upload_Files::count(),
                'filesize_count' => Upload_Files::sum('file_size')
            ]
        ];
        return view('admin.dashboard', $data);
    }
}
