@extends('layouts.app')

{{-- Customize layout sections --}}

{{-- Content body: main page content --}}
@section('content_header')
<div class="row">
    <div class="col-md-6">
        <h1></h1>
    </div>

</div>
@endsection
@section('content_body')
   
<div class="content">
    <div class="container-fluid">
        
        <!-- Section Header -->
        <!-- <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="m-0 text-dark font-weight-bold">Forms Dashboard</h3>
                <p class="text-muted small mb-0">Overview of created forms, approvals, and pending requests</p>
            </div>
            <div>
                <a href="" class="btn btn-primary btn-sm rounded-pill shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Create New Form
                </a>
            </div>
        </div> -->

        <!-- Metric Stat Cards -->
        <div class="row">
            <!-- Total Forms Created -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-white border-left-primary shadow-sm rounded">
                    <div class="inner p-3">
                        <span class="text-uppercase text-muted font-weight-bold text-xs">Total Created Forms</span>
                        <h2 class="font-weight-bold my-1 text-dark">{{ $totalFormsCount ?? 0 }}</h2>
                        <p class="mb-0 text-xs text-success">
                            <i class="fas fa-arrow-up mr-1"></i>All system forms
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt text-primary opacity-25"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Approval -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-white border-left-warning shadow-sm rounded">
                    <div class="inner p-3">
                        <span class="text-uppercase text-muted font-weight-bold text-xs">Pending For Approvals</span>
                        <h2 class="font-weight-bold my-1 text-warning">{{ $pendingFormsCount ?? 0 }}</h2>
                        <p class="mb-0 text-xs text-muted">Awaiting approvers sign-off</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock text-warning opacity-25"></i>
                    </div>
                </div>
            </div>

            <!-- Approved Forms -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-white border-left-success shadow-sm rounded">
                    <div class="inner p-3">
                        <span class="text-uppercase text-muted font-weight-bold text-xs">Approved Forms</span>
                        <h2 class="font-weight-bold my-1 text-success">{{ $approvedFormsCount ?? 0 }}</h2>
                        <p class="mb-0 text-xs text-success">
                            <i class="fas fa-check-circle mr-1"></i>Completed requests
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-thumbs-up text-success opacity-25"></i>
                    </div>
                </div>
            </div>

            <!-- Cancelled Forms -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-white border-left-danger shadow-sm rounded">
                    <div class="inner p-3">
                        <span class="text-uppercase text-muted font-weight-bold text-xs">Rejected/Cancelled</span>
                        <h2 class="font-weight-bold my-1 text-danger">{{ $cancelledFormsCount ?? 0 }}</h2>
                        <p class="mb-0 text-xs text-muted">Rejected or cancelled</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Checked Forms -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-white border-left-success shadow-sm rounded">
                    <div class="inner p-3">
                        <span class="text-uppercase text-muted font-weight-bold text-xs">Checked</span>
                        <h2 class="font-weight-bold my-1 text-purple">{{ $checkedFormsCount ?? 0 }}</h2>
                        <p class="mb-0 text-xs text-purple">
                            <i class="fas fa-check mr-1"></i>Security checked forms
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-double text-purple opacity-25"></i>
                    </div>
                </div>
            </div>

            <!-- Received Forms -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-white border-left-success shadow-sm rounded">
                    <div class="inner p-3">
                        <span class="text-uppercase text-muted font-weight-bold text-xs">Acknowledged/Received</span>
                        <h2 class="font-weight-bold my-1 text-lime">{{ $receivedFormsCount ?? 0 }}</h2>
                        <p class="mb-0 text-xs text-muted">Received by receiver</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-gift text-lime opacity-25"></i>
                    </div>
                </div>
            </div>

            <!-- Liquidated Forms -->
            <div class="col-lg-4 col-6">
                <div class="small-box bg-white border-left-success shadow-sm rounded">
                    <div class="inner p-3">
                        <span class="text-uppercase text-muted font-weight-bold text-xs">Liquidated</span>
                        <h2 class="font-weight-bold my-1 text-lime">{{ $liquidatedFormsCount ?? 0 }}</h2>
                        <p class="mb-0 text-xs text-muted">Cash/PettyCash Advance Liquidate</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign text-lime opacity-25"></i>
                    </div>
                </div>
            </div>

            
        </div>


        <!-- Breakdown by Form Type Section -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title font-weight-bold m-0 text-dark">
                            <i class="fas fa-folder-open text-primary mr-2"></i> Created Forms Breakdown
                        </h5>
                        @role('superadmin')
                        <a href="{{ route('allforms.index') }}" class="btn btn-light btn-xs">View All</a>
                        @endrole
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="pl-4">Form Category</th>
                                        <th class="text-center">Prefix</th>
                                        <th class="text-center">Total Submitted</th>
                                        <th class="text-center">Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($formsBreakdown as $item)
                                        @php
                                            $percentage = $totalFormsCount > 0 ? round(($item->count / $totalFormsCount) * 100) : 0;
                                        @endphp
                                        <tr>
                                            <td class="pl-4 font-weight-bold text-dark">
                                                <i class="fas fa-file-invoice text-secondary mr-2"></i> {{ $item->name }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-light border">{{ $item->prefix }}</span>
                                            </td>
                                            <td class="text-center font-weight-bold">{{ $item->count }}</td>
                                            <td class="text-center" style="width: 250px;">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="mr-2 text-xs font-weight-bold">{{ $percentage }}%</span>
                                                    <div class="progress progress-xs w-50 m-0 rounded">
                                                        <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No form records available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
    
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

<style>
    .border-left-primary { border-left: 4px solid #007bff !important; }
    .border-left-warning { border-left: 4px solid #ffc107 !important; }
    .border-left-success { border-left: 4px solid #28a745 !important; }
    .border-left-danger  { border-left: 4px solid #dc3545 !important; }

    .small-box {
        position: relative;
        display: block;
        border-radius: 8px;
        transition: transform 0.2s ease-in-out;
    }
    .small-box:hover {
        transform: translateY(-3px);
    }
    .small-box .icon {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 2.2rem;
    }
    .opacity-25 { opacity: 0.25; }
    .text-xs { font-size: 0.75rem; }
</style>
@endpush

{{-- Push extra scripts --}}

@push('js')
@endpush
