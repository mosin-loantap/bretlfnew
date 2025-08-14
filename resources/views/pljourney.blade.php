<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoanTap - Personal Loan Application</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts - LoanTap uses modern, clean fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- LoanTap Custom Styles -->

        <link href="/css/styles.css" rel="stylesheet">

    <!-- Loader Styles -->
    <style>
        .step-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }
        
        .loader-content {
            text-align: center;
            color: #7B4397;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #7B4397;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loader-text {
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            font-weight: 500;
            margin: 0;
        }
        
        .loader-subtext {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: #666;
            margin: 5px 0 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <div class="logo-placeholder">
               <img src="img/logo.svg" alt="LoanTap Logo" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </nav>

    <!-- Step Transition Loader -->
    <div class="step-loader" id="stepLoader">
        <div class="loader-content">
            <div class="spinner"></div>
            <p class="loader-text" id="loaderText">Processing...</p>
            <p class="loader-subtext" id="loaderSubtext">Please wait</p>
        </div>
    </div>

    <!-- Application Status -->
    <div class="container">
        <div class="application-status">
            <span>You're Applying for : <span style="color: #7B4397; font-weight: 500;">Personal Loan</span></span>
            <button class="close-btn" type="button">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="main-container">
            <div class="row g-0">
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar h-100">
                        <h4 class="text-center">Complete Your Application</h4>
                        
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <div class="step-title">Basic Info</div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number inactive">2</div>
                            <div class="step-title">Offers</div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number inactive">3</div>
                            <div class="step-title">KYC</div>
                        </div>
                        
                        <div class="footer-text">
                            Fast . Flexible . Friendly
                        </div>
                    </div>
                </div>
                
                <!-- Form Section -->
                <div class="col-lg-8">
                    <div class="form-section">
                        <!-- Step 1: Basic Information -->
                        <div id="step1" class="form-step active">
                            <h2 class="section-title">Sign Up</h2>
                            
                            <form id="basicInfoForm">
                                <!-- Email Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <input type="email" id="email" class="form-control" placeholder="Enter Personal Email" required>
                                    </div>
                                </div>
                                
                                <!-- Mobile Number Field -->
                                <div class="form-group">
                                    <div class="row g-2">
                                        <div class="col-8">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-phone"></i>
                                                </span>
                                                <input type="tel" id="mobileNumber" class="form-control" placeholder="Enter Mobile Number*" required>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" id="otpBtn" class="btn-otp">Send OTP</button>
                                        </div>
                                    </div>
                                    
                                    <!-- OTP Verification Field (Hidden initially) -->
                                    <div class="otp-verification" id="otpVerification">
                                        <div class="row g-2">
                                            <div class="col-8">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-shield-check"></i>
                                                    </span>
                                                    <input type="text" id="otpInput" class="form-control" placeholder="Enter OTP" maxlength="6">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="row g-1">
                                                    <div class="col-12">
                                                        <button type="button" id="verifyBtn" class="btn-verify">Verify OTP</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-2 mt-1">
                                            <div class="col-8">
                                                <small class="text-muted" id="mobileDisplay"></small>
                                            </div>
                                            <div class="col-4">
                                                <button type="button" id="resendBtn" class="btn-resend" disabled>Resend OTP</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pincode Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-geo-alt"></i>
                                        </span>
                                        <input type="text" id="pincode" class="form-control" placeholder="Pincode (Ex.411006)*" required>
                                    </div>
                                </div>
                                
                                <!-- Loan City Dropdown -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-building"></i>
                                        </span>
                                        <select id="loanCity" class="form-control" required>
                                            <option value="" disabled selected>Select Loan City*</option>
                                            <option value="mumbai">Mumbai</option>
                                            <option value="delhi">Delhi</option>
                                            <option value="bangalore">Bangalore</option>
                                            <option value="pune">Pune</option>
                                            <option value="hyderabad">Hyderabad</option>
                                            <option value="chennai">Chennai</option>
                                            <option value="kolkata">Kolkata</option>
                                            <option value="ahmedabad">Ahmedabad</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Income Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-currency-rupee"></i>
                                        </span>
                                        <input type="number" id="income" class="form-control" placeholder="Enter Fixed Income As Per Bank Statement" required>
                                    </div>
                                </div>
                                
                                
                                <!-- Consent Section -->
                                <div class="consent-section">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="consentCheck" required>
                                        <label class="form-check-label consent-text" for="consentCheck">
                                            I hereby confirm my consent and authorise LoanTap and its affiliates to 
                                            contact me through SMS / Call / WhatsApp on the mobile number provided, 
                                            and to use or share this number for verification purposes for processing my 
                                            loan application. <a href="#" class="read-more">Read more</a>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Continue Button -->
                                <div class="form-group">
                                    <button type="submit" class="btn-continue">Continue</button>
                                </div>
                            </form>
                        </div>

                        <!-- Step 2: Personal Details (No PAN/Aadhaar Verification) -->
                        <div id="step2" class="form-step" style="display: none;">
                            <h2 class="section-title">Personal Details</h2>
                            <form id="personalDetailsForm">
                                <!-- Name as per PAN Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person-badge"></i>
                                        </span>
                                        <input type="text" id="nameAsPerPAN" class="form-control" placeholder="Name as per PAN" required>
                                    </div>
                                    <small class="text-muted">Enter your name exactly as on your PAN card</small>
                                </div>

                                <!-- PAN Number Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-credit-card-2-front"></i>
                                        </span>
                                        <input type="text" id="panNumber" class="form-control" placeholder="PAN Number (Ex. ABCDE1234F)" maxlength="10" required>
                                    </div>
                                    <small class="text-muted">Enter your 10-character PAN number</small>
                                </div>

                                <!-- Date of Birth Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar"></i>
                                        </span>
                                        <input type="date" id="dateOfBirth" class="form-control" required>
                                    </div>
                                    <small class="text-muted">As per your official documents</small>
                                </div>
                                <!-- Action Buttons -->
                                <div class="form-group">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button type="button" id="backBtn" class="btn-back">Back</button>
                                        </div>
                                        <div class="col-6">
                                            <button type="submit" class="btn-continue">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Step 3: Employment & Loan Details -->
                        <div id="step3" class="form-step" style="display: none;">
                            <h2 class="section-title">Employment & Loan Details</h2>
                            
                            <form id="employmentDetailsForm">
                                <!-- Employer Name Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-briefcase"></i>
                                        </span>
                                        <input type="text" id="employerName" class="form-control" placeholder="Enter Employer Name*" required>
                                    </div>
                                </div>

                                <!-- Official Email ID Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-envelope-at"></i>
                                        </span>
                                        <input type="email" id="officialEmail" class="form-control" placeholder="Enter Official Email ID*" required>
                                    </div>
                                    <small class="text-muted">Your work/company email address</small>
                                </div>

                                <!-- Years of Experience Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-clock-history"></i>
                                        </span>
                                        <select id="yearsOfExperience" class="form-control" required>
                                            <option value="" disabled selected>Select Years of Experience*</option>
                                            <option value="0-1">0-1 Years</option>
                                            <option value="1-2">1-2 Years</option>
                                            <option value="2-3">2-3 Years</option>
                                            <option value="3-5">3-5 Years</option>
                                            <option value="5-7">5-7 Years</option>
                                            <option value="7-10">7-10 Years</option>
                                            <option value="10+">10+ Years</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="form-group">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button type="button" id="backBtn2" class="btn-back">Back</button>
                                        </div>
                                        <div class="col-6">
                                            <button type="submit" class="btn-continue">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Step 4: Personal & Loan Details -->
                        <div id="step4" class="form-step" style="display: none;">
                            <h2 class="section-title">Personal & Loan Details</h2>
                            
                            <form id="personalLoanDetailsForm">
                                <!-- Gender Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <select id="gender" class="form-control" required>
                                            <option value="" disabled selected>Select Gender*</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Marital Status Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-heart"></i>
                                        </span>
                                        <select id="maritalStatus" class="form-control" required>
                                            <option value="" disabled selected>Select Marital Status*</option>
                                            <option value="single">Single</option>
                                            <option value="married">Married</option>
                                            <option value="divorced">Divorced</option>
                                            <option value="widowed">Widowed</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Father's Name Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person-badge"></i>
                                        </span>
                                        <input type="text" id="fatherName" class="form-control" placeholder="Enter Father's Name*" required>
                                    </div>
                                    <small class="text-muted">As per your documents</small>
                                </div>

                                <!-- Requested Loan Amount Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-currency-rupee"></i>
                                        </span>
                                        <input type="number" id="requestedLoanAmount" class="form-control" placeholder="Enter Requested Loan Amount*" min="50000" max="5000000" required>
                                    </div>
                                    <small class="text-muted">Minimum ₹50,000 - Maximum ₹50,00,000</small>
                                </div>

                                <!-- Loan Purpose Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-target"></i>
                                        </span>
                                        <select id="loanPurpose" class="form-control" required>
                                            <option value="" disabled selected>Select Loan Purpose*</option>
                                            <option value="debt-consolidation">Debt Consolidation</option>
                                            <option value="home-improvement">Home Improvement</option>
                                            <option value="medical-expenses">Medical Expenses</option>
                                            <option value="education">Education</option>
                                            <option value="wedding">Wedding</option>
                                            <option value="travel">Travel</option>
                                            <option value="business">Business</option>
                                            <option value="emergency">Emergency</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Address Line 1 Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-house"></i>
                                        </span>
                                        <input type="text" id="addressLine1" class="form-control" placeholder="Enter Address Line 1*" required>
                                    </div>
                                    <small class="text-muted">House/Flat No., Building Name, Street</small>
                                </div>

                                <!-- Address Line 2 Field -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-signpost"></i>
                                        </span>
                                        <input type="text" id="addressLine2" class="form-control" placeholder="Enter Address Line 2">
                                    </div>
                                    <small class="text-muted">Area, Landmark, City, State (Optional)</small>
                                </div>

                                <!-- Action Buttons -->
                                <div class="form-group">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button type="button" id="backBtn3" class="btn-back">Back</button>
                                        </div>
                                        <div class="col-6">
                                            <button type="submit" class="btn-continue">Submit Application</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <footer class="bottom-footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start text-center">
                    <button class="disclaimer-btn">Disclaimer</button>
                </div>
                <div class="col-md-6 text-md-end text-center">
                    LoanTap | © All Rights Reserved
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation and interaction
        document.addEventListener('DOMContentLoaded', function() {
            // Form elements for Step 1
            const basicInfoForm = document.getElementById('basicInfoForm');
            const consentCheck = document.getElementById('consentCheck');
            const mobileInput = document.getElementById('mobileNumber');
            const otpBtn = document.getElementById('otpBtn');
            const otpVerification = document.getElementById('otpVerification');
            const otpInput = document.getElementById('otpInput');
            const verifyBtn = document.getElementById('verifyBtn');
            const resendBtn = document.getElementById('resendBtn');
            const mobileDisplay = document.getElementById('mobileDisplay');
            
            
            // Form elements for Step 2
            const personalDetailsForm = document.getElementById('personalDetailsForm');
            const dateOfBirth = document.getElementById('dateOfBirth');
            const backBtn = document.getElementById('backBtn');
            
            // Form elements for Step 3
            const employmentDetailsForm = document.getElementById('employmentDetailsForm');
            const employerName = document.getElementById('employerName');
            const officialEmail = document.getElementById('officialEmail');
            const yearsOfExperience = document.getElementById('yearsOfExperience');
            const backBtn2 = document.getElementById('backBtn2');
            
            // Form elements for Step 4
            const personalLoanDetailsForm = document.getElementById('personalLoanDetailsForm');
            const gender = document.getElementById('gender');
            const maritalStatus = document.getElementById('maritalStatus');
            const fatherName = document.getElementById('fatherName');
            const requestedLoanAmount = document.getElementById('requestedLoanAmount');
            const loanPurpose = document.getElementById('loanPurpose');
            const addressLine1 = document.getElementById('addressLine1');
            const addressLine2 = document.getElementById('addressLine2');
            const backBtn3 = document.getElementById('backBtn3');
            
            // Step navigation
            const step1 = document.getElementById('step1');
            const step2 = document.getElementById('step2');
            const step3 = document.getElementById('step3');
            const step4 = document.getElementById('step4');
            
            // Loader elements
            const stepLoader = document.getElementById('stepLoader');
            const loaderText = document.getElementById('loaderText');
            const loaderSubtext = document.getElementById('loaderSubtext');
            
            let otpSent = false;
            let otpVerified = false;
            // PAN/Aadhaar/Voter ID verification removed
            let currentStep = 1;
            
            // Sample PAN database for demonstration
            const panDatabase = {
                'ABCDE1234F': 'JOHN SMITH',
                'DEFGH5678I': 'PRIYA SHARMA',
                'GHIJK9012L': 'RAHUL KUMAR',
                'JKLMN3456O': 'ANITA SINGH',
                'MNOPQ7890R': 'AMIT PATEL'
            };
            
            // Loader functions
            function showLoader(text = 'Processing...', subtext = 'Please wait') {
                loaderText.textContent = text;
                loaderSubtext.textContent = subtext;
                stepLoader.style.display = 'flex';
            }
            
            function hideLoader() {
                stepLoader.style.display = 'none';
            }
            
            // Send OTP button functionality
            otpBtn.addEventListener('click', function() {
                const mobileValue = mobileInput.value.trim();
                
                if (mobileValue.length >= 10) {
                    // If this is the first time, show OTP verification field
                    if (!otpSent) {
                        // Show OTP verification field with animation
                        otpVerification.classList.add('show');
                        
                        // Update mobile display
                        mobileDisplay.textContent = mobileValue;
                        
                        // Hide the separate resend button
                        resendBtn.style.display = 'none';
                        
                        // Change button text to Resend OTP
                        otpBtn.textContent = 'Resend OTP';
                        mobileInput.disabled = true;
                        mobileInput.style.opacity = '0.6';
                        
                        otpSent = true;
                        
                        // Focus on OTP input
                        setTimeout(() => {
                            otpInput.focus();
                        }, 300);
                    } else {
                        // This is a resend action
                        const originalText = otpBtn.textContent;
                        otpBtn.textContent = 'Resending...';
                        otpBtn.disabled = true;
                        
                        setTimeout(() => {
                            otpBtn.textContent = originalText;
                            otpBtn.disabled = false;
                            otpInput.focus();
                            otpInput.value = ''; // Clear previous OTP
                        }, 2000);
                    }
                    
                    // Simulate OTP sending (replace with actual API call)
                    console.log('OTP sent to:', mobileValue);
                    
                } else {
                    alert('Please enter a valid 10-digit mobile number');
                    mobileInput.focus();
                }
            });
            
            // Verify OTP button functionality
            verifyBtn.addEventListener('click', function() {
                const otpValue = otpInput.value.trim();
                
                if (otpValue.length === 6) {
                    // Simulate OTP verification (replace with actual API call)
                    if (otpValue === '123456' || otpValue.length === 6) { // Demo OTP for testing
                        verifyBtn.textContent = 'Verified ✓';
                        verifyBtn.style.background = '#28a745';
                        verifyBtn.disabled = true;
                        otpInput.disabled = true;
                        otpInput.style.opacity = '0.6';
                        
                        // Hide both resend buttons since verification is complete
                        resendBtn.style.display = 'none';
                        otpBtn.style.display = 'none';
                        
                        otpVerified = true;
                        
                        // Show success message
                        setTimeout(() => {
                            alert('Mobile number verified successfully!');
                        }, 500);
                        
                        console.log('OTP verified successfully');
                    } else {
                        alert('Invalid OTP. Please try again.');
                        otpInput.focus();
                        otpInput.select();
                    }
                } else {
                    alert('Please enter a 6-digit OTP');
                    otpInput.focus();
                }
            });
            
            // Resend OTP button functionality
            resendBtn.addEventListener('click', function() {
                const mobileValue = mobileInput.value.trim();
                
                resendBtn.textContent = 'Resending...';
                resendBtn.disabled = true;
                
                // Simulate resend delay
                setTimeout(() => {
                    resendBtn.textContent = 'Resend OTP';
                    resendBtn.disabled = false;
                    otpInput.focus();
                    alert('OTP resent successfully!');
                    console.log('OTP resent to:', mobileValue);
                }, 2000);
            });
            
            // Allow only numbers in OTP input
            otpInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
                
                // Auto-verify when 6 digits are entered
                if (this.value.length === 6) {
                    setTimeout(() => {
                        verifyBtn.click();
                    }, 500);
                }
            });
            
            // Allow only numbers in mobile input
            mobileInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
                if (this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }
            });
            
            // Voter ID formatting and validation
            
            // Verify Voter ID button functionality
            
            // Voter ID validation function
            
            // PAN/Aadhaar verification logic removed
            
            // Step 1 Form submission (Basic Info)
            basicInfoForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!consentCheck.checked) {
                    alert('Please accept the consent to continue');
                    return;
                }
                
                if (otpSent && !otpVerified) {
                    alert('Please verify your mobile number with OTP first');
                    otpInput.focus();
                    return;
                }
                
                if (!otpSent) {
                    alert('Please verify your mobile number first');
                    mobileInput.focus();
                    return;
                }
                
                // Validate all required fields
                const requiredFields = basicInfoForm.querySelectorAll('[required]');
                let allValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.disabled && !field.value.trim()) {
                        field.classList.add('is-invalid');
                        allValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (allValid && otpVerified) {
                    // Show loader before transitioning
                    showLoader('Validating Information...', 'Moving to personal details');
                    
                    // Simulate processing delay
                    setTimeout(() => {
                        // Move to Step 2
                        showStep(2);
                        hideLoader();
                        console.log('Step 1 completed, moving to Step 2');
                        
                        // Log voter ID verification status
                        // Voter ID verification removed
                    }, 2000);
                }
            });
            
            // Step 2 Form submission (Personal Details)
            personalDetailsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const dob = dateOfBirth.value;
                if (!dob) {
                    alert('Please select your date of birth');
                    dateOfBirth.focus();
                    return;
                }
                // All validations passed
                showLoader('Verifying Personal Details...', 'Moving to employment information');
                setTimeout(() => {
                    showStep(3);
                    hideLoader();
                    console.log('Step 2 completed - DOB:', dob);
                }, 2000);
            });
            
            // Step 3 Form submission (Employment Details)
            employmentDetailsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const employer = employerName.value.trim();
                const email = officialEmail.value.trim();
                const experience = yearsOfExperience.value;
                
                if (!employer) {
                    alert('Please enter your employer name');
                    employerName.focus();
                    return;
                }
                
                if (!email) {
                    alert('Please enter your official email ID');
                    officialEmail.focus();
                    return;
                }
                
                if (!experience) {
                    alert('Please select your years of experience');
                    yearsOfExperience.focus();
                    return;
                }
                
                // All validations passed - Move to Step 4
                showLoader('Processing Employment Details...', 'Moving to final step');
                
                // Simulate processing delay
                setTimeout(() => {
                    // Move to Step 4
                    showStep(4);
                    hideLoader();
                    console.log('Step 3 completed - Employer:', employer, 'Email:', email, 'Experience:', experience);
                }, 2000);
            });
            
            // Step 4 Form submission (Personal & Loan Details)
            personalLoanDetailsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const selectedGender = gender.value;
                const maritalStat = maritalStatus.value;
                const father = fatherName.value.trim();
                const amount = requestedLoanAmount.value.trim();
                const purpose = loanPurpose.value;
                const address1 = addressLine1.value.trim();
                const address2 = addressLine2.value.trim();
                
                if (!selectedGender) {
                    alert('Please select your gender');
                    gender.focus();
                    return;
                }
                
                if (!maritalStat) {
                    alert('Please select your marital status');
                    maritalStatus.focus();
                    return;
                }
                
                if (!father) {
                    alert('Please enter your father\'s name');
                    fatherName.focus();
                    return;
                }
                
                if (!amount || amount < 50000 || amount > 5000000) {
                    alert('Please enter a valid loan amount between ₹50,000 and ₹50,00,000');
                    requestedLoanAmount.focus();
                    return;
                }
                
                if (!purpose) {
                    alert('Please select the loan purpose');
                    loanPurpose.focus();
                    return;
                }
                
                if (!address1) {
                    alert('Please enter your address line 1');
                    addressLine1.focus();
                    return;
                }
                
                // All validations passed - Final submission
                showLoader('Submitting Application...', 'Processing your loan application');
                
                // Simulate final processing delay
                setTimeout(() => {
                    hideLoader();
                    alert('Application submitted successfully! You will receive a confirmation email shortly.');
                    console.log('Step 4 completed - Gender:', selectedGender, 'Marital Status:', maritalStat, 'Father:', father, 'Amount:', amount, 'Purpose:', purpose, 'Address1:', address1, 'Address2:', address2);
                    
                    // Here you would submit the complete application to your server
                    console.log('Complete Application Data:', {
                        // Step 1 data
                        email: document.getElementById('email').value,
                        mobile: mobileInput.value,
                        pincode: document.getElementById('pincode').value,
                        city: document.getElementById('loanCity').value,
                        income: document.getElementById('income').value,
                        // Step 2 data
                        pan: panNumber.value,
                        dob: dateOfBirth.value,
                        name: nameAsPerPAN.value,
                        // Step 3 data
                        employer: employerName.value,
                        officialEmail: officialEmail.value,
                        experience: yearsOfExperience.value,
                        // Step 4 data
                        gender: selectedGender,
                        maritalStatus: maritalStat,
                        fatherName: father,
                        requestedLoanAmount: amount,
                        loanPurpose: purpose,
                        addressLine1: addressLine1,
                        addressLine2: addressLine2
                    });
                }, 3000);
            });
            
            // Back button functionality
            backBtn.addEventListener('click', function() {
                showLoader('Going Back...', 'Loading previous step');
                setTimeout(() => {
                    showStep(1);
                    hideLoader();
                }, 1000);
            });
            
            // Back button functionality for Step 3
            backBtn2.addEventListener('click', function() {
                showLoader('Going Back...', 'Loading previous step');
                setTimeout(() => {
                    showStep(2);
                    hideLoader();
                }, 1000);
            });
            
            // Back button functionality for Step 4
            backBtn3.addEventListener('click', function() {
                showLoader('Going Back...', 'Loading previous step');
                setTimeout(() => {
                    showStep(3);
                    hideLoader();
                }, 1000);
            });
            
            // Loan amount formatting for Step 4
            requestedLoanAmount.addEventListener('input', function() {
                // Remove any non-numeric characters
                this.value = this.value.replace(/\D/g, '');
                
                // Add validation feedback
                const value = parseInt(this.value);
                const helpText = this.nextElementSibling;
                
                if (value < 50000) {
                    helpText.className = 'text-danger';
                    helpText.textContent = 'Minimum amount is ₹50,000';
                } else if (value > 5000000) {
                    helpText.className = 'text-danger';
                    helpText.textContent = 'Maximum amount is ₹50,00,000';
                } else {
                    helpText.className = 'text-muted';
                    helpText.textContent = 'Minimum ₹50,000 - Maximum ₹50,00,000';
                }
            });
            
            // Father's name formatting
            fatherName.addEventListener('input', function() {
                // Allow only letters and spaces
                this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            });
            
            // Function to show specific step
            function showStep(stepNumber) {
                // Hide all steps
                step1.style.display = 'none';
                step2.style.display = 'none';
                step3.style.display = 'none';
                step4.style.display = 'none';
                
                // Show current step
                if (stepNumber === 1) {
                    step1.style.display = 'block';
                    currentStep = 1;
                } else if (stepNumber === 2) {
                    step2.style.display = 'block';
                    currentStep = 2;
                    // Focus on first input of step 2
                    setTimeout(() => {
                        panNumber.focus();
                    }, 100);
                } else if (stepNumber === 3) {
                    step3.style.display = 'block';
                    currentStep = 3;
                    // Focus on first input of step 3
                    setTimeout(() => {
                        employerName.focus();
                    }, 100);
                } else if (stepNumber === 4) {
                    step4.style.display = 'block';
                    currentStep = 4;
                    // Focus on first input of step 4
                    setTimeout(() => {
                        gender.focus();
                    }, 100);
                }
            }
            
            // Close button functionality
            document.querySelector('.close-btn').addEventListener('click', function() {
                if (confirm('Are you sure you want to close the application?')) {
                    window.close();
                }
            });
        });
    </script>
</body>
</html>