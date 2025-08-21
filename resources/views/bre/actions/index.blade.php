@extends('bre.layout')

@section('title', 'Rule Actions Management')
@section('page-title', 'Rule Actions')
@section('page-description', 'Manage Rule Actions')

@section('content')
<!-- Add Action Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-plus"></i> Add New Action</h5>
    </div>
    <div class="card-body">
        <form id="actionForm" method="POST" action="{{ route('bre.actions.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="partner_id" class="form-label">Partner <span class="text-danger">*</span></label>
                        <select class="form-select" id="partner_id" name="partner_id" required>
                            <option value="">Select Partner</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->partner_id }}">{{ $partner->nbfc_name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="partner_id_error"></div>
                    </div>
                </div>
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
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="action_type" class="form-label">Action Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="action_type" name="action_type" required>
                            <option value="">Select Action Type</option>
                            <option value="Approve">Approve</option>
                            <option value="Reject">Reject</option>
                            <option value="Hold">Hold for Review</option>
                            <option value="Conditional_Approval">Conditional Approval</option>
                            <option value="Set_Interest_Rate">Set Interest Rate</option>
                            <option value="Set_Credit_Limit">Set Credit Limit</option>
                            <option value="Require_Document">Require Additional Document</option>
                            <option value="Flag_Application">Flag Application</option>
                            <option value="Send_Notification">Send Notification</option>
                            <option value="Custom">Custom Action</option>
                        </select>
                        <div class="invalid-feedback" id="action_type_error"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="action_value" class="form-label">Action Value</label>
                        <input type="text" class="form-control" id="action_value" name="action_value">
                        <div class="form-text" id="actionValueHelp">Specify the value for this action (e.g., interest rate, document type, etc.)</div>
                        <div class="invalid-feedback" id="action_value_error"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="action_description" class="form-label">Description</label>
                        <textarea class="form-control" id="action_description" name="action_description" rows="3"></textarea>
                        <div class="form-text">Describe what this action does and when it should be executed</div>
                        <div class="invalid-feedback" id="action_description_error"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="execution_order" class="form-label">Execution Order</label>
                        <input type="number" class="form-control" id="execution_order" name="execution_order" value="1" min="1">
                        <div class="form-text">Order in which this action should be executed</div>
                        <div class="invalid-feedback" id="execution_order_error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="is_final" class="form-label">Is Final Action</label>
                        <select class="form-select" id="is_final" name="is_final">
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                        </select>
                        <div class="form-text">Should rule evaluation stop after this action?</div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-secondary me-2" onclick="resetForm()">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> <span id="submitText">Add Action</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Actions List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-list"></i> Rule Actions</h5>
        <div class="d-flex">
            <select class="form-select form-select-sm me-2" id="ruleFilter" style="width: 200px;">
                <option value="">All Rules</option>
            </select>
            <select class="form-select form-select-sm me-2" id="actionTypeFilter" style="width: 150px;">
                <option value="">All Types</option>
                <option value="Approve">Approve</option>
                <option value="Reject">Reject</option>
                <option value="Hold">Hold</option>
                <option value="Conditional_Approval">Conditional</option>
                <option value="Set_Interest_Rate">Set Rate</option>
                <option value="Set_Credit_Limit">Set Limit</option>
            </select>
            <input type="text" class="form-control form-control-sm me-2" id="searchInput" placeholder="Search actions..." style="width: 200px;">
            <button class="btn btn-outline-secondary btn-sm" onclick="refreshActions()">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="actionsTable">
                <thead>
                    <tr>
                        <th>Rule</th>
                        <th>Action Type</th>
                        <th>Value</th>
                        <th>Description</th>
                        <th>Order</th>
                        <th>Final</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="actionsTableBody">
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            Loading actions...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Action Modal -->
<div class="modal fade" id="editActionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editActionForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_action_id">
                    
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
                                <label for="edit_action_type" class="form-label">Action Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_action_type" name="action_type" required>
                                    <option value="">Select Action Type</option>
                                    <option value="Approve">Approve</option>
                                    <option value="Reject">Reject</option>
                                    <option value="Hold">Hold for Review</option>
                                    <option value="Conditional_Approval">Conditional Approval</option>
                                    <option value="Set_Interest_Rate">Set Interest Rate</option>
                                    <option value="Set_Credit_Limit">Set Credit Limit</option>
                                    <option value="Require_Document">Require Additional Document</option>
                                    <option value="Flag_Application">Flag Application</option>
                                    <option value="Send_Notification">Send Notification</option>
                                    <option value="Custom">Custom Action</option>
                                </select>
                                <div class="invalid-feedback" id="edit_action_type_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_action_value" class="form-label">Action Value</label>
                                <input type="text" class="form-control" id="edit_action_value" name="action_value">
                                <div class="invalid-feedback" id="edit_action_value_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_action_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_action_description" name="action_description" rows="3"></textarea>
                                <div class="invalid-feedback" id="edit_action_description_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_execution_order" class="form-label">Execution Order</label>
                                <input type="number" class="form-control" id="edit_execution_order" name="execution_order" min="1">
                                <div class="invalid-feedback" id="edit_execution_order_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_is_final" class="form-label">Is Final Action</label>
                                <select class="form-select" id="edit_is_final" name="is_final">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">
                        <i class="fas fa-save"></i> Update Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Delete Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this action?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone and may affect rule execution.
                </div>
                <div id="deleteActionInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Delete Action
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let editingActionId = null;
let deleteActionId = null;

$(document).ready(function() {
    loadRules();
    loadActions();
    
    // Action type change event
    $('#action_type, #edit_action_type').on('change', function() {
        const actionType = $(this).val();
        const isEdit = $(this).attr('id').includes('edit');
        const valueHelp = isEdit ? $('#editActionValueHelp') : $('#actionValueHelp');
        
        updateActionValueHelp(actionType, valueHelp);
    });
    
    // Search functionality
    $('#searchInput').on('keyup', function() {
        filterActions();
    });
    
    // Filter functionality
    $('#ruleFilter, #actionTypeFilter').on('change', function() {
        filterActions();
    });
});

// Add Action Form
$('#actionForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $('#submitBtn');
    const submitText = $('#submitText');
    const originalText = submitText.text();
    
    submitBtn.prop('disabled', true);
    submitText.text('Adding...');
    
    clearErrors();
    
    $.ajax({
        url: '{{ route("bre.actions.store") }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            showAlert('Action added successfully!', 'success');
            resetForm();
            loadActions();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayErrors(xhr.responseJSON.errors);
            } else {
                showAlert('Error adding action. Please try again.', 'danger');
            }
        },
        complete: function() {
            submitBtn.prop('disabled', false);
            submitText.text(originalText);
        }
    });
});

// Edit Action Form
$('#editActionForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $('#editSubmitBtn');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
    
    clearEditErrors();
    
    $.ajax({
        url: '/api/actions/' + editingActionId,
        method: 'PUT',
        data: $(this).serialize(),
        success: function(response) {
            showAlert('Action updated successfully!', 'success');
            $('#editActionModal').modal('hide');
            loadActions();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayEditErrors(xhr.responseJSON.errors);
            } else {
                showAlert('Error updating action. Please try again.', 'danger');
            }
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Action');
        }
    });
});

// Delete Action
$('#confirmDeleteBtn').on('click', function() {
    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
    
    $.ajax({
        url: '/api/actions/' + deleteActionId,
        method: 'DELETE',
        success: function(response) {
            showAlert('Action deleted successfully!', 'success');
            $('#deleteActionModal').modal('hide');
            loadActions();
        },
        error: function(xhr) {
            showAlert('Error deleting action. Please try again.', 'danger');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete Action');
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
                    $select.append(`<option value="${rule.id}">${rule.rule_name}</option>`);
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

function loadActions() {
    $.ajax({
        url: '/api/actions',
        method: 'GET',
        success: function(response) {
            renderActions(response.data || response);
        },
        error: function() {
            $('#actionsTableBody').html(`
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error loading actions
                    </td>
                </tr>
            `);
        }
    });
}

function renderActions(actions) {
    const tbody = $('#actionsTableBody');
    
    if (actions.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="7" class="text-center text-muted">
                    <i class="fas fa-play fa-2x mb-2"></i><br>
                    No actions found
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    actions.forEach(action => {
        const ruleName = action.rule?.rule_name || 'N/A';
        const actionTypeDisplay = getActionTypeDisplay(action.action_type);
        const finalBadge = action.is_final ? 
            '<span class="badge bg-danger">Final</span>' : 
            '<span class="badge bg-secondary">Continue</span>';
        
        html += `
            <tr data-rule-id="${action.rule_id}" data-action-type="${action.action_type}">
                <td>
                    <strong>${ruleName}</strong>
                    ${action.rule?.description ? `<br><small class="text-muted">${action.rule.description.substring(0, 30)}...</small>` : ''}
                </td>
                <td>
                    <span class="badge bg-${getActionTypeBadgeColor(action.action_type)}">${actionTypeDisplay}</span>
                </td>
                <td>
                    ${action.action_value ? `<code>${action.action_value}</code>` : '<span class="text-muted">N/A</span>'}
                </td>
                <td>
                    ${action.action_description ? action.action_description.substring(0, 50) + (action.action_description.length > 50 ? '...' : '') : '<span class="text-muted">No description</span>'}
                </td>
                <td>
                    <span class="badge bg-light text-dark">${action.execution_order || 1}</span>
                </td>
                <td>${finalBadge}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="editAction(${action.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteAction(${action.id}, '${action.action_type}')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.html(html);
}

function editAction(actionId) {
    $.ajax({
        url: '/api/actions/' + actionId,
        method: 'GET',
        success: function(action) {
            editingActionId = actionId;
            
            $('#edit_action_id').val(action.id);
            $('#edit_rule_id').val(action.rule_id);
            $('#edit_action_type').val(action.action_type);
            $('#edit_action_value').val(action.action_value);
            $('#edit_action_description').val(action.action_description);
            $('#edit_execution_order').val(action.execution_order);
            $('#edit_is_final').val(action.is_final ? '1' : '0');
            
            clearEditErrors();
            $('#editActionModal').modal('show');
        },
        error: function() {
            showAlert('Error loading action data. Please try again.', 'danger');
        }
    });
}

function deleteAction(actionId, actionType) {
    deleteActionId = actionId;
    $('#deleteActionInfo').html(`
        <strong>Action Type:</strong> ${getActionTypeDisplay(actionType)}<br>
        <strong>ID:</strong> ${actionId}
    `);
    $('#deleteActionModal').modal('show');
}

function filterActions() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const ruleFilter = $('#ruleFilter').val();
    const actionTypeFilter = $('#actionTypeFilter').val();
    const rows = $('#actionsTableBody tr');
    
    rows.each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        const rowRuleId = row.data('rule-id');
        const rowActionType = row.data('action-type');
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && !text.includes(searchTerm)) {
            showRow = false;
        }
        
        // Rule filter
        if (ruleFilter && rowRuleId != ruleFilter) {
            showRow = false;
        }
        
        // Action type filter
        if (actionTypeFilter && rowActionType !== actionTypeFilter) {
            showRow = false;
        }
        
        if (showRow) {
            row.show();
        } else {
            row.hide();
        }
    });
}

function getActionTypeDisplay(actionType) {
    const types = {
        'Approve': 'Approve',
        'Reject': 'Reject',
        'Hold': 'Hold',
        'Conditional_Approval': 'Conditional Approval',
        'Set_Interest_Rate': 'Set Interest Rate',
        'Set_Credit_Limit': 'Set Credit Limit',
        'Require_Document': 'Require Document',
        'Flag_Application': 'Flag Application',
        'Send_Notification': 'Send Notification',
        'Custom': 'Custom'
    };
    
    return types[actionType] || actionType;
}

function getActionTypeBadgeColor(actionType) {
    const colors = {
        'Approve': 'success',
        'Reject': 'danger',
        'Hold': 'warning',
        'Conditional_Approval': 'info',
        'Set_Interest_Rate': 'primary',
        'Set_Credit_Limit': 'primary',
        'Require_Document': 'secondary',
        'Flag_Application': 'warning',
        'Send_Notification': 'info',
        'Custom': 'dark'
    };
    
    return colors[actionType] || 'secondary';
}

function updateActionValueHelp(actionType, helpElement) {
    const helpTexts = {
        'Approve': 'No value needed for approval',
        'Reject': 'Rejection reason (optional)',
        'Hold': 'Hold reason or reviewer assignment',
        'Conditional_Approval': 'Conditions that must be met',
        'Set_Interest_Rate': 'Interest rate percentage (e.g., 12.5)',
        'Set_Credit_Limit': 'Credit limit amount (e.g., 500000)',
        'Require_Document': 'Document type required (e.g., "Income Certificate")',
        'Flag_Application': 'Flag reason or category',
        'Send_Notification': 'Notification message or template ID',
        'Custom': 'Custom action value or parameters'
    };
    
    if (helpElement && helpElement.length) {
        helpElement.text(helpTexts[actionType] || 'Specify the value for this action');
    }
}

function refreshActions() {
    loadActions();
    showAlert('Actions list refreshed!', 'info');
}

function resetForm() {
    $('#actionForm')[0].reset();
    clearErrors();
    $('#rule_id').focus();
}

function clearErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').empty();
}

function clearEditErrors() {
    $('#editActionModal .is-invalid').removeClass('is-invalid');
    $('#editActionModal .invalid-feedback').empty();
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
