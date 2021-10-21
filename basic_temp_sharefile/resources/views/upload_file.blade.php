@extends('layouts.frontend_layout')

@section('title','Upload')

@section('content')
<div class="px-4 py-5 my-5">
    <h1 class="display-5 fw-bold mb-3 text-center"><span class="bi bi-file-earmark-arrow-up me-3"></span>Upload</h1>
    <div class="col-lg-8 mx-auto">
        <div class="card card-body">
            <div id="resumable-drop" class="dropzone-file-wrapper d-block">
                <div id="resumable-browse" class="dropzone-file-desc-wrapper">
                    <div class="dropzone-file-description">
                        <div class="bi bi-cloud-upload h1"></div>
                        <p class="text-truncate text-wrap">Drag an your file in here or click here to
                            browse files. [Max Files {{ $cat_filesize_readable }}]
                        </p>
                    </div>
                </div>
            </div>
            <div class="border p-2 mt-2 d-none list-uploader-files-all">
                <div class="btn-group uploader-btn-control p-2">
                </div>
                <ul class="list-group uploader-file-list">
                </ul>
            </div>
            <div class="my-2 upload-alert-info-data"></div>
        </div>
    </div>
</div>
@endsection

@section('js-content')
<script src="{{ asset('assets/vendor/resumable.js/resumable.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var fileUpload = $('#resumable-browse');
        var fileUploadDrop = $('#resumable-drop');

            var resumable = new Resumable({
                // Use chunk size that is smaller than your maximum limit due a resumable issue
                // https://github.com/23/resumable.js/issues/51
                chunkSize: 5 * 1024 * 1024, // 5MB
                simultaneousUploads: 3,
                testChunks: false,
                maxFiles: 1,
                throttleProgressCallbacks: 1,
                maxFiles: 10,
                maxFileSize: {{ $cv_to_kb_limitsize }},
                fileType: [],
                maxChunkRetries: 5,
                target: "{{ route('upload.upload_module') }}",
                generateUniqueIdentifier:function(file, event){
                    // Some confusion in different versions of Firefox
                    var relativePath = file.webkitRelativePath||file.relativePath||file.fileName||file.name;
                    var size = file.size;
                    var lastModified = file.lastModified;
                    return(lastModified + '-' + size + '-' + relativePath.replace(/[^0-9a-zA-Z_-]/img, ''));
                },
                headers: {
                    'Accept' : 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                maxFileSizeErrorCallback: function(file, errorCount) {
                    Swal.fire('Upload File Too Larger',`${file.fileName||file.name} is too large, please upload files less than ${formatBytes(resumable.getOpt('maxFileSize'))}`,'warning');
                },
                fileTypeErrorCallback: function(file, errorCount) {
                    Swal.fire('File Type Wrong',`${file.fileName||file.name} has type not allowed, please upload files of type ${resumable.getOpt('fileType')}`,'warning');
                },
                maxFilesErrorCallback: function (files, errorCount) {
                    var maxFiles = resumable.getOpt('maxFiles');
                    Swal.fire('Max Upload Files',`You can upload up to ${maxFiles} files ${(maxFiles === 1 ? '' : 's')} at a time.`,'warning');
                }
            });

            // Resumable.js isn't supported
            if (!resumable.support) {
               $("div.upload-alert-info-data").html('<div class="alert alert-danger text-center" role="alert"><span class="bi bi-exclamation-triangle me-1"></span>Upload module not supported on your browser.</div>').show();
            } else {
                // Show a place for dropping/selecting files
                fileUploadDrop.show();
                resumable.assignDrop(fileUpload[0]);
                resumable.assignBrowse(fileUploadDrop[0]);
                
                // Handle file add event
                resumable.on('fileAdded', function(file) {
                    $('div.list-uploader-files-all').addClass("d-block").removeClass("d-none");
                    resumable.upload();
                        $('ul.uploader-file-list').append(`
                        <li class="list-group-item uploader-${file.uniqueIdentifier}" data-uploader-id="${file.uniqueIdentifier}">
                            <div class="btn btn-danger btn-sm remove-file-uploader" data-uploader-id="${file.uniqueIdentifier}">
                                <span class="bi bi-trash"></span></div>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                    <div class="bi bi-file-earmark h1">
                                        </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                        <div class="fw-light">${file.fileName.replace("_", " ")} (${formatBytes(file.size)})</div>
                                        <div class="d-none d-sm-block my-2">
                                            <div class="progress">
                                            <div class="progress-bar progress-upload-file" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    <div class="text-end">
                                        <div class="badge bg-success indicator-upload-file">Ready To Upload</div>
                                    </div>
                                    </div>
                            </div>
                            <div class="uploader-file-info-container"></div>
                        </li>
                        `);
                });
                resumable.on('fileSuccess', function(file, message) {
                    response = JSON.parse(message)
                    // Reflect that the file upload has completed
                    if(resumable.isUploading() == false && file.isComplete() == true){
                        $('div.uploader-btn-control').html('<div class="btn btn-secondary btn-sm clear-file-uploader"><span class="bi bi-trash me-1"></span>Clear All</div>');
                        $('meta[name="csrf-token"').val(response.csrftoken);
                    }
                    $(`.uploader-${file.uniqueIdentifier} .uploader-file-info-container`).html(`
                    <div class="form-group mb-2">
                        <label class="form-label">Share Link</label>
                        <div class="input-group">
                            <div class="input-group-text"><span class="bi bi-link-45deg"></span></div>
                            <input type="text" class="form-control file-copy-link-val" value="${response.share_info.download_url}" readonly>
                            <button class="btn-clipboard btn btn-primary" data-clipboard-action="copy" data-clipboard-target=".uploader-${file.uniqueIdentifier} input.file-copy-link-val"
                                data-bs-toggle="tooltip" data-bs-original-title="Copy to clipboard"><span
                                    class="bi bi-clipboard me-1"></span>Copy</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Delete File</label>
                        <div class="input-group">
                            <div class="input-group-text"><span class="bi bi-key"></span></div>
                            <input type="text" class="form-control" value="${response.share_info.password_delete}" readonly>
                            </div>
                        <div class="small text-danger">Please save this password in safe area.</div>
                    </div>
                    `);
                    $(`.uploader-${file.uniqueIdentifier} .remove-file-uploader`).remove();
                    $(`.uploader-${file.uniqueIdentifier} .indicator-upload-file`).addClass("bg-success").removeClass("bg-danger bg-primary").html(`<span class="bi bi-check me-1"></span>Uploaded`);
                });
                resumable.on('fileError', function(file, message) {
                    Swal.fire('Upload File Interupted',`${file.fileName||file.name} is error while on upload progress`,'warning');
                    // Reflect that the file upload has resulted in error
                    $('div.uploader-btn-control').html('<div class="btn btn-primary btn-sm retry-file-uploader"><span class="bi bi-arrow-counterclockwise me-1"></span>Retry Upload</div>');
                    $(`.uploader-${file.uniqueIdentifier} .progress-upload-file`).addClass('bg-danger').html('Error Occurred');
                    $(`.uploader-${file.uniqueIdentifier} div.uploader-file-info-container`).html(`<span class="bi bi-exclamation-triangle me-1"></span>file could not be uploaded: ${message}`).show();
                    $(`.uploader-${file.uniqueIdentifier} .indicator-upload-file`).addClass("bg-danger").removeClass("bg-success bg-primary").html('<span class="bi bi-exclamation-triangle me-1"></span>Failed');
                });
                resumable.on('fileProgress', function(file,message) {
                    // Handle progress for both the file and the overall upload
                    $('div.uploader-btn-control').html('<div class="btn btn-danger abort-file-uploader"><span class="bi bi-stop me-1"></span>Cancel</div><div class="btn btn-warning pause-resume-file-uploader" data-uploader-state="upload"><span class="bi bi-pause me-1">Pause</span></div>');
                    $(`.uploader-${file.uniqueIdentifier} .progress-upload-file`).attr('aria-valuenow', Math.floor(file.progress() * 100)).css('width', `${Math.floor(file.progress() * 100)}%`);
                    $(`.uploader-${file.uniqueIdentifier} .indicator-upload-file`).addClass("bg-primary").removeClass("bg-success bg-danger").html(`Uploading ${Math.floor(file.progress() * 100)}%`);
                });
            }

            $(document).on('click', '.remove-file-uploader', function (e) {
                var file = resumable.getFromUniqueIdentifier($(e.currentTarget).data('uploader-id'));
                resumable.removeFile(file);
                $(e.currentTarget).parent().parent().empty();
            });

            $(document).on('click', '.abort-file-uploader', function (e) {
                resumable.cancel();
                $('div.list-uploader-files-all').addClass("d-none").removeClass("d-block");
                $('div.uploader-btn-control').empty();
                $('ul.uploader-file-list').empty();
            });
            
            $(document).on('click', '.clear-file-uploader', function (e) {
                $('div.list-uploader-files-all').addClass("d-none").removeClass("d-block");
                $('div.uploader-btn-control').empty();
                $('ul.uploader-file-list').empty();
            });

            $(document).on('click', '.retry-file-uploader', function (e) {
                resumable.upload();
                $("div.upload-alert-info-data").html('<div class="alert alert-success text-center" role="alert">Retry to upload started.</div>').show().delay(3000).fadeOut();
            });

            $(document).on('click', '.pause-resume-file-uploader', function (e) {
                var state_uploader = $(e.currentTarget).data('uploader-state');
                if(state_uploader == 'upload'){
                    resumable.pause();
                    $('.pause-resume-file-uploader').addClass("btn-success").removeClass("btn-warning").attr('data-uploader-state','pause').html('<span class="bi bi-play me-1"></span>Continue');
                }else if (state_uploader == 'pause'){
                    resumable.upload();
                    $('.pause-resume-file-uploader').addClass("btn-success").removeClass("btn-warning").attr('data-uploader-state','upload').html('<span class="bi bi-pause me-1"></span>Pause');
                }
            });

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals; const sizes=['Bytes','KB','MB','GB','TB','PB','EB','ZB','YB','YB+'];
            const i=Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function msgalert(sector, msg) {
            $(sector).show();
            $(sector).find('ul').children().remove();
            $(sector).html('<ul></ul>').addClass("alert alert-danger");
            $.each(msg, function(key, value) {
                $(sector).find("ul").append('<li>' + value + '</li>');
            });
        }

    });
</script>
@endsection