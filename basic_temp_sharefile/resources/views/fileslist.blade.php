@extends('layouts.frontend_layout')

@section('title','Files List')

@section('content')
<div class="px-4 py-5 my-5">
    <h1 class="display-5 fw-bold text-center"><span class="bi bi-folder me-3"></span>Files List</h1>
    <div class="col-lg-10 mx-auto">
        <div class="card card-body">
            <div class="my-2 fs-5">
                Total Files: {{ $files_list->total() }}
            </div>
            @if ($files_list->count() > 0)
            <div class="row row-cols-1 row-cols-md-3 g-4">
                @foreach ($files_list as $files)
                <div class="col">
                    <div class="card card-body text-center">
                        <h1 class="card-title"><span
                                class="{{ (Str::contains($files->file_extension, 'video') ? 'bi bi-file-earmark-play' : (Str::contains($files->file_extension, 'audio') ? 'bi bi-file-earmark-music' : (Str::contains($files->file_extension, 'image') ? 'bi bi-file-earmark-image' : (Str::contains($files->file_extension, 'application') ? 'bi bi-file-earmark' : (Str::contains($files->file_extension, 'text') ? 'bi bi-file-earmark-text' : (Str::contains($files->file_extension, 'font') ? 'bi bi-file-earmark-font' : 'bi bi-file-earmark-binary')))))) }}"></span>
                        </h1>
                        <div class="fw-light text-truncate"><span class="bi bi-type fs-4 me-1"></span>{{
                            $files->name_file }}
                        </div>
                        <div class="fw-light text-truncate"><span class="bi bi-hdd fs-4 me-1"></span>{{
                            FileHelperCustom::filesize_formatted($files->file_size) }}
                        </div>
                        <div class="fw-light"><span class="bi bi-clock fs-4 me-1"></span>{{
                            \Carbon\Carbon::parse($files->created_at)->format('j F Y H:i:s'); }}
                        </div>
                        <div class="fw-light text-truncate"><span class="bi bi-person fs-4 me-1"></span>{{
                            $files->type_uploader == 'registered' ? $files->user_data->name :
                            ($files->type_uploader == 'anonymous' ? 'Anonymous' : 'Undefined')}}
                        </div>
                        <a class="btn btn-primary"
                            href="{{ route('download.getfile', ['uuid_file' => $files->uuid_file, 'id_file' => $files->unique_id_file]) }}"
                            target="_blank"><span class="bi bi-file-earmark-arrow-down me-1"></span>Download</a>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center my-2">
                {{ $files_list->links() }}
            </div>
            @else
            <h3 class="text-center"><span class="bi bi-exclamation-triangle me-1"></span>No Files Available</h3>
            @endif
        </div>
    </div>
</div>
@endsection