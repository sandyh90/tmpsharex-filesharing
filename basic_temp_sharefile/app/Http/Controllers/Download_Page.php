<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

use App\Models\Upload_Files;

class Download_Page extends Controller
{
    public function __construct()
    {
        $this->name_folder_upload = env('UPLOAD_NAME_FOLDER', 'upload_storage');
    }

    public function get_file(Request $request, $uuid_file = NULL, $id_file = NULL)
    {
        $file_db = Upload_Files::with('user_data')->where(['uuid_file' => $request->uuid_file, 'unique_id_file' => $request->id_file])->first();

        if (!$file_db || is_null($uuid_file) || is_null($id_file)) {
            return redirect()->route('home')->with('public_all_msg', '<div class="alert alert-danger text-center my-2 alert-dismissible fade show" role="alert"><span class="bi bi-exclamation-triangle me-1"></span>File Not Found.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        } else {
            return view('download_file', ['file_data' => $file_db]);
        }
    }

    public function process_download(Request $request, $uuid_file = NULL, $id_file = NULL)
    {
        $file_db = Upload_Files::with('user_data')->where(['uuid_file' => $request->uuid_file, 'unique_id_file' => $request->id_file])->first();

        if (!$file_db || is_null($uuid_file) || is_null($id_file)) {
            return redirect()->route('home')->with('public_all_msg', '<div class="alert alert-danger text-center my-2 alert-dismissible fade show" role="alert"><span class="bi bi-exclamation-triangle me-1"></span>File Not Found.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        } else {
            $validator = Validator::make(
                $request->all(),
                [
                    'captcha' => 'required|captcha'
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('download_page_msg', '<div class="alert alert-danger text-center my-2 alert-dismissible fade show" role="alert">' . $validator->errors()->first() . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            } else {
                $valid_limit = now()->addHours(24);
                $data = [
                    'limittime_calc' => $valid_limit,
                    'file_data' => $file_db,
                    'gentemp_link' => URL::temporarySignedRoute('download.downloadfile', $valid_limit, ['filename' => $file_db['filename']])
                ];
                return view('download_process', $data);
            }
        }
    }

    public function download_file(Request $request, $filename = NULL)
    {
        if (!$request->hasValidSignature()) {
            return response()->json([
                'status' => 'FAILED',
                'msg' => 'Download file token not match.'
            ], 401);
        }
        return Storage::disk(config('filesystems.default'))->download("public/{$this->name_folder_upload}/" . $filename);
    }

    public function delete_file(Request $request, $uuid_file = NULL, $id_file = NULL)
    {
        $file_db = Upload_Files::with('user_data')->where(['uuid_file' => $request->uuid_file, 'unique_id_file' => $request->id_file])->first();

        if (!$file_db || is_null($uuid_file) || is_null($id_file)) {
            return redirect()->route('home')->with('public_all_msg', '<div class="alert alert-danger text-center my-2 alert-dismissible fade show" role="alert"><span class="bi bi-exclamation-triangle me-1"></span>File Not Found.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        } else {
            $validator = Validator::make(
                $request->all(),
                [
                    'delete_file_password' => 'required'
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('download_page_msg', '<div class="alert alert-danger text-center my-2 alert-dismissible fade show" role="alert">' . $validator->errors()->first() . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            } else {
                if (Hash::check($request->input('delete_file_password'), $file_db->delete_password)) {
                    if (File::exists(storage_path("app/public/{$this->name_folder_upload}/" . $file_db->filename))) {
                        File::delete(storage_path("app/public/{$this->name_folder_upload}/" . $file_db->filename));
                    }
                    Upload_Files::where(['uuid_file' => $file_db->uuid_file, 'unique_id_file' => $file_db->unique_id_file])->delete();
                    return redirect()->route('home')->with('public_all_msg', '<div class="alert alert-success my-2 text-center alert-dismissible fade show" role="alert">File successfully delete.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                } else {
                    return redirect()->back()->with('download_page_msg', '<div class="alert alert-danger my-2 text-center alert-dismissible fade show" role="alert">File delete password wrong.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                }
            }
        }
    }
}
