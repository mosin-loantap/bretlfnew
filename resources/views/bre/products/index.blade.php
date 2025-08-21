@extends('bre.layout')

@section('title', 'Products Management')
@section('page-title', 'Products')
@section('page-description', 'Manage Financial Products')

@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus"></i> Add New Product</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('bre.products.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="partner_id" class="form-label">Partner *</label>
                        <select class="form-select @error('partner_id') is-invalid @enderror" 
                                id="partner_id" name="partner_id" required>
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
                    
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name *</label>
                        <input type="text" class="form-control @error('product_name') is-invalid @enderror" 
                               id="product_name" name="product_name" value="{{ old('product_name') }}" required>
                        @error('product_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="product_type" class="form-label">Product Type *</label>
                        <select class="form-select @error('product_type') is-invalid @enderror" 
                                id="product_type" name="product_type" required>
                            <option value="">Select Type</option>
                            <option value="Loan" {{ old('product_type') === 'Loan' ? 'selected' : '' }}>Loan</option>
                            <option value="Credit Card" {{ old('product_type') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="Insurance" {{ old('product_type') === 'Insurance' ? 'selected' : '' }}>Insurance</option>
                            <option value="Investment" {{ old('product_type') === 'Investment' ? 'selected' : '' }}>Investment</option>
                        </select>
                        @error('product_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="product_category" class="form-label">Product Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('product_category') is-invalid @enderror" 
                                id="product_category" name="product_category" required>
                            <option value="">Select Category</option>
                            <option value="secured" {{ old('product_category') === 'secured' ? 'selected' : '' }}>Secured</option>
                            <option value="unsecured" {{ old('product_category') === 'unsecured' ? 'selected' : '' }}>Unsecured</option>
                        </select>
                        @error('product_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_amount" class="form-label">Min Amount</label>
                                <input type="number" class="form-control @error('min_amount') is-invalid @enderror" 
                                       id="min_amount" name="min_amount" value="{{ old('min_amount') }}" step="0.01">
                                @error('min_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_amount" class="form-label">Max Amount</label>
                                <input type="number" class="form-control @error('max_amount') is-invalid @enderror" 
                                       id="max_amount" name="max_amount" value="{{ old('max_amount') }}" step="0.01">
                                @error('max_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_tenure" class="form-label">Min Tenure (months)</label>
                                <input type="number" class="form-control @error('min_tenure') is-invalid @enderror" 
                                       id="min_tenure" name="min_tenure" value="{{ old('min_tenure') }}">
                                @error('min_tenure')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_tenure" class="form-label">Max Tenure (months)</label>
                                <input type="number" class="form-control @error('max_tenure') is-invalid @enderror" 
                                       id="max_tenure" name="max_tenure" value="{{ old('max_tenure') }}">
                                @error('max_tenure')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="interest_rate" class="form-label">Interest Rate (%)</label>
                        <input type="number" class="form-control @error('interest_rate') is-invalid @enderror" 
                               id="interest_rate" name="interest_rate" value="{{ old('interest_rate') }}" step="0.01">
                        @error('interest_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Product
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
                <h5><i class="fas fa-list"></i> Existing Products</h5>
                <a href="{{ route('bre.products.index') }}" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-sync"></i> Refresh
                </a>
            </div>
            <div class="card-body">
                @if($products->count() > 0)
                    <div class="table-container">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Type</th>
                                    <th>Partner</th>
                                    <th>Amount Range</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->product_id }}</td>
                                    <td>
                                        <strong>{{ $product->product_name }}</strong><br>
                                        @if($product->product_category)
                                            <span class="badge bg-secondary">{{ ucfirst($product->product_category) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->product_type }}</td>
                                    <td>{{ $product->partner->nbfc_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($product->min_amount && $product->max_amount)
                                            ₹{{ number_format($product->min_amount) }} - ₹{{ number_format($product->max_amount) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="viewProduct({{ $product->product_id }})" 
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
                        <i class="fas fa-box fa-3x mb-3"></i>
                        <p>No products found</p>
                        @if($partners->count() > 0)
                            <small>Add your first product using the form on the left</small>
                        @else
                            <small>You need to add a partner first</small>
                            <br><a href="{{ route('bre.partners.index') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> Add Partner
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-box"></i> Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="productModalBody">
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
    function viewProduct(productId) {
        const modal = new bootstrap.Modal(document.getElementById('productModal'));
        const modalBody = document.getElementById('productModalBody');
        
        // Show loading
        modalBody.innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        // Fetch product details via API
        apiRequest(`/products/${productId}`)
            .then(product => {
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Product Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Product Name:</strong></td><td>${product.product_name}</td></tr>
                                <tr><td><strong>Type:</strong></td><td>${product.product_type}</td></tr>
                                <tr><td><strong>Category:</strong></td><td>${product.product_category || 'N/A'}</td></tr>
                                <tr><td><strong>Partner:</strong></td><td>${product.partner?.nbfc_name || 'N/A'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Terms & Conditions</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Amount Range:</strong></td><td>₹${product.min_amount ? parseFloat(product.min_amount).toLocaleString() : 'N/A'} - ₹${product.max_amount ? parseFloat(product.max_amount).toLocaleString() : 'N/A'}</td></tr>
                                <tr><td><strong>Tenure Range:</strong></td><td>${product.min_tenure || 'N/A'} - ${product.max_tenure || 'N/A'} months</td></tr>
                                <tr><td><strong>Interest Rate:</strong></td><td>${product.interest_rate ? product.interest_rate + '%' : 'N/A'}</td></tr>
                            </table>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Error loading product details: ${error.message}
                    </div>
                `;
            });
    }
</script>
@endpush
