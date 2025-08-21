@extends('bre.layout')

@section('title', 'BRE Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Business Rules Engine Management Overview')

@section('content')

<!-- Step Flow Indicator -->
<div class="step-indicator">
    <div class="step {{ $stats['partners'] > 0 ? 'completed' : 'active' }}">
        <i class="fas fa-building"></i><br>
        <small>1. Partners</small>
    </div>
    <div class="step {{ $stats['products'] > 0 ? 'completed' : ($stats['partners'] > 0 ? 'active' : '') }}">
        <i class="fas fa-box"></i><br>
        <small>2. Products</small>
    </div>
    <div class="step {{ \App\Models\Variable::count() > 0 ? 'completed' : ($stats['products'] > 0 ? 'active' : '') }}">
        <i class="fas fa-tags"></i><br>
        <small>3. Variables</small>
    </div>
    <div class="step {{ $stats['rules'] > 0 ? 'completed' : (\App\Models\Variable::count() > 0 ? 'active' : '') }}">
        <i class="fas fa-list-alt"></i><br>
        <small>4. Rules</small>
    </div>
    <div class="step {{ \App\Models\RuleCondition::count() > 0 ? 'completed' : ($stats['rules'] > 0 ? 'active' : '') }}">
        <i class="fas fa-filter"></i><br>
        <small>5. Conditions</small>
    </div>
    <div class="step {{ \App\Models\Action::count() > 0 ? 'completed' : (\App\Models\RuleCondition::count() > 0 ? 'active' : '') }}">
        <i class="fas fa-play"></i><br>
        <small>6. Actions</small>
    </div>
    <div class="step {{ $stats['applications'] > 0 ? 'completed' : (\App\Models\Action::count() > 0 ? 'active' : '') }}">
        <i class="fas fa-calculator"></i><br>
        <small>7. Evaluate</small>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Partners</h5>
                        <h3>{{ $stats['partners'] }}</h3>
                    </div>
                    <i class="fas fa-building fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Products</h5>
                        <h3>{{ $stats['products'] }}</h3>
                    </div>
                    <i class="fas fa-box fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Rules</h5>
                        <h3>{{ $stats['rules'] }}</h3>
                        <small>{{ $stats['active_rules'] }} active</small>
                    </div>
                    <i class="fas fa-list-alt fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Applications</h5>
                        <h3>{{ $stats['applications'] }}</h3>
                    </div>
                    <i class="fas fa-file-alt fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions and Recent Activity -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-rocket"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('bre.partners.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Partner
                    </a>
                    <a href="{{ route('bre.products.index') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                    <a href="{{ route('bre.rules.index') }}" class="btn btn-warning">
                        <i class="fas fa-plus"></i> Create New Rule
                    </a>
                    <a href="{{ route('bre.evaluate.index') }}" class="btn btn-info">
                        <i class="fas fa-calculator"></i> Evaluate Application
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Recent Applications</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_applications']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_applications'] as $app)
                                <tr>
                                    <td>{{ $app->customer_name }}</td>
                                    <td>{{ $app->product->product_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($app->status === 'Approve')
                                            <span class="badge bg-success">{{ $app->status }}</span>
                                        @elseif($app->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning">{{ $app->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $app->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                        <p>No applications yet</p>
                        <a href="{{ route('bre.evaluate.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Evaluate First Application
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cog"></i> System Status</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Database Connected</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-{{ $stats['partners'] > 0 ? 'check-circle text-success' : 'exclamation-circle text-warning' }} me-2"></i>
                            <span>Partners {{ $stats['partners'] > 0 ? 'Configured' : 'Pending' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-{{ $stats['rules'] > 0 ? 'check-circle text-success' : 'exclamation-circle text-warning' }} me-2"></i>
                            <span>Rules {{ $stats['rules'] > 0 ? 'Configured' : 'Pending' }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-{{ $stats['active_rules'] > 0 ? 'check-circle text-success' : 'exclamation-circle text-warning' }} me-2"></i>
                            <span>Active Rules {{ $stats['active_rules'] > 0 ? 'Available' : 'None' }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>API Endpoints Active</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Rule Engine Ready</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh stats every 30 seconds
    setInterval(function() {
        // Only refresh if we're still on the dashboard
        if (window.location.pathname === '{{ route('bre.dashboard') }}') {
            location.reload();
        }
    }, 30000);
</script>
@endpush
