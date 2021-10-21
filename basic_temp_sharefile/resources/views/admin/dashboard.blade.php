@extends('layouts.admin_layout')

@section('title','Dashboard')

@section('content')
<div class="p-3">
    <div class="fw-light h4"><span class="bi bi-speedometer2 me-1"></span>Dashboard</div>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4 mx-auto">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-light text-primary text-uppercase mb-1">
                                Total Registered User</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $users_count }}</div>
                        </div>
                        <div class="col-auto">
                            <span class="bi bi-people fs-2 text-gray-300"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4 mx-auto">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-light text-success text-uppercase mb-1">
                                Total Uploaded Files</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $files_count['base_count'] }}</div>
                            <div class="fw-lighter">Filesize Total: {{
                                FileHelperCustom::filesize_formatted($files_count['filesize_count']) }}</div>
                        </div>
                        <div class="col-auto">
                            <span class="bi bi-files fs-2 text-gray-300"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection