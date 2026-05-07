@extends('admin.layouts.master')
@section('title')
Payment Details
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Payment Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Payments</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <!-- Payment Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction Information</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div style="width: 80px; height: 80px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white; font-size: 24px;">
                            <i class="fas fa-check"></i>
                        </div>
                        <h4 class="mt-2 text-success">Payment Successful</h4>
                        <h2 class="text-success">${{ number_format($payment->amount, 2) }}</h2>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th>Transaction ID</th>
                            <td>#{{ $payment->id }}</td>
                        </tr>
                        <tr>
                            <th>Date & Time</th>
                            <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>
                                <span class="badge badge-{{ $payment->payment_method == 'stripe' ? 'success' : ($payment->payment_method == 'wallet' ? 'info' : 'warning') }}">
                                    {{ ucfirst($payment->payment_method ?? 'N/A') }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Reason</th>
                            <td>{{ $payment->reason }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><span class="badge badge-success">Completed</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Information</h3>
                </div>
                <div class="card-body">
                    @if($payment->user)
                    <div class="text-center mb-3">
                        <div style="width: 80px; height: 80px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white; font-size: 24px;">
                            {{ strtoupper(substr($payment->user->first_name, 0, 1)) }}{{ strtoupper(substr($payment->user->last_name, 0, 1)) }}
                        </div>
                        <h4 class="mt-2">{{ $payment->user->first_name }} {{ $payment->user->last_name }}</h4>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th>Email</th>
                            <td>{{ $payment->user->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $payment->user->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>User Since</th>
                            <td>{{ $payment->user->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge badge-{{ $payment->user->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($payment->user->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <div class="text-center mt-3">
                        <a href="{{ route('users.show', $payment->user->id) }}" class="btn btn-primary">
                            <i class="fas fa-user"></i> View Customer Profile
                        </a>
                    </div>
                    @else
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                        <h5>Customer Not Found</h5>
                        <p>The user associated with this payment may have been deleted.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Booking Information -->
            @if($payment->booking)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Related Booking</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Booking ID</th>
                            <td>#{{ $payment->booking->id }}</td>
                        </tr>
                        <tr>
                            <th>Barber</th>
                            <td>{{ $payment->booking->barber_info->first_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Booking Date</th>
                            <td>{{ $payment->booking->booking_date }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge badge-{{ $payment->booking->status == 'completed' ? 'success' : ($payment->booking->status == 'pending' ? 'warning' : 'info') }}">
                                    {{ ucfirst($payment->booking->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection