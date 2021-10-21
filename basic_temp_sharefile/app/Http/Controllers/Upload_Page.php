<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use App\Models\Upload_Files;

use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class Upload_Page extends Controller
{
    public function __construct()
    {
        $this->name_folder_upload = env('UPLOAD_NAME_FOLDER', 'upload_storage');
    }

    public function upload()
    {
        /**
         * This readable is different because it get MB size
         * and then calculate it from MB to Bytes after that
         * categorizing to file size with suffix.
         */
        function readableMBytes($mbytes)
        {
            $mb_convert = $mbytes * (1024 ** 2);
            $i = floor(log($mb_convert) / log(1024));
            $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

            return sprintf('%.02F', $mb_convert / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
        }

        $data = [
            'cat_filesize_readable' => readableMBytes(env('UPLOAD_LIMIT_ALLOW', 1024)),
            'cv_to_kb_limitsize' => round(env('UPLOAD_LIMIT_ALLOW', 1024) * (1024 ** 3) / 1024),
        ];
        return view('upload_file', $data);
    }

    public function upload_files(Request $request)
    {
        /**
         * For now force max execution time to 5 minutes
         * due library chunk upload merger slow and triggered max execution time error.
         * Status: [Temporary Patch]
         */
        ini_set('max_execution_time', 300);

        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            return response()->json([
                'send' => FALSE,
                'status' => [
                    'state' => 'ERROR',
                    'message' => '<div class="alert alert-danger text-center alert-dismissible fade show" role="alert">Something wrong with your upload files.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                ],
                'csrftoken' => csrf_token()
            ]);
        }

        // receive the file
        $filesave = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($filesave->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            $file = $filesave->getFile(); // get file

            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.' . $extension, '', $file->getClientOriginalName()); //file name without extenstion
            $fileName .= '_' . sha1(time()) . '.' . $extension; // a unique file name

            $upload_data = [
                'uuid_file' => Str::uuid(),
                'hash_file' => json_encode([
                    'md5' => md5_file($file),
                    'sha_1' => sha1_file($file)
                ]),
                'size_file' => File::size($file),
                'password_delete_file' => Str::random(10),
                'unique_id_file' => Str::random(30)
            ];

            Upload_Files::create([
                'user_id' => (Auth::check() ? Auth::user()->id : NULL),
                'type_uploader' => (Auth::check() ? 'registered' : 'anonymous'),
                'delete_password' => Hash::make($upload_data['password_delete_file']),
                'name_file' => $file->getClientOriginalName(),
                'filename' => $fileName,
                'file_size' => $upload_data['size_file'],
                'file_extension' => (!$request->resumableType ? 'application/octet-stream' : $request->resumableType),
                'hash_file' => $upload_data['hash_file'],
                'unique_id_file' => $upload_data['unique_id_file'],
                'uuid_file' => $upload_data['uuid_file']
            ]);
            $file->move(storage_path("app/public/{$this->name_folder_upload}/"), $fileName);

            return response()->json([
                'send' => TRUE,
                'status' => [
                    'state' => 'COMPLETE'
                ],
                'share_info' => [
                    'password_delete' => $upload_data['password_delete_file'],
                    'download_url' => route('download.getfile', ['uuid_file' => $upload_data['uuid_file'], 'id_file' => $upload_data['unique_id_file']])
                ],
                'csrftoken' => csrf_token()
            ]);
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $filesave->handler();

        return response()->json([
            'send' => TRUE,
            'chunks' => [
                'total_chunk' => $request->resumableTotalChunks,
                'current_chunk' => $request->resumableChunkNumber,
                'chunk_size' => $request->resumableChunkSize
            ],
            'file_info' => [
                'file_identity' => $request->resumableIdentifier,
                'mimetype' => $request->resumableType
            ],
            'status' => [
                'state' => 'TRANSFER',
                'progress' => $handler->getPercentageDone()
            ],
        ]);
    }
}
