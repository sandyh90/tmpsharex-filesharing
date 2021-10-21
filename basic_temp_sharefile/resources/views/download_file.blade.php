@extends('layouts.frontend_layout')

@section('title','Download Files')

@section('content')
<div class="px-4 py-5 my-5 text-center">
    <h1 class="display-5 fw-bold mb-3"><span class="bi bi-file-earmark-arrow-down me-3"></span>Download Files</h1>
    <div class="p-2 fs-4 text-truncate">{{ $file_data['name_file'] }}</div>
    <div class="col-lg-6 mx-auto">
        <div class="card card-body">
            <div class="d-sm-flex justify-content-between flex-wrap d-sm-inline-block sm-text-center py-3">
                <div class="d-sm-flex justify-content-left">
                    <img class="rounded-circle"
                        src="{{ $file_data['type_uploader'] == 'registered'? $file_data['user_data']['user_photo'] != 'no-image'? route('user_img', ['name' => $file_data['user_data']['user_photo']]) : Avatar::create($file_data['user_data']['name'])->toBase64() :
                        ($file_data['type_uploader'] == 'anonymous' ? Avatar::create('Anonymous')->toBase64() : Avatar::create('Undefined')->toBase64()) }}"
                        height="35">
                    <div class="align-self-center ms-1 text-reset">{{
                        $file_data['type_uploader'] == 'registered' ? $file_data['user_data']['name'] :
                        ($file_data['type_uploader'] == 'anonymous' ? 'Anonymous' : 'Undefined')}}</div>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".share-file-link">
                    <span class="bi bi-share me-1"></span>Share
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th colspan="2">Info Files</th>
                    </tr>
                    <tr>
                        <th>Name File</th>
                        <td>{{ $file_data['name_file'] }}</td>
                    </tr>
                    <tr>
                        <th>Size File</th>
                        <td>{{ FileHelperCustom::filesize_formatted($file_data['file_size']) }}</td>
                    </tr>
                    <tr>
                        <th>Extension</th>
                        <td>{{ $file_data['file_extension']
                            .' ('.FileHelperCustom::get_mime_type($file_data['file_extension']).')' }}</td>
                    </tr>
                    <tr>
                        <th>Hash</th>
                        <td>
                            <table class="table table-bordered">
                                <tr>
                                    <th>MD5</th>
                                    <td>{{ json_decode($file_data['hash_file'])->md5 }}
                                </tr>
                                <tr>
                                    <th>SHA1</th>
                                    <td>{{ json_decode($file_data['hash_file'])->sha_1 }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            {!! Session::get('download_page_msg') !!}
            <form
                action="{{ route('download.processfile', ['uuid_file' => $file_data['uuid_file'], 'id_file' => $file_data['unique_id_file']]) }}"
                method="POST">
                @csrf
                <div class="form-group row my-2">
                    <label class="form-label">Captcha</label>
                    <div class=" col-md-6">
                        <div class="captcha-img d-inline">{!! captcha_img() !!}</div>
                        <button type="button" class="btn btn-danger reload-captcha-img">
                            <span class="bi bi-arrow-clockwise"></span>
                        </button>
                    </div>
                    <div class="col-md-6 my-2 my-md-0">
                        <div class="input-group">
                            <div class="input-group-text"><span class="bi bi-key"></span></div>
                            <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha"
                                name="captcha">
                        </div>
                    </div>
                    <div class="captcha-info-msg"></div>
                </div>
                <div class="p-3">
                    <button type="submit" class="btn btn-primary"><span
                            class="bi bi-file-earmark-arrow-down me-1"></span>Download Files</button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="collapse"
                        data-bs-target=".delete-file-download">
                        <span class="bi bi-trash me-1"></span>Delete Files
                    </button>
                </div>
            </form>
            <div class="collapse delete-file-download">
                <div class="card card-body">
                    <form
                        action="{{ route('download.deletefile', ['uuid_file' => $file_data['uuid_file'], 'id_file' => $file_data['unique_id_file']]) }}"
                        method="POST">
                        @csrf
                        <div class="form-group mb-2">
                            <label class="form-label">Delete Password</label>
                            <div class="input-group">
                                <div class="input-group-text"><span class="bi bi-key"></span></div>
                                <input type="password" class="form-control" name="delete_file_password"
                                    value="{{ old('delete_file_password') }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><span
                                class="bi bi-trash me-1"></span>Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('js-content')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.reload-captcha-img').click(function () {
            $.ajax({
                type: 'GET',
                url: "{{ route('home.regen_captcha') }}",
                success: function (data) {
                    $(".captcha-img").html(data.captcha);
                    $(".captcha-info-msg").html(data.messages).show().delay(1000).fadeOut();
                }
            });
        });
    });
</script>
@endsection

@section('modal-content')
<div class="modal fade share-file-link" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="bi bi-share me-1"></span>Share</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <div class="row row-cols-1 row-cols-md-5 g-4">
                        <div class="col">
                            <a class="btn shadow rounded-circle" href="
                                mailto:?subject={{ $file_data['name_file'] }}&body={{ route('download.getfile', ['uuid_file'=>
                                $file_data['uuid_file'], 'id_file' => $file_data['unique_id_file']]) }}"
                                target="_blank"><span class="fas fa-envelope fa-1x"></span></a>
                        </div>
                        <div class="col">
                            <a class="btn btn-primary shadow rounded-circle" href="https://facebook.com/sharer/sharer.php?u={{ route('download.getfile', ['uuid_file'=>
                                $file_data['uuid_file'], 'id_file' => $file_data['unique_id_file']]) }}"
                                target="_blank"><span class="fab fa-facebook fa-1x"></span></a>
                        </div>
                        <div class="col">
                            <a class="btn btn-info shadow rounded-circle text-white" href="https://twitter.com/intent/tweet/?text={{ $file_data['name_file'] }}&url={{ route('download.getfile', ['uuid_file'=>
                                $file_data['uuid_file'], 'id_file' => $file_data['unique_id_file']]) }}"
                                target="_blank"><span class="fab fa-twitter fa-1x"></span></a>
                        </div>
                        <div class="col">
                            <a class="btn btn-secondary shadow rounded-circle" href="https://telegram.me/share/url?url={{ route('download.getfile', ['uuid_file'=>
                                $file_data['uuid_file'], 'id_file' => $file_data['unique_id_file']]) }}"
                                target="_blank"><span class="fab fa-telegram fa-1x"></span></a>
                        </div>
                        <div class="col">
                            <a class="btn btn-success shadow rounded-circle" href="https://api.whatsapp.com/send?text={{ route('download.getfile', ['uuid_file'=>
                                $file_data['uuid_file'], 'id_file' => $file_data['unique_id_file']]) }}"
                                target="_blank"><span class="fab fa-whatsapp fa-1x"></span></a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control file-copy-link-val"
                            value="{{ route('download.getfile', ['uuid_file' => $file_data['uuid_file'], 'id_file' => $file_data['unique_id_file']]) }}"
                            readonly>
                        <button class="btn-clipboard btn btn-primary" data-clipboard-action="copy"
                            data-clipboard-target=".file-copy-link-val" data-bs-toggle="tooltip"
                            data-bs-original-title="Copy to clipboard"><span
                                class="bi bi-clipboard me-1"></span>Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection