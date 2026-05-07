@extends('admin.layouts.master')
@section('title')
Dashboard
@endsection
@section('content')

<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <!-- Total Users -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->

        <!-- Total Barbers -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalBarbers }}</h3>
                    <p>Total Barbers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cut"></i>
                </div>
                <a href="{{ route('users.index') }}?role=barber" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->

        <!-- Total Bookings -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalBookings }}</h3>
                    <p>Total Bookings</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <a href="{{ route('bookings.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->

        <!-- Total Revenue -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($totalRevenue, 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('payments.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>

    <!-- Second Row Stats -->
    <div class="row">
        <!-- Today's Bookings -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $todayBookings }}</h3>
                    <p>Today's Bookings</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <a href="{{ route('bookings.index') }}?date=today" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->

        <!-- Pending Bookings -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $pendingBookings }}</h3>
                    <p>Pending Bookings</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('bookings.index') }}?status=pending" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->

        <!-- Monthly Revenue -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($monthlyRevenue, 2) }}</h3>
                    <p>Monthly Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="{{ route('payments.index') }}?period=monthly" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->

        <!-- Total Customers -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalCustomers }}</h3>
                    <p>Total Customers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <a href="{{ route('users.index') }}?role=user" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>

    <!-- Charts and Recent Activities Row -->
    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Revenue (Last 6 Months)</h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Booking Status Chart -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Booking Status</h3>
                </div>
                <div class="card-body">
                    <canvas id="bookingStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Row -->
    <div class="row">
        <!-- Recent Bookings -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Bookings</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Barber</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>{{ $booking->member_info->first_name ?? 'N/A' }}</td>
                                <td>{{ $booking->barber_info->first_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>{{ $booking->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Transactions</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $transaction)
                            <tr>
                                <td>#{{ $transaction->id }}</td>
                                <td>{{ $transaction->user->first_name ?? 'N/A' }}</td>
                                <td>${{ number_format($transaction->amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $transaction->type == 'credit' ? 'success' : 'danger' }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
@endsection

@push('scripts')
<script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    $(function () {
        // Revenue Chart
        var revenueCtx = document.getElementById('revenueChart').getContext('2d');
        var revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json(array_column($monthlyRevenueData, 'month')),
                datasets: [{
                    label: 'Revenue ($)',
                    data: @json(array_column($monthlyRevenueData, 'revenue')),
                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });

        // Booking Status Chart
        var statusCtx = document.getElementById('bookingStatusChart').getContext('2d');
        var statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        {{ $bookingStatusData['pending'] }},
                        {{ $bookingStatusData['confirmed'] }},
                        {{ $bookingStatusData['completed'] }},
                        {{ $bookingStatusData['cancelled'] }}
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#17a2b8',
                        '#28a745',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endpush