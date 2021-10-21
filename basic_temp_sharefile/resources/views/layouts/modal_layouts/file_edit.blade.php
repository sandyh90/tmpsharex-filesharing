<div class="modal-header">
    <h5 class="modal-title"><span class="bi bi-pencil me-1"></span>Edit User
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="edit-file-data-form">
        @csrf
        <div class="form-group mb-2">
            <label class="form-label">File Name</label>
            <div class="input-group">
                <div class="input-group-text"><span class="bi bi-type"></span></div>
                <input id="name-file" type="text" class="form-control" name="name_file"
                    value="{{ $file_data['name_file'] }}">

            </div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label">Password Delete File</label>
            <div class="input-group password-toggle-edit">
                <div class="input-group-text"><span class="bi bi-lock"></span></div>
                <input id="file-delete-password" type="password" class="form-control" name="file_delete_password">
                <button type="button" class="input-group-text btn-toggle-pass-edit" data-bs-toggle="tooltip"
                    data-bs-original-title="Show Password"><span class="bi bi-eye-slash-fill"></span></button>
            </div>
        </div>
        <button class="btn btn-primary btn-edit-file" type="submit"><span class="bi bi-pencil me-1"></span>Save</button>
    </form>
    <div class="my-2" id="file-info-data-edit"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<script>
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