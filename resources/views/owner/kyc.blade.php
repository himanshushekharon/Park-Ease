@extends('layouts.app')

@section('title', 'Owner KYC Verification')

@section('content')
<div class="container mt-5 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-5 shadow-sm">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Host KYC Verification</h2>
                    <p class="text-muted">To ensure the safety and security of our platform, all parking hosts must be verified.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/api/owner/kyc" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Personal Information</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone" value="{{ auth()->user()->phone ?? '' }}" placeholder="+91 9876543210" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Aadhaar Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="aadhaar_no" value="{{ auth()->user()->aadhaar_no ?? '' }}" pattern="[0-9]{12}" title="12 Digit Aadhaar Number" placeholder="XXXX XXXX XXXX" required>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2">Document Uploads</h5>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Owner Photo <span class="text-danger">*</span></label>
                        <p class="small text-muted mb-2">Please upload a clear, front-facing passport-style photo of yourself. <strong>(Size: 10KB - 2MB)</strong></p>
                        <input class="form-control" type="file" name="photo" accept="image/*" {{ auth()->user()->kyc_status === 'verified' ? '' : 'required' }}>
                        @if(auth()->user()->kyc_status === 'verified')
                            <small class="text-success"><i class="bi bi-check-circle"></i> File uploaded. Select a new file only if you wish to change it.</small>
                        @endif
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold">Property/Parking Location Image <span class="text-danger">*</span></label>
                        <p class="small text-muted mb-2">Upload a wide shot of the parking area you intend to list. <strong>(Size: 10KB - 2MB)</strong></p>
                        <input class="form-control" type="file" name="property_image" accept="image/*" {{ auth()->user()->kyc_status === 'verified' ? '' : 'required' }}>
                        @if(auth()->user()->kyc_status === 'verified')
                            <small class="text-success"><i class="bi bi-check-circle"></i> File uploaded. Select a new file only if you wish to change it.</small>
                        @endif
                    </div>

                    <div class="alert alert-light border small text-muted mb-4">
                        <i class="bi bi-shield-check text-success me-2"></i> By submitting this form, you agree to our Terms of Service. Your data is securely encrypted.
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-3 fs-5">Submit for Verification</button>
                </form>
                
                <div class="text-center mt-4">
                    <a href="/switch-role" class="text-muted text-decoration-none small">I changed my mind, switch back to User</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
