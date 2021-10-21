@extends('layouts.frontend_layout')

@section('title','Get Files')

@section('content')
<div class="px-4 py-5 my-5 text-center">
    <h1 class="display-5 fw-bold mb-3"><span class="bi bi-file-earmark-arrow-down me-3"></span>Get Files</h1>
    <div class="p-2 fs-4 text-truncate">{{ $file_data['name_file'] }}</div>
    <div class="col-lg-6 mx-auto">
        <div class="card card-body">
            <p class="text-success">Your download file will be available shortly in
                <span class="countdown-download fw-bold"></span>, or you can click button below
            </p>
            <p class="text-center">Link download only valid until: {{ \Carbon\Carbon::parse($limittime_calc)->format('j
                F Y H:i:s T') }}</p>
            <a href="{{ $gentemp_link }}" class="btn btn-primary btn-download-href"><span
                    class="bi bi-file-earmark-arrow-down me-1"></span>Download</a>
        </div>
    </div>
</div>
@endsection

@section('js-content')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var timeleft = 5;
        var downloadTimer = setInterval(function(){
            if(timeleft <= 0){ 
                clearInterval(downloadTimer);
                location.replace(document.querySelector("a.btn-download-href").href);
            }
            document.querySelector("span.countdown-download").innerHTML = timeleft;
            timeleft -=1;
        }, 1000);
    });
</script>
@endsection