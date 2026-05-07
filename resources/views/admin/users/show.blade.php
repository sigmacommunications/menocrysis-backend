@extends('admin.layouts.master')
@section('title')
User Details
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>User Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <!-- User Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Information</h3>
                    <div class="card-tools">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="user-profile-image" style="width: 100px; height: 100px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white; font-size: 24px;">
                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                        </div>
                        <h4 class="mt-2">{{ $user->first_name }} {{ $user->last_name }}</h4>
                        <span class="badge badge-{{ $user->role == 'barber' ? 'success' : 'info' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge badge-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Registered</th>
                            <td>{{ $user->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $user->updated_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Email Verified</th>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-warning">No</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="col-md-8">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $user->bookings->count() }}</h3>
                            <p>Total Bookings</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ ($user->review) ? $user->review->count() : 0 }}</h3>
                            <p>Total Reviews</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>${{ number_format($user->transaction->sum('amount'), 2) }}</h3>
                            <p>Total Spent</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Bookings</h3>
                </div>
                <div class="card-body p-0">
                    @if($user->bookings->count() > 0)
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Barber</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->bookings->take(5) as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>{{ $booking->barber_info->first_name ?? 'N/A' }}</td>
                                <td>{{ $booking->booking_date }}</td>
                                <td>
                                    <span class="badge badge-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>${{ number_format($booking->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-3 text-center">
                        <p>No bookings found.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Reviews</h3>
                </div>
                <div class="card-body p-0">
                    @if($user->review)
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Rating</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->review->take(5) as $review)
                            <tr>
                                <td>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-secondary' }}"></i>
                                    @endfor
                                </td>
                                <td>{{ Str::limit($review->description, 50) }}</td>
                                <td>{{ $review->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-3 text-center">
                        <p>No reviews found.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection