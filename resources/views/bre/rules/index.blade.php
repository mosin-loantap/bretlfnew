@extends('bre.layout')

@section('title', 'Rules Management')
@section('page-title', 'Rules')
@section('page-description', 'Manage Business Rules')

@section('content')
<!-- Add Rule Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-plus"></i> Add New Rule</h5>
    </div>
    <div class="card-body">
        <form id="ruleForm" method="POST" action="{{ route('bre.rules.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="partner_id" class="form-label">Partner <span class="text-danger">*</span></label>
                        <select class="form-select" id="partner_id" name="partner_id" required>
                            <option value="">Select Partner</option>
                            <!-- Will be populated via AJAX -->
                        </select>
                        <div class="invalid-feedback" id="partner_id_error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_id" name="product_id" required disabled>
                            <option value="">Select Product</option>
                        </select>
                        <div class="invalid-feedback" id="product_id_error"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="rule_name" class="form-label">Rule Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="rule_name" name="rule_name" required>
                        <div class="invalid-feedback" id="rule_name_error"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="priority" name="priority" min="1" max="100" value="10" required>
                        <div class="form-text">1 = Highest, 100 = Lowest</div>
                        <div class="invalid-feedback" id="priority_error"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Status</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="rule_type" class="form-label">Rule Type</label>
                        <select class="form-select" id="rule_type" name="rule_type">
                            <option value="eligibility" selected>Eligibility</option>
                            <option value="pricing">Pricing</option>
                            <option value="underwriting">Underwriting</option>
                            <option value="validation">Validation</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="effective_from" class="form-label">Effective From <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="effective_from" name="effective_from" value="{{ date('Y-m-d') }}" required>
                        <div class="invalid-feedback" id="effective_from_error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="effective_to" class="form-label">Effective To</label>
                        <input type="date" class="form-control" id="effective_to" name="effective_to">
                        <div class="form-text">Leave empty for no end date</div>
                        <div class="invalid-feedback" id="effective_to_error"></div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-secondary me-2" onclick="resetForm()">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> <span id="submitText">Add Rule</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rules List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-list"></i> Rules List</h5>
        <div class="d-flex">
            <select class="form-select form-select-sm me-2" id="partnerFilter" style="width: 150px;">
                <option value="">All Partners</option>
            </select>
            <select class="form-select form-select-sm me-2" id="statusFilter" style="width: 120px;">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            <input type="text" class="form-control form-control-sm me-2" id="searchInput" placeholder="Search rules..." style="width: 200px;">
            <button class="btn btn-outline-secondary btn-sm" onclick="refreshRules()">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="rulesTable">
                <thead>
                    <tr>
                        <th>Rule Name</th>
                        <th>Partner</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Conditions</th>
                        <th>Actions</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="rulesTableBody">
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            Loading rules...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Rule Modal -->
<div class="modal fade" id="editRuleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRuleForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_rule_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_partner_id" class="form-label">Partner <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_partner_id" name="partner_id" required>
                                    <option value="">Select Partner</option>
                                </select>
                                <div class="invalid-feedback" id="edit_partner_id_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_product_id" name="product_id" required>
                                    <option value="">Select Product</option>
                                </select>
                                <div class="invalid-feedback" id="edit_product_id_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_rule_name" class="form-label">Rule Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_rule_name" name="rule_name" required>
                                <div class="invalid-feedback" id="edit_rule_name_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_priority" name="priority" min="1" max="100" required>
                                <div class="invalid-feedback" id="edit_priority_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_is_active" class="form-label">Status</label>
                                <select class="form-select" id="edit_is_active" name="is_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_rule_type" class="form-label">Rule Type</label>
                                <select class="form-select" id="edit_rule_type" name="rule_type">
                                    <option value="eligibility">Eligibility</option>
                                    <option value="pricing">Pricing</option>
                                    <option value="underwriting">Underwriting</option>
                                    <option value="validation">Validation</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_effective_from" class="form-label">Effective From <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_effective_from" name="effective_from" required>
                                <div class="invalid-feedback" id="edit_effective_from_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_effective_to" class="form-label">Effective To</label>
                                <input type="date" class="form-control" id="edit_effective_to" name="effective_to">
                                <div class="invalid-feedback" id="edit_effective_to_error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">
                        <i class="fas fa-save"></i> Update Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rule Details Modal -->
<div class="modal fade" id="ruleDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye"></i> Rule Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="ruleDetailsBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <a href="#" class="btn btn-primary" id="manageConditionsBtn">
                    <i class="fas fa-filter"></i> Manage Conditions
                </a>
                <a href="#" class="btn btn-success" id="manageActionsBtn">
                    <i class="fas fa-play"></i> Manage Actions
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteRuleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash"></i> Delete Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this rule?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This action cannot be undone. All associated conditions and actions will also be deleted.
                </div>
                <div id="deleteRuleInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Delete Rule
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let editingRuleId = null;
let deleteRuleId = null;

$(document).ready(function() {
    loadPartners();
    loadRules();
    
    // Partner change event
    $('#partner_id, #edit_partner_id').on('change', function() {
        const partnerId = $(this).val();
        const isEdit = $(this).attr('id').includes('edit');
        const productSelect = isEdit ? '#edit_product_id' : '#product_id';
        
        if (partnerId) {
            loadProductsByPartner(partnerId, productSelect);
        } else {
            $(productSelect).html('<option value="">Select Product</option>').prop('disabled', true);
        }
    });
    
    // Search functionality
    $('#searchInput').on('keyup', function() {
        filterRules();
    });
    
    // Filter functionality
    $('#partnerFilter, #statusFilter').on('change', function() {
        filterRules();
    });
});

// Add Rule Form
$('#ruleForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $('#submitBtn');
    const submitText = $('#submitText');
    const originalText = submitText.text();
    
    submitBtn.prop('disabled', true);
    submitText.text('Adding...');
    
    clearErrors();
    
    $.ajax({
        url: '{{ route("bre.rules.store") }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            showAlert('Rule added successfully!', 'success');
            resetForm();
            loadRules();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayErrors(xhr.responseJSON.errors);
            } else {
                showAlert('Error adding rule. Please try again.', 'danger');
            }
        },
        complete: function() {
            submitBtn.prop('disabled', false);
            submitText.text(originalText);
        }
    });
});

// Edit Rule Form
$('#editRuleForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $('#editSubmitBtn');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
    
    clearEditErrors();
    
    $.ajax({
        url: '/api/rules/' + editingRuleId,
        method: 'PUT',
        data: $(this).serialize(),
        success: function(response) {
            showAlert('Rule updated successfully!', 'success');
            $('#editRuleModal').modal('hide');
            loadRules();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayEditErrors(xhr.responseJSON.errors);
            } else {
                showAlert('Error updating rule. Please try again.', 'danger');
            }
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Rule');
        }
    });
});

// Delete Rule
$('#confirmDeleteBtn').on('click', function() {
    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
    
    $.ajax({
        url: '/api/rules/' + deleteRuleId,
        method: 'DELETE',
        success: function(response) {
            showAlert('Rule deleted successfully!', 'success');
            $('#deleteRuleModal').modal('hide');
            loadRules();
        },
        error: function(xhr) {
            showAlert('Error deleting rule. Please try again.', 'danger');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete Rule');
        }
    });
});

function loadPartners() {
    $.ajax({
        url: '/api/bre/partners',
        method: 'GET',
        success: function(response) {
            const partners = response.data || response;
            const selects = ['#partner_id', '#edit_partner_id', '#partnerFilter'];
            
            selects.forEach(selectId => {
                const $select = $(selectId);
                const currentValue = $select.val();
                
                if (selectId === '#partnerFilter') {
                    $select.html('<option value="">All Partners</option>');
                } else {
                    $select.html('<option value="">Select Partner</option>');
                }
                
                partners.forEach(partner => {
                    $select.append(`<option value="${partner.partner_id}">${partner.nbfc_name}</option>`);
                });
                
                if (currentValue) {
                    $select.val(currentValue);
                }
            });
        },
        error: function() {
            showAlert('Error loading partners', 'danger');
        }
    });
}

function loadProductsByPartner(partnerId, selectId) {
    $.ajax({
        url: `/api/bre/partners/${partnerId}/products`,
        method: 'GET',
        success: function(response) {
            const products = response.data || response;
            const $select = $(selectId);
            
            $select.html('<option value="">Select Product</option>');
            
            products.forEach(product => {
                $select.append(`<option value="${product.product_id}">${product.product_name}</option>`);
            });
            
            $select.prop('disabled', false);
        },
        error: function() {
            $(selectId).html('<option value="">Error loading products</option>');
        }
    });
}

function loadRules() {
    $.ajax({
        url: '/api/rules',
        method: 'GET',
        success: function(response) {
            renderRules(response.data || response);
        },
        error: function() {
            $('#rulesTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error loading rules
                    </td>
                </tr>
            `);
        }
    });
}

function renderRules(rules) {
    const tbody = $('#rulesTableBody');
    
    if (rules.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="9" class="text-center text-muted">
                    <i class="fas fa-list-alt fa-2x mb-2"></i><br>
                    No rules found
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    rules.forEach(rule => {
        const statusBadge = rule.is_active ? 
            '<span class="badge bg-success">Active</span>' : 
            '<span class="badge bg-secondary">Inactive</span>';
        
        const partnerName = rule.partner?.partner_name || rule.partner?.nbfc_name || 'N/A';
        const productName = rule.product?.product_name || 'N/A';
        const conditionsCount = rule.conditions_count || rule.conditions?.length || 0;
        const actionsCount = rule.actions_count || rule.actions?.length || 0;
        
        html += `
            <tr data-partner-id="${rule.partner_id}" data-status="${rule.is_active}">
                <td>
                    <strong>${rule.rule_name}</strong>
                    ${rule.description ? `<br><small class="text-muted">${rule.description.substring(0, 50)}${rule.description.length > 50 ? '...' : ''}</small>` : ''}
                </td>
                <td>${partnerName}</td>
                <td>${productName}</td>
                <td><span class="badge bg-info">${rule.rule_type || 'eligibility'}</span></td>
                <td>
                    <span class="badge bg-${rule.priority <= 10 ? 'danger' : rule.priority <= 50 ? 'warning' : 'secondary'}">${rule.priority}</span>
                </td>
                <td>
                    <span class="badge bg-primary">${conditionsCount} conditions</span>
                </td>
                <td>
                    <span class="badge bg-success">${actionsCount} actions</span>
                </td>
                <td>${statusBadge}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-info" onclick="viewRule(${rule.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="editRule(${rule.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteRule(${rule.id}, '${rule.rule_name}')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.html(html);
}

function viewRule(ruleId) {
    $.ajax({
        url: '/api/rules/' + ruleId,
        method: 'GET',
        success: function(rule) {
            const modalBody = $('#ruleDetailsBody');
            
            modalBody.html(`
                <div class="row">
                    <div class="col-md-6">
                        <h6>Basic Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Rule Name:</strong></td><td>${rule.rule_name}</td></tr>
                            <tr><td><strong>Type:</strong></td><td>${rule.rule_type || 'eligibility'}</td></tr>
                            <tr><td><strong>Priority:</strong></td><td>${rule.priority}</td></tr>
                            <tr><td><strong>Status:</strong></td><td>${rule.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'}</td></tr>
                            <tr><td><strong>Created:</strong></td><td>${new Date(rule.created_at).toLocaleDateString()}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Associated Data</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Partner:</strong></td><td>${rule.partner?.partner_name || rule.partner?.nbfc_name || 'N/A'}</td></tr>
                            <tr><td><strong>Product:</strong></td><td>${rule.product?.product_name || 'N/A'}</td></tr>
                            <tr><td><strong>Conditions:</strong></td><td>${rule.conditions?.length || 0} defined</td></tr>
                            <tr><td><strong>Actions:</strong></td><td>${rule.actions?.length || 0} defined</td></tr>
                        </table>
                    </div>
                </div>
                
                ${rule.description ? `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Description</h6>
                        <p class="text-muted">${rule.description}</p>
                    </div>
                </div>
                ` : ''}
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Conditions</h6>
                        ${rule.conditions && rule.conditions.length > 0 ? `
                            <div class="list-group">
                                ${rule.conditions.map(condition => `
                                    <div class="list-group-item">
                                        <small class="text-muted">${condition.variable_name}</small><br>
                                        <strong>${condition.operator}</strong> ${condition.value}
                                    </div>
                                `).join('')}
                            </div>
                        ` : '<p class="text-muted">No conditions defined</p>'}
                    </div>
                    <div class="col-md-6">
                        <h6>Actions</h6>
                        ${rule.actions && rule.actions.length > 0 ? `
                            <div class="list-group">
                                ${rule.actions.map(action => `
                                    <div class="list-group-item">
                                        <strong>${action.action_type}</strong><br>
                                        <small class="text-muted">${action.action_value || ''}</small>
                                    </div>
                                `).join('')}
                            </div>
                        ` : '<p class="text-muted">No actions defined</p>'}
                    </div>
                </div>
            `);
            
            // Update buttons
            $('#manageConditionsBtn').attr('href', `{{ url('/bre/conditions') }}?rule_id=${ruleId}`);
            $('#manageActionsBtn').attr('href', `{{ url('/bre/actions') }}?rule_id=${ruleId}`);
            
            $('#ruleDetailsModal').modal('show');
        },
        error: function() {
            showAlert('Error loading rule details. Please try again.', 'danger');
        }
    });
}

function editRule(ruleId) {
    $.ajax({
        url: '/api/rules/' + ruleId,
        method: 'GET',
        success: function(rule) {
            editingRuleId = ruleId;
            
            $('#edit_rule_id').val(rule.id);
            $('#edit_partner_id').val(rule.partner_id);
            $('#edit_rule_name').val(rule.rule_name);
            $('#edit_priority').val(rule.priority);
            $('#edit_is_active').val(rule.is_active ? '1' : '0');
            $('#edit_rule_type').val(rule.rule_type || 'eligibility');
            $('#edit_effective_from').val(rule.effective_from);
            $('#edit_effective_to').val(rule.effective_to);
            
            // Load products for the selected partner
            if (rule.partner_id) {
                loadProductsByPartner(rule.partner_id, '#edit_product_id');
                setTimeout(() => {
                    $('#edit_product_id').val(rule.product_id);
                }, 500);
            }
            
            clearEditErrors();
            $('#editRuleModal').modal('show');
        },
        error: function() {
            showAlert('Error loading rule data. Please try again.', 'danger');
        }
    });
}

function deleteRule(ruleId, ruleName) {
    deleteRuleId = ruleId;
    $('#deleteRuleInfo').html(`
        <strong>Rule:</strong> ${ruleName}<br>
        <strong>ID:</strong> ${ruleId}
    `);
    $('#deleteRuleModal').modal('show');
}

function filterRules() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const partnerFilter = $('#partnerFilter').val();
    const statusFilter = $('#statusFilter').val();
    const rows = $('#rulesTableBody tr');
    
    rows.each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        const rowPartnerId = row.data('partner-id');
        const rowStatus = row.data('status');
        
        let showRow = true;
        
        // Search filter
        if (searchTerm && !text.includes(searchTerm)) {
            showRow = false;
        }
        
        // Partner filter
        if (partnerFilter && rowPartnerId != partnerFilter) {
            showRow = false;
        }
        
        // Status filter
        if (statusFilter !== '' && rowStatus != statusFilter) {
            showRow = false;
        }
        
        if (showRow) {
            row.show();
        } else {
            row.hide();
        }
    });
}

function refreshRules() {
    loadRules();
    showAlert('Rules list refreshed!', 'info');
}

function resetForm() {
    $('#ruleForm')[0].reset();
    $('#product_id').html('<option value="">Select Product</option>').prop('disabled', true);
    $('#effective_from').val('{{ date("Y-m-d") }}'); // Reset to today's date
    clearErrors();
    $('#rule_name').focus();
}

function clearErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').empty();
}

function clearEditErrors() {
    $('#editRuleModal .is-invalid').removeClass('is-invalid');
    $('#editRuleModal .invalid-feedback').empty();
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
