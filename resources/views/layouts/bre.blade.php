<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BRE Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            position: relative;
        }
        .step.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .step.completed {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }
        .loading {
            display: none;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 5px;
        }
        .success-message {
            color: #28a745;
            font-size: 0.875em;
            margin-top: 5px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar p-3">
        <div class="text-center mb-4">
            <h4><i class="fas fa-cogs"></i> BRE Management</h4>
            <small>Business Rules Engine</small>
        </div>
        
        <ul class="nav nav-pills flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('bre.dashboard') ? 'active' : '' }}" href="{{ route('bre.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('bre.partners*') ? 'active' : '' }}" href="{{ route('bre.partners.index') }}">
                    <i class="fas fa-building me-2"></i> Partners
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('bre.products*') ? 'active' : '' }}" href="{{ route('bre.products.index') }}">
                    <i class="fas fa-box me-2"></i> Products
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('bre.variables*') ? 'active' : '' }}" href="{{ route('bre.variables.index') }}">
                    <i class="fas fa-tags me-2"></i> Variables
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('bre.rules*') ? 'active' : '' }}" href="{{ route('bre.rules.index') }}">
                    <i class="fas fa-list-alt me-2"></i> Rules
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('bre.conditions*') ? 'active' : '' }}" href="{{ route('bre.conditions.index') }}">
                    <i class="fas fa-filter me-2"></i> Conditions
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('bre.actions*') ? 'active' : '' }}" href="{{ route('bre.actions.index') }}">
                    <i class="fas fa-play me-2"></i> Actions
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('bre.evaluate*') ? 'active' : '' }}" href="{{ route('bre.evaluate.index') }}">
                    <i class="fas fa-calculator me-2"></i> Evaluate
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('bre.applications*') ? 'active' : '' }}" href="{{ route('bre.applications.index') }}">
                    <i class="fas fa-file-alt me-2"></i> Applications
                </a>
            </li>
        </ul>

        <div class="mt-auto pt-4">
            <div class="text-center">
                <small>API Base URL: <br><code>{{ url('/api') }}</code></small>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global configuration
        const API_BASE_URL = '{{ url("/api") }}';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Set up CSRF token for all AJAX requests
        fetch.defaults = {
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        };

        // Helper function for API requests
        async function apiRequest(endpoint, options = {}) {
            try {
                const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    ...options
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json();
            } catch (error) {
                console.error('API Request failed:', error);
                showAlert('API request failed: ' + error.message, 'danger');
                throw error;
            }
        }

        // Show alert messages
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-info-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const mainContent = document.querySelector('.main-content');
            mainContent.insertBefore(alertDiv, mainContent.firstChild);

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Clear form helper
        function clearForm(formId) {
            document.getElementById(formId).reset();
            // Clear error messages
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        }
    </script>
    @stack('scripts')
</body>
</html>
