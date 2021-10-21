<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

use App\Models\Upload_Files;

use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Helpers\FileHelper;

class FilesManage extends Controller
{
    public function __construct()
    {
        $this->name_folder_upload = env('UPLOAD_NAME_FOLDER', 'upload_storage');
    }

    public function manage_files()
    {
        return view('admin.files_manage');
    }

    /**
     * Ajax Function
     */

    public function get_files()
    {
        return DataTables::of(Upload_Files::get())
            ->addIndexColumn()
            ->editColumn('file_extension', function ($data) {
                $icon = (Str::contains($data->file_extension, 'video') ? 'bi bi-file-earmark-play' : (Str::contains($data->file_extension, 'audio') ? 'bi bi-file-earmark-music' : (Str::contains($data->file_extension, 'image') ? 'bi bi-file-earmark-image' : (Str::contains($data->file_extension, 'application') ? 'bi bi-file-earmark' : (Str::contains($data->file_extension, 'text') ? 'bi bi-file-earmark-text' : (Str::contains($data->file_extension, 'font') ? 'bi bi-file-earmark-font' : 'bi bi-file-earmark-binary'))))));
                return  "<span class=' $icon me-1'></span>$data->file_extension" . ' (' . FileHelper::get_mime_type($data->file_extension) . ')';
            })
            ->editColumn('file_size', function ($data) {
                return FileHelper::filesize_formatted($data->file_size);
            })
            ->editColumn('created_at', function ($data) {
                return Carbon::parse($data->created_at)->format('j F Y H:i:s');
            })
            ->addColumn('user_uploader', function ($data) {
                return ($data->type_uploader == 'registered' ? $data->user_data->name : ($data->type_uploader == 'anonymous' ? 'Anonymous' : 'Undefined'));
            })
            ->addColumn('actions', function ($data) {
                return '<div class="btn-group">
                    <a class="btn btn-primary" href="' . route('download.getfile', ['uuid_file' => $data->uuid_file, 'id_file' => $data->unique_id_file]) . '"  data-toggle="tooltip" title="Goto Download Page" target="_blank"><span
                        class="bi bi-file-earmark-arrow-down"></span></a>
                    <div class="btn btn-danger delete-file" data-file-id="' . $data->id . '" data-toggle="tooltip" title="Delete File"><span
                        class="bi bi-file-earmark-easel"></span></div>
                </div>
            ';
            })
            ->rawColumns(['actions', 'file_extension', 'created_at', 'file_size'])
            ->make(true);
    }

    public function delete_files(Request $request)
    {
        if ($request->isMethod('post')) {
            $file_data = Upload_Files::where('id', $request->input('id_file'))->first();

            if ($file_data) {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => TRUE,
                    'alert' => [
                        'icon' => 'success',
                        'title' => 'Delete File Success',
                        'text' => 'File has been deleted forever!',
                    ]
                ];

                if ($file_data['filename'] != NULL) {
                    if (File::exists(storage_path("app/public/{$this->name_folder_upload}/" . $file_data['filename']))) {
                        File::delete(storage_path("app/public/{$this->name_folder_upload}/" . $file_data['filename']));
                    }
                }

                Upload_Files::where('id', $file_data['id'])->delete();
            } else {
                $responses = [
                    'csrftoken' => csrf_token(),
                    'success' => FALSE,
                    'alert' => [
                        'icon' => 'error',
                        'title' => 'Delete File Failed',
                        'text' => 'File failed to delete!',
                    ]
                ];
            }
        }
        return response()->json($responses);
    }
}
