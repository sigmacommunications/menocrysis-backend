<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
		 <div style="text-align: center; margin-top: 50px;background-color:black">
			<img src="{{ asset('admin/Logo.png') }}" alt="Logo" style="width: 200px; height: auto;">
		</div>
        <h2>Email Verification for Account Deletion</h2>

        <!-- Display Success Messages -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Display Error Messages -->
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Step 1: Send OTP Form -->
        @if (!session('otp_sent'))
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Step 1: Send OTP to Email</h5>
                    <form method="POST" action="{{ url('/send-otp') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" value="{{ old('email') }}" required>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Send OTP</button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Step 2: Verify OTP Form -->
        @if (session('otp_sent'))
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Step 2: Verify OTP</h5>
                    <form method="POST" action="{{ url('/verify-otp-delete') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="otp_email" class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" id="otp_email" value="{{ session('email') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="otp" class="form-label">OTP</label>
                            <input type="text" name="otp" class="form-control" id="otp" placeholder="Enter OTP" required>
                            @error('otp')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger">Verify & Delete Account</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
