<div class="modal-header">
    <h5 class="modal-title"><span class="bi bi-pencil me-1"></span>Edit User
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="edit-user-account-form">
        @csrf
        <div class="form-group mb-2">
            <label class="form-label">Fullname</label>
            <div class="input-group">
                <div class="input-group-text"><span class="bi bi-type"></span></div>
                <input id="fullname-user" type="text" class="form-control" name="fullname_user"
                    value="{{ $user_data['name'] }}">

            </div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label">Username</label>
            <div class="input-group">
                <div class="input-group-text"><span class="bi bi-person-circle"></span></div>
                <input id="username-user" type="text" class="form-control" name="username_user"
                    value="{{ $user_data['username'] }}">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Password</label>
                <div class="input-group password-toggle-edit">
                    <div class="input-group-text"><span class="bi bi-lock"></span></div>
                    <input id="password-user" type="password" class="form-control" name="password_user">
                    <button type="button" class="input-group-text btn-toggle-pass-edit" data-bs-toggle="tooltip"
                        data-bs-original-title="Show Password"><span class="bi bi-eye-slash-fill"></span></button>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Role Akun</label>
                <div class="input-group">
                    <div class="input-group-text"><span class="bi bi-award"></span></div>
                    <select class="form-select" id="role-user" name="role_user">
                        <option selected>Select Role</option>
                        <option value="administrator" {{ $user_data['role_access']=='administrator' ? 'selected' : ''
                            }}>
                            Administrator</option>
                        <option value="member" {{ $user_data['role_access']=='member' ? 'selected' : '' }}>Member
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label">Active Account</label>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="active-user-edit" name="active_user" {{
                    $user_data['is_active']==TRUE ? 'checked' : '' }}>
                <label class="form-check-label" for="active-user-edit">Active Account</label>
            </div>
        </div>
        <div class="form-group row mb-2">
            <div class="col-sm-3">
                <img src="{{ $user_data['user_photo'] != 'no-image'? route('user_img',['name' => $user_data['user_photo']]) : Avatar::create($user_data['name'])->toBase64() }}"
                    class="rounded-circle preview-edit-mode-img" height="65">
            </div>
            <div class="col-sm-9">
                <input type="file" class="form-control user-photo-input-edit" id="user-photo" name="user_photo">
                <div class="small">Choose file. Max 2 MB</div>
            </div>
        </div>
        <button class="btn btn-primary btn-edit-user" type="submit"><span class="bi bi-pencil me-1"></span>Save</button>
    </form>
    <div class="my-2" id="user-info-data-edit"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<script>
    if (document.querySelector(".user-photo-input-edit")) {
        document.querySelector(".user-photo-input-edit").addEventListener('change', function() {
            if (this.files && this.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('img.preview-edit-mode-img').attr('src',e.target.result);
            }
            
            reader.readAsDataURL(this.files[0]);
            }
        });
    }

    $(".password-toggle-edit button.btn-toggle-pass-edit").on("click", function (event) {
            event.preventDefault();
        if ($(".password-toggle-edit input").attr("type") == "text") {
            $(".password-toggle-edit input").attr("type", "password");
            $(".password-toggle-edit span").addClass("bi-eye-slash-fill").removeClass("bi-eye-fill");
        } else if ($(".password-toggle-edit input").attr("type") == "password") {
            $(".password-toggle-edit input").attr("type", "text");
            $(".password-toggle-edit span").removeClass("bi-eye-slash-fill").addClass("bi-eye-fill");
        }
    });
</script>