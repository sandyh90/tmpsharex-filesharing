@extends('layouts.frontend_layout')

@section('title','My Files')

@section('content')
<div class="px-4 py-5 my-5">
    <h1 class="display-5 fw-bold text-center"><span class="bi bi-folder me-3"></span>My Files</h1>
    <div class="row">
        <div class="col-lg-9">
            <div class="card card-body">
                <div class="table-responsive">
                    <table class="table table-striped dataTable no-footer myfiles-list" width="100%">
                        <thead>
                            <tr role="row">
                                <th>#</th>
                                <th>Name File</th>
                                <th>Mimetype</th>
                                <th>File Size</th>
                                <th>Uploaded Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card card-body">
                <div class="text-center align-items-center">
                    <img src="{{ Auth::user()->user_photo != 'no-image'? route('user_img', ['name' => Auth::user()->user_photo]) : Avatar::create(Auth::user()->name)->toBase64() }}"
                        class="rounded-circle" height="120">
                    <div class="my-2 h5">
                        {{ \Str::of(Auth::user()->name)->limit(13); }}
                    </div>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="fs-6 mb-2"><span class="bi bi-hdd me-1"></span>Your Storage</div>
                        <div class="progress">
                            <div class="progress-bar @if($filesize_storage_count > FileHelperCustom::mbconvert_filesize_calc(env('STORAGE_LIMIT_ACCOUNTS', 20480))) bg-danger @endif"
                                role="progressbar"
                                style="width: {{ floor($filesize_storage_count / FileHelperCustom::mbconvert_filesize_calc(env('STORAGE_LIMIT_ACCOUNTS', 20480)) * 100) }}%"
                                aria-valuenow="{{ floor($filesize_storage_count / FileHelperCustom::mbconvert_filesize_calc(env('STORAGE_LIMIT_ACCOUNTS', 20480)) * 100) }}"
                                aria-valuemin="0" aria-valuemax="100">@if($filesize_storage_count >
                                FileHelperCustom::mbconvert_filesize_calc(env('STORAGE_LIMIT_ACCOUNTS', 20480))) Full
                                @endif
                            </div>
                        </div>
                        <div class="text-end small text-muted">
                            {{ FileHelperCustom::filesize_formatted($filesize_storage_count). ' of
                            '.FileHelperCustom::filesize_formatted(FileHelperCustom::mbconvert_filesize_calc(env('STORAGE_LIMIT_ACCOUNTS',
                            20480))) }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="fs-6 mb-2"><span class="bi bi-files me-1"></span>Total Files</div>
                        <div class="text-muted">
                            {{ $file_storage_count }} Items
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-content')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.myfiles-list').DataTable({
            ajax: {
                url: "{{ route('myfiles.ajaxlist') }}",
                type: 'get',
                async: true,
                processing: true,
                serverSide: true,
                bDestroy: true
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            }, {
                data: 'name_file',
                name: 'name_file'
            }, {
                data: 'file_extension',
                name: 'file_extension'
            }, {
                data: 'file_size',
                name: 'file_size'
            }, {
                data: 'created_at',
                name: 'created_at'
            }, {
                data: 'actions',
                name: 'actions',
                orderable: true,
                searchable: true
            }, ]
        });

        $(".myfiles-list").on('click', '.delete-file', function(event) {
            var file_data = $(event.currentTarget).attr('data-file-id');
            if (file_data === null) return;
            var dataJson = {
                id_file: file_data
            };
            Swal.fire({
                title: 'Delete File',
                text: "This file will be erase and not available again for download!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('myfiles.ajaxdelete') }}",
                        type: 'POST',
                        data: dataJson,
                        beforeSend: function() {
                            swal.fire({
                                title: "Deleting File",
                                text: "Please wait",
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });
                            Swal.showLoading();
                        },
                        success: function(data) {
                            swal.fire({
                                icon: data.alert.icon,
                                title: data.alert.title,
                                text: data.alert.text,
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });
                            $('.myfiles-list').DataTable().ajax.reload();
                            $('meta[name="csrf-token"').val(data.csrftoken);
                        },
                        error: function(err) {
                            swal.fire("Delete File Failed", "There have problem while deleting file!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });

        $(".myfiles-list").on('click', '.edit-file', function(event) {
            var file_data = $(event.currentTarget).attr('data-file-id');
            if (file_data === null) return;
            var dataJson = {
                id_file: file_data
            };
            $.ajax({
                url: "{{ route('myfiles.ajaxedit',['fetch' => 'show']) }}",
                type: 'POST',
                data: dataJson,
                beforeSend: function() {
                    $('.custom-modal-display').modal('show');
                    $('.custom-modal-content').html("<span class='spinner-border my-2'></span>").addClass("text-center");
                },
                success: function(data) {
                    $('meta[name="csrf-token"').val(data.csrftoken);
                    $('input[name=_token]').val(data.csrftoken);
                    $('.custom-modal-content').html(data.html).removeClass("text-center");
                    $('.edit-file-data-form').submit(function(e) {
                        e.preventDefault();
                        var form = this;
                        var formdata = new FormData(form);
                        formdata.append('id_file', file_data);
                        $.ajax({
                            url: "{{ route('myfiles.ajaxedit',['fetch' => 'edit']) }}",
                            type: 'POST',
                            data: formdata,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                $(".btn-edit-file").on('.custom-modal-content').html("<span class='spinner-border spinner-border-sm me-1'></span>Saving").attr("disabled", true);
                            },
                            success: function(data) {
                                $('meta[name="csrf-token"').val(data.csrftoken);
                                $('input[name=_token]').val(data.csrftoken);
                                if (data.success == false) {
                                    msgalert("#file-info-data-edit", data.messages);
                                } else {
                                    $('.myfiles-list').DataTable().ajax.reload();
                                    if ($("#file-info-data-edit").hasClass("alert alert-danger")) {
                                        $("#file-info-data-edit").removeClass("alert alert-danger");
                                    }
                                    $("#file-info-data-edit").html(data.messages).show().delay(3000).fadeOut();
                                    $('.custom-modal-display').modal('hide');
                                    form.reset();
                                }
                                $(".btn-edit-file").on('.custom-modal-content').html("<span class='bi bi-pencil me-1'></span>Save").attr("disabled", false);
                            },
                            error: function() {
                                $(".btn-edit-file").on('.custom-modal-content').html("<span class='bi bi-pencil me-1'></span>Save").attr("disabled", false);
                            }
                        });

                    });
                },
                error: function(err) {
                    $('.custom-modal-content').html("<span class='bi bi-exclamation-triangle mr-1'></span>There have problem while processing data").addClass("text-center");
                }
            });
            event.preventDefault();
        });
    });

    function msgalert(sector, msg) {
        $(sector).show();
        $(sector).find('ul').children().remove();
        $(sector).html('<ul></ul>').addClass("alert alert-danger");
        $.each(msg, function(key, value) {
            $(sector).find("ul").append('<li>' + value + '</li>');
        });
    }
</script>
@endsection