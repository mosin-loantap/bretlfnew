@extends('bre.layout')

@section('title', 'Rule Conditions Management')
@section('page-title', 'Rule Conditions')
@section('page-description', 'Manage Rule Conditions')

@section('content')
<!-- Add Condition Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-plus"></i> Add New Condition</h5>
    </div>
    <div class="card-body">
        <form id="conditionForm" method="POST" action="{{ route('bre.conditions.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="rule_id" class="form-label">Rule <span class="text-danger">*</span></label>
                        <select class="form-select" id="rule_id" name="rule_id" required>
                            <option value="">Select Rule</option>
                            <!-- Will be populated via AJAX -->
                        </select>
                        <div class="invalid-feedback" id="rule_id_error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="variable_name" class="form-label">Variable <span class="text-danger">*</span></label>
                        <select class="form-select" id="variable_name" name="variable_name" required>
                            <option value="">Select Variable</option>
                            <!-- Will be populated via AJAX -->
                        </select>
                        <div class="invalid-feedback" id="variable_name_error"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="operator" class="form-label">Operator <span class="text-danger">*</span></label>
                        <select class="form-select" id="operator" name="operator" required>
                            <option value="">Select Operator</option>
                            <option value="equals">Equals (=)</option>
                            <option value="not_equals">Not Equals (≠)</option>
                            <option value="greater_than">Greater Than (>)</option>
                            <option value="greater_than_or_equal">Greater Than or Equal (≥)</option>
                            <option value="less_than">Less Than (<)</option>
                            <option value="less_than_or_equal">Less Than or Equal (≤)</option>
                            <option value="contains">Contains</option>
                            <option value="not_contains">Does Not Contain</option>
                            <option value="starts_with">Starts With</option>
                            <option value="ends_with">Ends With</option>
                            <option value="in">In List</option>
                            <option value="not_in">Not In List</option>
                            <option value="between">Between</option>
                            <option value="is_null">Is Null</option>
                            <option value="is_not_null">Is Not Null</option>
                        </select>
                        <div class="invalid-feedback" id="operator_error"></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="value" name="value" required>
                        <div class="form-text" id="valueHelp">Enter the comparison value</div>
                        <div class="invalid-feedback" id="value_error"></div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-secondary me-2" onclick="resetForm()">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> <span id="submitText">Add Condition</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Conditions List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-list"></i> Rule Conditions</h5>
        <div class="d-flex">
            <select class="form-select form-select-sm me-2" id="ruleFilter" style="width: 200px;">
                <option value="">All Rules</option>
            </select>
            <input type="text" class="form-control form-control-sm me-2" id="searchInput" placeholder="Search conditions..." style="width: 200px;">
            <button class="btn btn-outline-secondary btn-sm" onclick="refreshConditions()">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="conditionsTable">
                <thead>
                    <tr>
                        <th>Rule</th>
                        <th>Variable</th>
                        <th>Operator</th>
                        <th>Value</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="conditionsTableBody">
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            Loading conditions...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Condition Modal -->
<div class="modal fade" id="editConditionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Condition</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editConditionForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_condition_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_rule_id" class="form-label">Rule <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_rule_id" name="rule_id" required>
                                    <option value="">Select Rule</option>
                                </select>
                                <div class="invalid-feedback" id="edit_rule_id_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_variable_name" class="form-label">Variable <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_variable_name" name="variable_name" required>
                                    <option value="">Select Variable</option>
                                </select>
                                <div class="invalid-feedback" id="edit_variable_name_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_operator" class="form-label">Operator <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_operator" name="operator" required>
                                    <option value="">Select Operator</option>
                                    <option value="equals">Equals (=)</option>
                                    <option value="not_equals">Not Equals (≠)</option>
                                    <option value="greater_than">Greater Than (>)</option>
                                    <option value="greater_than_or_equal">Greater Than or Equal (≥)</option>
                                    <option value="less_than">Less Than (<)</option>
                                    <option value="less_than_or_equal">Less Than or Equal (≤)</option>
                                    <option value="contains">Contains</option>
                                    <option value="not_contains">Does Not Contain</option>
                                    <option value="starts_with">Starts With</option>
                                    <option value="ends_with">Ends With</option>
                                    <option value="in">In List</option>
                                    <option value="not_in">Not In List</option>
                                    <option value="between">Between</option>
                                    <option value="is_null">Is Null</option>
                                    <option value="is_not_null">Is Not Null</option>
                                </select>
                                <div class="invalid-feedback" id="edit_operator_error"></div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_value" class="form-label">Value <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_value" name="value" required>
                                <div class="invalid-feedback" id="edit_value_error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">
                        <i class="fas fa-save"></i> Update Condition
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConditionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Delete Condition</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this condition?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone and may affect rule evaluation.
                </div>
                <div id="deleteConditionInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Delete Condition
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let editingConditionId = null;
let deleteConditionId = null;

$(document).ready(function() {
    loadRules();
    loadVariables();
    loadConditions();
    
    // Search functionality
    $('#searchInput').on('keyup', function() {
        filterConditions();
    });
    
    // Filter functionality
    $('#ruleFilter').on('change', function() {
        filterConditions();
    });
});

// Add Condition Form
$('#conditionForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $('#submitBtn');
    const submitText = $('#submitText');
    const originalText = submitText.text();
    
    submitBtn.prop('disabled', true);
    submitText.text('Adding...');
    
    clearErrors();
    
    $.ajax({
        url: '{{ route("bre.conditions.store") }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            showAlert('Condition added successfully!', 'success');
            resetForm();
            loadConditions();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayErrors(xhr.responseJSON.errors);
            } else {
                showAlert('Error adding condition. Please try again.', 'danger');
            }
        },
        complete: function() {
            submitBtn.prop('disabled', false);
            submitText.text(originalText);
        }
    });
});

// Edit Condition Form
$('#editConditionForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $('#editSubmitBtn');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
    
    clearEditErrors();
    
    $.ajax({
        url: '/api/bre/conditions/' + editingConditionId,
        method: 'PUT',
        data: $(this).serialize(),
        success: function(response) {
            showAlert('Condition updated successfully!', 'success');
            $('#editConditionModal').modal('hide');
            loadConditions();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayEditErrors(xhr.responseJSON.errors);
            } else {
                showAlert('Error updating condition. Please try again.', 'danger');
            }
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Condition');
        }
    });
});

// Delete Condition
$('#confirmDeleteBtn').on('click', function() {
    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
    
    $.ajax({
        url: '/api/bre/conditions/' + deleteConditionId,
        method: 'DELETE',
        success: function(response) {
            showAlert('Condition deleted successfully!', 'success');
            $('#deleteConditionModal').modal('hide');
            loadConditions();
        },
        error: function(xhr) {
            showAlert('Error deleting condition. Please try again.', 'danger');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete Condition');
        }
    });
});

function loadRules() {
    $.ajax({
        url: '/api/rules',
        method: 'GET',
        success: function(response) {
            const rules = response.data || response;
            const selects = ['#rule_id', '#edit_rule_id', '#ruleFilter'];
            
            selects.forEach(selectId => {
                const $select = $(selectId);
                const currentValue = $select.val();
                
                if (selectId === '#ruleFilter') {
                    $select.html('<option value="">All Rules</option>');
                } else {
                    $select.html('<option value="">Select Rule</option>');
                }
                
                rules.forEach(rule => {
                    $select.append(`<option value="${rule.rule_id}">${rule.rule_name}</option>`);
                });
                
                if (currentValue) {
                    $select.val(currentValue);
                }
            });
        },
        error: function() {
            showAlert('Error loading rules', 'danger');
        }
    });
}

function loadVariables() {
    $.ajax({
        url: '/api/bre/variables',
        method: 'GET',
        success: function(response) {
            const variables = response.data || response;
            const selects = ['#variable_name', '#edit_variable_name'];
            
            selects.forEach(selectId => {
                const $select = $(selectId);
                const currentValue = $select.val();
                
                $select.html('<option value="">Select Variable</option>');
                
                variables.forEach(variable => {
                    $select.append(`<option value="${variable.variable_name}" data-type="${variable.data_type}">${variable.variable_name} (${variable.data_type})</option>`);
                });
                
                if (currentValue) {
                    $select.val(currentValue);
                }
            });
        },
        error: function() {
            showAlert('Error loading variables', 'danger');
        }
    });
}

function loadConditions() {
    $.ajax({
        url: '/api/bre/conditions',
        method: 'GET',
        success: function(response) {
            renderConditions(response.data || response);
        },
        error: function() {
            $('#conditionsTableBody').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error loading conditions
                    </td>
                </tr>
            `);
        }
    });
}

function renderConditions(conditions) {
    const tbody = $('#conditionsTableBody');
    
    if (conditions.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="5" class="text-center text-muted">
                    <i class="fas fa-filter fa-2x mb-2"></i><br>
                    No conditions found
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    conditions.forEach(condition => {
        const ruleName = condition.rule?.rule_name || 'N/A';
        const operatorDisplay = getOperatorDisplay(condition.operator);
        
        html += `
            <tr data-rule-id="${condition.rule_id}">
                <td>
                    <strong>${ruleName}</strong>
                </td>
                <td>
                    <code>${condition.variable_name}</code>
                </td>
                <td>
                    <span class="badge bg-info">${operatorDisplay}</span>
                </td>
                <td>
                    <strong>${condition.value}</strong>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="editCondition(${condition.condition_id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteCondition(${condition.condition_id}, '${condition.variable_name}')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.html(html);
}

function editCondition(conditionId) {
    $.ajax({
        url: '/api/bre/conditions/' + conditionId,
        method: 'GET',
        success: function(condition) {
            editingConditionId = conditionId;
            
            $('#edit_condition_id').val(condition.condition_id);
            $('#edit_rule_id').val(condition.rule_id);
            $('#edit_variable_name').val(condition.variable_name);
            $('#edit_operator').val(condition.operator);
            $('#edit_value').val(condition.value);
            
            clearEditErrors();
            $('#editConditionModal').modal('show');
        },
        error: function() {
            showAlert('Error loading condition data. Please try again.', 'danger');
        }
    });
}

function deleteCondition(conditionId, variableName) {
    deleteConditionId = conditionId;
    $('#deleteConditionInfo').html(`
        <strong>Variable:</strong> ${variableName}<br>
        <strong>ID:</strong> ${conditionId}
    `);
    $('#deleteConditionModal').modal('show');
}

function filterConditions() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const ruleFilter = $('#ruleFilter').val();
    const rows = $('#conditionsTableBody tr');
    
    rows.each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        const rowRuleId = row.data('rule-id');
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && !text.includes(searchTerm)) {
            showRow = false;
        }
        
        // Rule filter
        if (ruleFilter && rowRuleId != ruleFilter) {
            showRow = false;
        }
        
        if (showRow) {
            row.show();
        } else {
            row.hide();
        }
    });
}

function getOperatorDisplay(operator) {
    const operators = {
        'equals': '=',
        'not_equals': '≠',
        'greater_than': '>',
        'greater_than_or_equal': '≥',
        'less_than': '<',
        'less_than_or_equal': '≤',
        'contains': 'Contains',
        'not_contains': 'Not Contains',
        'starts_with': 'Starts With',
        'ends_with': 'Ends With',
        'in': 'In',
        'not_in': 'Not In',
        'between': 'Between',
        'is_null': 'Is Null',
        'is_not_null': 'Is Not Null'
    };
    
    return operators[operator] || operator;
}

function refreshConditions() {
    loadConditions();
    showAlert('Conditions list refreshed!', 'info');
}

function resetForm() {
    $('#conditionForm')[0].reset();
    clearErrors();
    $('#rule_id').focus();
}

function clearErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').empty();
}

function clearEditErrors() {
    $('#editConditionModal .is-invalid').removeClass('is-invalid');
    $('#editConditionModal .invalid-feedback').empty();
}

function displayErrors(errors) {
    Object.keys(errors).forEach(function(field) {
        $('#' + field).addClass('is-invalid');
        $('#' + field + '_error').text(errors[field][0]);
    });
}

function displayEditErrors(errors) {
    Object.keys(errors).forEach(function(field) {
        $('#edit_' + field).addClass('is-invalid');
        $('#edit_' + field + '_error').text(errors[field][0]);
    });
}

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at the top of content
    $('.content-wrapper').prepend(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush
