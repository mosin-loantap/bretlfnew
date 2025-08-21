<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BRE Management System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .sidebar {
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            transition: all 0.3s;
            border-radius: 5px;
            margin-bottom: 2px;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
        }
        
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            color: white;
        }
        
        .stats-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
        }
        
        .stats-card.primary { border-left-color: #007bff; }
        .stats-card.success { border-left-color: #28a745; }
        .stats-card.warning { border-left-color: #ffc107; }
        .stats-card.info { border-left-color: #17a2b8; }
        
        .form-floating .form-select {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }
        
        .alert {
            border-radius: 10px;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
        }
        
        .page-title {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar p-3">
        <div class="text-center mb-4">
            <h4><i class="fas fa-cogs"></i> BRE Management</h4>
            <small class="text-light">Business Rules Engine</small>
        </div>
        
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bre.dashboard') ? 'active' : '' }}" 
                   href="{{ route('bre.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bre.partners.*') ? 'active' : '' }}" 
                   href="{{ route('bre.partners.index') }}">
                    <i class="fas fa-building me-2"></i> Partners
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bre.products.*') ? 'active' : '' }}" 
                   href="{{ route('bre.products.index') }}">
                    <i class="fas fa-box me-2"></i> Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bre.variables.*') ? 'active' : '' }}" 
                   href="{{ route('bre.variables.index') }}">
                    <i class="fas fa-tags me-2"></i> Variables
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bre.rules.*') ? 'active' : '' }}" 
                   href="{{ route('bre.rules.index') }}">
                    <i class="fas fa-list-alt me-2"></i> Rules
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bre.conditions.*') ? 'active' : '' }}" 
                   href="{{ route('bre.conditions.index') }}">
                    <i class="fas fa-filter me-2"></i> Conditions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bre.actions.*') ? 'active' : '' }}" 
                   href="{{ route('bre.actions.index') }}">
                    <i class="fas fa-play me-2"></i> Actions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bre.evaluate.*') ? 'active' : '' }}" 
                   href="{{ route('bre.evaluate.index') }}">
                    <i class="fas fa-calculator me-2"></i> Evaluate
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bre.applications.*') ? 'active' : '' }}" 
                   href="{{ route('bre.applications.index') }}">
                    <i class="fas fa-file-alt me-2"></i> Applications
                </a>
            </li>
        </ul>

        <div class="mt-auto pt-4">
            <div class="text-center">
                <small class="text-light">
                    API Base URL:<br>
                    <code class="text-warning">{{ url('/api') }}</code>
                </small>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Breadcrumb -->
        @if(!request()->routeIs('bre.dashboard'))
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('bre.dashboard') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                @yield('breadcrumb')
            </ol>
        </nav>
        @endif

        <!-- Page Title -->
        <div class="page-title">
            <h1>@yield('page-title')</h1>
            @if(View::hasSection('page-description'))
                <p class="text-muted">@yield('page-description')</p>
            @endif
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('evaluation_result'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h6><i class="fas fa-calculator me-2"></i>Evaluation Result:</h6>
                <pre>{{ json_encode(session('evaluation_result'), JSON_PRETTY_PRINT) }}</pre>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Content -->
        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Common JavaScript -->
    <script>
        // CSRF token setup for AJAX
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if(alert.querySelector('.btn-close')) {
                    alert.querySelector('.btn-close').click();
                }
            });
        }, 5000);
        
        // Form validation helper
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (form.checkValidity() === false) {
                form.classList.add('was-validated');
                return false;
            }
            return true;
        }
        
        // Loading state helper
        function setLoading(buttonId, loading = true) {
            const button = document.getElementById(buttonId);
            if (loading) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
            } else {
                button.disabled = false;
                button.innerHTML = button.getAttribute('data-original-text') || 'Submit';
            }
        }
        
        // API request helper
        async function apiRequest(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                    'Accept': 'application/json'
                }
            };
            
            const finalOptions = {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...(options.headers || {})
                }
            };
            
            try {
                const response = await fetch('/api' + url, finalOptions);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                console.error('API request failed:', error);
                throw error;
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>
