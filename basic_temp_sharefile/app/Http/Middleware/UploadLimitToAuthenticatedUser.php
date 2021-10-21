<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Upload_Files;

use App\Helpers\FileHelper;

class UploadLimitToAuthenticatedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * Calculate all user file upload size with storage limit server for
         * authentication user only but bypass for unauthentication client. 
         */
        if (Auth::check()) {
            $limit_storage = [
                'client_total_filesize' => Upload_Files::where('user_id', Auth::user()->id)->sum('file_size'),
                'storage_limit_server' => FileHelper::mbconvert_filesize_calc(env('STORAGE_LIMIT_ACCOUNTS', 20480))
            ];
        }

        /**
         * Logic check if user logged in from ajax or no ajax.
         */
        if ($request->ajax() && Auth::check() && ($limit_storage['client_total_filesize'] > $limit_storage['storage_limit_server'])) {
            return response()->json(['status' => 'error', 'msg' => 'Your storage limit is exceded, please erase some files if you want to upload again!'], 403);
        } elseif (Auth::check() && ($limit_storage['client_total_filesize'] > $limit_storage['storage_limit_server'])) {
            return redirect()->route('home')->with('public_all_msg', '<div class="alert alert-danger text-center my-2 alert-dismissible fade show" role="alert"><span class="bi bi-exclamation-triangle me-1"></span>Your storage limit is exceeded, please erase some files if you want to upload again!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        } else {
            return $next($request);
        }
    }
}
