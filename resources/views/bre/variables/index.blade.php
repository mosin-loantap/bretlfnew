@extends('bre.layout')

@section('title', 'Variables Management')
@section('page-title', 'Variables Management')
@section('page-description', 'Manage dynamic variables used in business rules')

@section('breadcrumb')
    <li class="breadcrumb-item active">Variables</li>
@endsection

@section('content')
<div class="row">
    <!-- Add New Variable Form -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="fas fa-plus"></i> Add New Variable</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bre.variables.store') }}" method="POST" id="variable-form">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="partner_id" class="form-label">Partner <span class="text-danger">*</span></label>
                        <select class="form-select @error('partner_id') is-invalid @enderror" 
                                id="partner_id" 
                                name="partner_id" 
                                required>
                            <option value="">Select Partner</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->partner_id }}" {{ old('partner_id') == $partner->partner_id ? 'selected' : '' }}>
                                    {{ $partner->nbfc_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('partner_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" 
                               class="form-control @error('variable_name') is-invalid @enderror" 
                               id="variable_name" 
                               name="variable_name" 
                               placeholder="Variable Name"
                               value="{{ old('variable_name') }}" 
                               required>
                        <label for="variable_name">Variable Name *</label>
                        @error('variable_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Examples: salary, age, credit_score, employment_type
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  placeholder="Description"
                                  style="height: 100px" 
                                  required>{{ old('description') }}</textarea>
                        <label for="description">Description *</label>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select @error('data_type') is-invalid @enderror" 
                                id="data_type" 
                                name="data_type" 
                                required>
                            <option value="">Select Data Type</option>
                            <option value="string" {{ old('data_type') == 'string' ? 'selected' : '' }}>String</option>
                            <option value="number" {{ old('data_type') == 'number' ? 'selected' : '' }}>Number</option>
                            <option value="boolean" {{ old('data_type') == 'boolean' ? 'selected' : '' }}>Boolean</option>
                            <option value="date" {{ old('data_type') == 'date' ? 'selected' : '' }}>Date</option>
                        </select>
                        <label for="data_type">Data Type *</label>
                        @error('data_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" 
                               class="form-control @error('source') is-invalid @enderror" 
                               id="source" 
                               name="source" 
                               placeholder="Data Source"
                               value="{{ old('source') }}" 
                               required>
                        <label for="source">Data Source *</label>
                        @error('source')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Examples: application_form, bureau_data, bank_statement
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-gradient btn-lg">
                            <i class="fas fa-save"></i> Save Variable
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i> Clear Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Variables List -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list"></i> Existing Variables</h5>
                <button class="btn btn-sm btn-outline-light" onclick="location.reload()">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
            <div class="card-body p-0">
                @if($variables->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Variable Name</th>
                                <th>Data Type</th>
                                <th>Source</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($variables as $variable)
                            <tr>
                                <td>
                                    <strong>{{ $variable->variable_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ \Str::limit($variable->description, 30) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($variable->data_type) }}</span>
                                </td>
                                <td>
                                    <small>{{ $variable->source }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewModal"
                                                onclick="viewVariable('{{ $variable->variable_id }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm"
                                                onclick="deleteVariable('{{ $variable->variable_id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Variables Found</h5>
                    <p class="text-muted">Create your first variable to get started.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Info Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6><i class="fas fa-info-circle"></i> About Variables</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h6>Data Types</h6>
                        <ul class="list-unstyled">
                            <li><span class="badge bg-secondary">String</span> - Text values</li>
                            <li><span class="badge bg-primary">Number</span> - Numeric values</li>
                            <li><span class="badge bg-success">Boolean</span> - True/False</li>
                            <li><span class="badge bg-warning">Date</span> - Date values</li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h6>Common Variables</h6>
                        <ul class="list-unstyled">
                            <li>• salary</li>
                            <li>• age</li>
                            <li>• credit_score</li>
                            <li>• employment_type</li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h6>Data Sources</h6>
                        <ul class="list-unstyled">
                            <li>• application_form</li>
                            <li>• bureau_data</li>
                            <li>• bank_statement</li>
                            <li>• internal_scoring</li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h6>Usage in Rules</h6>
                        <p class="small">Variables are used in rule conditions to evaluate applicant data against predefined criteria.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Variable Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="variableDetails">
                <!-- Variable details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewVariable(variableId) {
    // Find variable in the current list
    const variables = @json($variables);
    const variable = variables.find(v => v.variable_id === variableId);
    
    if (variable) {
        document.getElementById('variableDetails').innerHTML = `
            <div class="row">
                <div class="col-sm-4"><strong>Variable ID:</strong></div>
                <div class="col-sm-8"><code>${variable.variable_id}</code></div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-4"><strong>Variable Name:</strong></div>
                <div class="col-sm-8">${variable.variable_name}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-4"><strong>Description:</strong></div>
                <div class="col-sm-8">${variable.description}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-4"><strong>Data Type:</strong></div>
                <div class="col-sm-8"><span class="badge bg-info">${variable.data_type}</span></div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-4"><strong>Source:</strong></div>
                <div class="col-sm-8">${variable.source}</div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-4"><strong>Created:</strong></div>
                <div class="col-sm-8">${new Date(variable.created_at).toLocaleString()}</div>
            </div>
        `;
    }
}

function deleteVariable(variableId) {
    if (confirm('Are you sure you want to delete this variable? This action cannot be undone.')) {
        // In a real application, you would make an AJAX call to delete
        alert('Delete functionality would be implemented here with proper API call.');
    }
}

// Form validation
document.getElementById('variable-form').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    this.classList.add('was-validated');
});
</script>
@endpush
