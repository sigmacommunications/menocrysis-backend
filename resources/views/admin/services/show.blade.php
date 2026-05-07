@extends('admin.layouts.master')
@section('title')
Service Details
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Service Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Services</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <!-- Service Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Service Information</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-{{ $service->status == 'active' ? 'warning' : 'success' }} btn-sm toggle-status" 
                                data-id="{{ $service->id }}">
                            <i class="fas fa-{{ $service->status == 'active' ? 'pause' : 'play' }}"></i>
                            {{ $service->status == 'active' ? 'Deactivate' : 'Activate' }}
                        </button>
                        <button type="button" class="btn btn-danger btn-sm delete-service" data-id="{{ $service->id }}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div style="width: 100px; height: 100px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white; font-size: 24px;">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <h4 class="mt-2">{{ $service->name }}</h4>
                        <span class="badge badge-{{ $service->status == 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($service->status) }}
                        </span>
                        @if($service->main_service)
                            <span class="badge badge-primary ml-1">Main Service</span>
                        @else
                            <span class="badge badge-secondary ml-1">Additional Service</span>
                        @endif
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th>Barber</th>
                            <td>
                                <a href="{{ route('users.show', $service->user_id) }}">
                                    {{ $service->barber->first_name ?? 'N/A' }} {{ $service->barber->last_name ?? '' }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Price</th>
                            <td>${{ number_format($service->price, 2) }}</td>
                        </tr>
                        @if($service->service_info)
                        <tr>
                            <th>Base Service</th>
                            <td>{{ $service->service_info->name }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Created</th>
                            <td>{{ $service->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $service->updated_at->format('M d, Y h:i A') }}</td>
                        tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Service Statistics -->
        <div class="col-md-8">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $service->bookings->count() }}</h3>
                            <p>Total Bookings</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>${{ number_format($service->bookings->sum('total_price'), 2) }}</h3>
                            <p>Total Revenue</p>
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
                    @if($service->bookings->count() > 0)
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($service->bookings->take(5) as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>{{ $booking->member_info->first_name ?? 'N/A' }}</td>
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
                        <p>No bookings found for this service.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Delete service confirmation
        $('.delete-service').click(function() {
            var serviceId = $(this).data('id');
            if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
                $.ajax({
                    url: "{{ url('admin/services') }}/" + serviceId,
                    type: 'DELETE',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = "{{ route('services.index') }}";
                        } else {
                            alert('Error deleting service');
                        }
                    }
                });
            }
        });

        // Toggle service status
        $('.toggle-status').click(function() {
            var serviceId = $(this).data('id');
            var button = $(this);
            
            $.ajax({
                url: "{{ url('admin/services') }}/" + serviceId + "/toggle-status",
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        });
    });
</script>
@endpush