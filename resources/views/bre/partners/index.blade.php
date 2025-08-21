@extends('bre.layout')

@section('title', 'Partners Management')
@section('page-title', 'Partners')
@section('page-description', 'Manage NBFC Partners')

@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus"></i> Add New Partner</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bre.partners.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nbfc_name" class="form-label">NBFC Name *</label>
                        <input type="text" class="form-control @error('nbfc_name') is-invalid @enderror" 
                               id="nbfc_name" name="nbfc_name" value="{{ old('nbfc_name') }}" required>
                        @error('nbfc_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="registration_number" class="form-label">Registration Number *</label>
                        <input type="text" class="form-control @error('registration_number') is-invalid @enderror" 
                               id="registration_number" name="registration_number" value="{{ old('registration_number') }}" required>
                        @error('registration_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="rbi_license_type" class="form-label">RBI License Type *</label>
                        <select class="form-select @error('rbi_license_type') is-invalid @enderror" 
                                id="rbi_license_type" name="rbi_license_type" required>
                            <option value="">Select License Type</option>
                            <option value="NBFC" {{ old('rbi_license_type') === 'NBFC' ? 'selected' : '' }}>NBFC</option>
                            <option value="NBFC-MFI" {{ old('rbi_license_type') === 'NBFC-MFI' ? 'selected' : '' }}>NBFC-MFI</option>
                            <option value="NBFC-ICC" {{ old('rbi_license_type') === 'NBFC-ICC' ? 'selected' : '' }}>NBFC-ICC</option>
                            <option value="NBFC-IFC" {{ old('rbi_license_type') === 'NBFC-IFC' ? 'selected' : '' }}>NBFC-IFC</option>
                        </select>
                        @error('rbi_license_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="date_of_incorporation" class="form-label">Date of Incorporation *</label>
                        <input type="date" class="form-control @error('date_of_incorporation') is-invalid @enderror" 
                               id="date_of_incorporation" name="date_of_incorporation" value="{{ old('date_of_incorporation') }}" required>
                        @error('date_of_incorporation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="business_limit" class="form-label">Business Limit *</label>
                        <input type="number" class="form-control @error('business_limit') is-invalid @enderror" 
                               id="business_limit" name="business_limit" value="{{ old('business_limit') }}" step="0.01" required>
                        @error('business_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="registered_address" class="form-label">Registered Address</label>
                        <textarea class="form-control @error('registered_address') is-invalid @enderror" 
                                  id="registered_address" name="registered_address" rows="2">{{ old('registered_address') }}</textarea>
                        @error('registered_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                               id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Contact Email</label>
                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                               id="contact_email" name="contact_email" value="{{ old('contact_email') }}">
                        @error('contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                               id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="website_url" class="form-label">Website URL</label>
                        <input type="url" class="form-control @error('website_url') is-invalid @enderror" 
                               id="website_url" name="website_url" value="{{ old('website_url') }}">
                        @error('website_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Partner
                    </button>
                    <button type="reset" class="btn btn-secondary ms-2">
                        <i class="fas fa-undo"></i> Clear
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list"></i> Existing Partners</h5>
                <a href="{{ route('bre.partners.index') }}" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-sync"></i> Refresh
                </a>
            </div>
            <div class="card-body">
                @if($partners->count() > 0)
                    <div class="table-container">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NBFC Name</th>
                                    <th>License Type</th>
                                    <th>Business Limit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($partners as $partner)
                                <tr>
                                    <td>{{ $partner->partner_id }}</td>
                                    <td>
                                        <strong>{{ $partner->nbfc_name }}</strong><br>
                                        <small class="text-muted">{{ $partner->registration_number }}</small>
                                    </td>
                                    <td>{{ $partner->rbi_license_type }}</td>
                                    <td>₹{{ number_format($partner->business_limit, 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="viewPartner({{ $partner->partner_id }})" 
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-building fa-3x mb-3"></i>
                        <p>No partners found</p>
                        <small>Add your first partner using the form on the left</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Partner Details Modal -->
<div class="modal fade" id="partnerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-building"></i> Partner Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="partnerModalBody">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewPartner(partnerId) {
        const modal = new bootstrap.Modal(document.getElementById('partnerModal'));
        const modalBody = document.getElementById('partnerModalBody');
        
        // Show loading
        modalBody.innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        // Fetch partner details via API
        apiRequest(`/bre/partners/${partnerId}`)
            .then(partner => {
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>NBFC Name:</strong></td><td>${partner.nbfc_name}</td></tr>
                                <tr><td><strong>Registration Number:</strong></td><td>${partner.registration_number}</td></tr>
                                <tr><td><strong>RBI License Type:</strong></td><td>${partner.rbi_license_type}</td></tr>
                                <tr><td><strong>Incorporation Date:</strong></td><td>${partner.date_of_incorporation}</td></tr>
                                <tr><td><strong>Business Limit:</strong></td><td>₹${parseFloat(partner.business_limit).toLocaleString()}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Contact Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Contact Person:</strong></td><td>${partner.contact_person || 'N/A'}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${partner.contact_email || 'N/A'}</td></tr>
                                <tr><td><strong>Phone:</strong></td><td>${partner.phone_number || 'N/A'}</td></tr>
                                <tr><td><strong>Website:</strong></td><td>${partner.website_url ? `<a href="${partner.website_url}" target="_blank">${partner.website_url}</a>` : 'N/A'}</td></tr>
                                <tr><td><strong>Address:</strong></td><td>${partner.registered_address || 'N/A'}</td></tr>
                            </table>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Error loading partner details: ${error.message}
                    </div>
                `;
            });
    }
</script>
@endpush
