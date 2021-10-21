@extends('layouts.admin_layout')

@section('title','Files Manage')

@section('content')
<div class="p-3">
    <div class="fw-light h4"><span class="bi bi-files me-1"></span>Files Manage</div>
    <div class="card card-body">
        <div class="p-2 d-flex justify-content-between flex-wrap">
            <button class="btn btn-primary files-data-refresh"><span
                    class="bi bi-arrow-repeat me-1"></span>Refresh</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped dataTable no-footer files-data-list" width="100%">
                <thead>
                    <tr role="row">
                        <th>#</th>
                        <th>Name File</th>
                        <th>Mimetype</th>
                        <th>File Size</th>
                        <th>Uploaded By</th>
                        <th>Uploaded Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js-content')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.files-data-list').DataTable({
            ajax: {
                url: "{{ route('files_manage.data') }}",
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
                data: 'user_uploader',
                name: 'user_uploader'
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

        $(".files-data-list").on('click', '.delete-file', function(event) {
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
                        url: "{{ route('files_manage.delete') }}",
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
                            $('.user-data-list').DataTable().ajax.reload();
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

    });

    document.querySelector(".files-data-refresh").addEventListener("click", function(e) {
        e.preventDefault();
        swal.fire({
            title: "Refresh Table",
            text: "Please wait",
            showConfirmButton: false,
            allowOutsideClick: false,
            timer: 800,
            timerProgressBar: true
        });
        Swal.showLoading();
        $('.files-data-list').DataTable().ajax.reload();
    });

</script>
@endsection

@section('modal-content')
<div class="modal fade add-user-manage" data-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="bi bi-person-plus me-1"></span>Add User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="add-user-account-form">
                    @csrf
                    <div class="form-group mb-2">
                        <label class="form-label">Fullname</label>
                        <div class="input-group">
                            <div class="input-group-text"><span class="bi bi-type"></span></div>
                            <input id="fullname-user" type="text" class="form-control" name="fullname_user">

                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <div class="input-group-text"><span class="bi bi-person-circle"></span></div>
                            <input id="username-user" type="text" class="form-control" name="username_user">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group password-toggle">
                                <div class="input-group-text"><span class="bi bi-lock"></span></div>
                                <input id="password-user" type="password" class="form-control" name="password_user">
                                <button type="button" class="input-group-text btn-toggle-pass" data-bs-toggle="tooltip"
                                    data-bs-original-title="Show Password"><span
                                        class="bi bi-eye-slash-fill"></span></button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role Akun</label>
                            <div class="input-group">
                                <div class="input-group-text"><span class="bi bi-award"></span></div>
                                <select class="form-select" id="role-user" name="role_user">
                                    <option selected>Select Role</option>
                                    <option value="administrator">Administrator</option>
                                    <option value="member">Member</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Active Account</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active-user-add" name="active_user">
                            <label class="form-check-label" for="active-user-add">Active Account</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Photo Profile</label>
                        <input type="file" class="form-control user-photo-input-add" id="user-photo" name="user_photo">
                        <div class="small">Choose file. Max 2 MB</div>
                        <div class="preview-img-add my-2"></div>
                    </div>
                    <button class="btn btn-primary btn-add-user" type="submit"><span
                            class="bi bi-person-plus me-1"></span>Save</button>
                </form>
                <div class="my-2" id="user-info-data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection