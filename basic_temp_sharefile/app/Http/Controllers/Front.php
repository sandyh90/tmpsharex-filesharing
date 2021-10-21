<?php

namespace App\Http\Controllers;

use App\Models\Upload_Files;

use Illuminate\Http\Request;

class Front extends Controller
{
    //
    public function home()
    {
        return view('home');
    }

    public function files_list()
    {
        $data = [
            'files_list' => Upload_Files::paginate(15)
        ];
        return view('fileslist', $data);
    }

    public function search_files(Request $request)
    {
        $search_data = Upload_Files::with('user_data')->where('name_file', 'LIKE', '%' . $request->get('search') . '%')->orwhereHas('user_data', function ($query) use ($request) {
            return $query->where('name', 'LIKE', "%" . $request->get('search') . "%");
        })->paginate(15);

        if (!$request->get('search') || empty($search_data)) {
            return redirect()->route('home')->with('public_all_msg', '<div class="alert alert-danger text-center my-2 alert-dismissible fade show" role="alert"><span class="bi bi-exclamation-triangle me-1"></span>Data Not Found.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        } else {
            return view('searchfiles', ['files_list' => $search_data, 'search_query' => $request->get('search')]);
        }
    }

    /**
     * Ajax Function
     */

    public function regen_captcha()
    {
        $responses = [
            'messages' => '<div class="alert alert-success my-2" role="alert">Captcha successfully regenerate</div>',
            'captcha' => captcha_img()
        ];
        return response()->json($responses);
    }
}
