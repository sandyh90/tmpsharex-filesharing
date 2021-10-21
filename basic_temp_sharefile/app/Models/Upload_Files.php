<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload_Files extends Model
{
    protected $table = 'upload_files_data';

    protected $fillable = [
        'user_id',
        'type_uploader',
        'delete_password',
        'name_file',
        'filename',
        'file_size',
        'file_extension',
        'hash_file',
        'unique_id_file',
        'uuid_file'
    ];

    public function user_data()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
