@extends('layouts.admin_layout')

@section('title','User Manage')

@section('content')
<div class="p-3">
    <div class="fw-light h4"><span class="bi bi-people me-1"></span>User Manage</div>
    <div class="card card-body">
        <div class="p-2 d-flex justify-content-between flex-wrap">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target=".add-user-manage"><span
                    class="bi bi-plus mr-1"></span>Add User</button>
            <button class="btn btn-primary user-data-refresh"><span
                    class="bi bi-arrow-repeat me-1"></span>Refresh</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped dataTable no-footer user-data-list" width="100%">
                <thead>
                    <tr role="row">
                        <th>#</th>
                        <th>Fullname</th>
                        <th>Username</th>
                        <th>Avatar</th>
                        <th>Role User</th>
                        <th>Active</th>
                        <th>Registered Date</th>
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
        $('.user-data-list').DataTable({
            ajax: {
                url: "{{ route('user_manage.data') }}",
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
                data: 'name',
                name: 'name'
            }, {
                data: 'username',
                name: 'username'
            }, {
                data: 'user_photo',
                name: 'user_photo'
            }, {
                data: 'role_access',
                name: 'role_access'
            }, {
                data: 'is_active',
                name: 'is_active'
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

        $('.add-user-account-form').on('submit', function(event) {
            event.preventDefault();
            var form = this;
            var formdata = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('user_manage.add') }}",
                data: formdata,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $(".btn-add-user").html("<span class='spinner-border spinner-border-sm fa-spin me-1'></span>Saving").attr("disabled", true);
                },
                success: function(data) {
                    if (data.success == false) {
                        msgalert("#user-info-data", data.messages);
                    } else {
                        $('.user-data-list').DataTable().ajax.reload();
                        if ($("#user-info-data").hasClass("alert alert-danger")) {
                            $("#user-info-data").removeClass("alert alert-danger");
                        }
                        $("#user-info-data").html(data.messages).show().delay(3000).fadeOut();
                        $('.add-user-manage').modal('hide');
                        form.reset();
                        $(".preview-img-add").empty();
                    }
                    $(".btn-add-user").on('.custom-modal-content').html("<span class='bi bi-person-plus me-1'></span>Save").attr("disabled", false);
                    $('meta[name="csrf-token"').val(data.csrftoken);
                },
                error: function() {
                    swal.fire("Add User Failed", "There have problem while adding user!", "error");
                }
            });

        });

        $(".user-data-list").on('click', '.delete-user-data', function(event) {
            var user_data = $(event.currentTarget).attr('data-user-id');
            if (user_data === null) return;
            var dataJson = {
                id_user: user_data
            };
            Swal.fire({
                title: 'Delete User',
                text: "This user will be erase from database!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('user_manage.delete') }}",
                        type: 'POST',
                        data: dataJson,
                        beforeSend: function() {
                            swal.fire({
                                title: "Deleting User",
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
                            swal.fire("Delete User Failed", "There have problem while deleting user!", "error");
                        }
                    });
                }
            });
            event.preventDefault();
        });

        $(".user-data-list").on('click', '.edit-user-data', function(event) {
            var user_data = $(event.currentTarget).attr('data-user-id');
            if (user_data === null) return;
            var dataJson = {
                id_user: user_data
            };
            $.ajax({
                url: "{{ route('user_manage.edit',['fetch' => 'show']) }}",
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
                    $('.edit-user-account-form').submit(function(e) {
                        e.preventDefault();
                        var form = this;
                        var formdata = new FormData(form);
                        formdata.append('id_user', user_data);
                        $.ajax({
                            url: "{{ route('user_manage.edit',['fetch' => 'edit']) }}",
                            type: 'POST',
                            data: formdata,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                $(".btn-edit-user").on('.custom-modal-content').html("<span class='spinner-border spinner-border-sm me-1'></span>Saving").attr("disabled", true);
                            },
                            success: function(data) {
                                $('meta[name="csrf-token"').val(data.csrftoken);
                                $('input[name=_token]').val(data.csrftoken);
                                if (data.success == false) {
                                    msgalert("#user-info-data-edit", data.messages);
                                } else {
                                    $('.user-data-list').DataTable().ajax.reload();
                                    if ($("#user-info-data-edit").hasClass("alert alert-danger")) {
                                        $("#user-info-data-edit").removeClass("alert alert-danger");
                                    }
                                    $("#user-info-data-edit").html(data.messages).show().delay(3000).fadeOut();
                                    $('.custom-modal-display').modal('hide');
                                    form.reset();
                                }
                                $(".btn-edit-user").on('.custom-modal-content').html("<span class='bi bi-pencil me-1'></span>Save").attr("disabled", false);
                            },
                            error: function() {
                                $(".btn-edit-user").on('.custom-modal-content').html("<span class='bi bi-pencil me-1'></span>Save").attr("disabled", false);
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

    document.addEventListener("DOMContentLoaded", function() {
        if (document.querySelector(".user-photo-input-add")) {
            document.querySelector(".user-photo-input-add").addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('div.preview-img-add').html(`<img class="img-thumbnail preview-mode-img" width="250"
                    src="${e.target.result}"></img>`);
                    }

                    reader.readAsDataURL(this.files[0]);
                }

            });

        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        $(".password-toggle button.btn-toggle-pass").on("click", function (event) {
            event.preventDefault();
            if ($(".password-toggle input").attr("type") == "text") {
                $(".password-toggle input").attr("type", "password");
                $(".password-toggle span").addClass("bi-eye-slash-fill").removeClass("bi-eye-fill");
            } else if ($(".password-toggle input").attr("type") == "password") {
                $(".password-toggle input").attr("type", "text");
                $(".password-toggle span").removeClass("bi-eye-slash-fill").addClass("bi-eye-fill");
            }
        });
    });

    document.querySelector(".user-data-refresh").addEventListener("click", function(e) {
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
        $('.user-data-list').DataTable().ajax.reload();
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