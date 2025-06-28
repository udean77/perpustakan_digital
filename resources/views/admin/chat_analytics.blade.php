@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Chat Analytics Dashboard</h4>
                        <div>
                            <a href="{{ route('admin.chat_analytics.export') }}" class="btn btn-success">
                                <i class="mdi mdi-download"></i> Export Data
                            </a>
                        </div>
                    </div>

                    <!-- Statistik Umum -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ number_format($stats['total_sessions']) }}</h3>
                                    <p class="mb-0">Total Sessions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ number_format($stats['total_messages']) }}</h3>
                                    <p class="mb-0">Total Messages</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ number_format($stats['active_sessions_today']) }}</h3>
                                    <p class="mb-0">Active Today</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ number_format($stats['avg_response_time'], 0) }}ms</h3>
                                    <p class="mb-0">Avg Response</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3>{{ number_format($stats['satisfaction_rate'], 1) }}%</h3>
                                    <p class="mb-0">Satisfaction</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $topUsers->count() }}</h3>
                                    <p class="mb-0">Active Users</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($stats['total_sessions'] > 0)
                        <!-- Content when there's data -->
                        <div class="alert alert-success">
                            <h5>Data Available!</h5>
                            <p>Chat analytics data is available and charts will be displayed here.</p>
                        </div>
                    @else
                        <!-- No Data Message -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-chat-outline" style="font-size: 4rem; color: #ccc;"></i>
                                        <h4 class="mt-3">Belum Ada Data Chat Analytics</h4>
                                        <p class="text-muted">Data akan muncul setelah ada aktivitas chat dari pengguna.</p>
                                        <div class="mt-3">
                                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                                <i class="mdi mdi-arrow-left"></i> Kembali ke Dashboard
                                            </a>
                                            <a href="{{ route('admin.test') }}" class="btn btn-info">
                                                <i class="mdi mdi-test-tube"></i> Test Admin Access
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 