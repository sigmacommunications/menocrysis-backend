@extends('admin.layouts.master')
@section('title')
Revenue Distribution
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Revenue Distribution</h1>
            <p class="text-muted">Actual earnings and booking analytics</p>
        </div>
        <div class="col-sm-6 text-right">
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#analyticsModal">
                <i class="fas fa-chart-bar"></i> View Analytics
            </button>
        </div>
    </div>

    <!-- Revenue Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format($revenueStats['total_revenue'], 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($revenueStats['today_revenue'], 2) }}</h3>
                    <p>Today's Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>${{ number_format($revenueStats['month_revenue'], 2) }}</h3>
                    <p>This Month</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $revenueStats['active_barbers'] }}</h3>
                    <p>Active Barbers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Statistics -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $revenueStats['total_bookings'] }}</h3>
                    <p>Total Bookings</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $revenueStats['completed_bookings'] }}</h3>
                    <p>Completed Bookings</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>${{ number_format($revenueStats['avg_booking_value'], 2) }}</h3>
                    <p>Avg. Booking Value</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Earning Barbers -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top Earning Barbers</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Barber</th>
                                    <th>Earnings</th>
                                    <th>Bookings</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topBarbers as $barber)
                                <tr>
                                    <td>
                                        <strong>{{ $barber->first_name }} {{ $barber->last_name }}</strong>
                                        @if($barber->phone)
                                        <br>
                                        <small class="text-muted"><i class="fas fa-phone"></i> {{ $barber->phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-success">${{ number_format($barber->total_earnings, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $barber->total_bookings }}</span>
                                        <br>
                                        <small class="text-muted">{{ $barber->completed_bookings }} completed</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info btn-sm view-barber-earnings" 
                                                    data-barber-id="{{ $barber->id }}"
                                                    data-barber-name="{{ $barber->first_name }} {{ $barber->last_name }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('users.show', $barber->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-user"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Payments</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('M d') }}</td>
                                    <td>
                                        @if($transaction->user)
                                            {{ $transaction->user->first_name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-success">${{ number_format($transaction->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $transaction->payment_method == 'stripe' ? 'success' : 'info' }}">
                                            {{ $transaction->payment_method ?? 'wallet' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Modal -->
<div class="modal fade" id="analyticsModal" tabindex="-1" role="dialog" aria-labelledby="analyticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="analyticsModalLabel">Revenue Analytics</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Daily Revenue (Last 30 Days)</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" style="min-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Top Barber Earnings</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="barberEarningsChart" style="min-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barber Earnings Modal -->
<div class="modal fade" id="barberEarningsModal" tabindex="-1" role="dialog" aria-labelledby="barberEarningsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barberEarningsModalLabel">Barber Earnings Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="barberEarningsContent">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    $(document).ready(function() {
        let revenueChart, earningsChart;

        // View barber earnings details
        $('.view-barber-earnings').click(function() {
            var barberId = $(this).data('barber-id');
            var barberName = $(this).data('barber-name');
            
            $('#barberEarningsModalLabel').text('Earnings: ' + barberName);
            $('#barberEarningsModal').modal('show');
            
            // Show loading
            $('#barberEarningsContent').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Loading earnings data...</p>
                </div>
            `);
            
            $.ajax({
                url: "{{ url('/splitting/barber') }}/" + barberId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var html = `
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>$${response.stats.total_earnings.toFixed(2)}</h4>
                                            <small>Total Earnings</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>${response.stats.total_transactions}</h4>
                                            <small>Transactions</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>${response.stats.total_bookings}</h4>
                                            <small>Total Bookings</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>${response.stats.completion_rate}%</h4>
                                            <small>Completion Rate</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        if (response.transactions.length > 0) {
                            html += `
                                <h6>Recent Transactions</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Reason</th>
                                                <th>Method</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
                            
                            response.transactions.forEach(function(transaction) {
                                html += `
                                    <tr>
                                        <td>${new Date(transaction.created_at).toLocaleDateString()}</td>
                                        <td><span class="text-success">$${parseFloat(transaction.amount).toFixed(2)}</span></td>
                                        <td>${transaction.reason}</td>
                                        <td>
                                            <span class="badge badge-${transaction.payment_method == 'stripe' ? 'success' : 'info'}">
                                                ${transaction.payment_method || 'wallet'}
                                            </span>
                                        </td>
                                    </tr>
                                `;
                            });
                            
                            html += `
                                        </tbody>
                                    </table>
                                </div>
                            `;
                        } else {
                            html += `
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i>
                                    No transactions found for this barber.
                                </div>
                            `;
                        }
                        
                        $('#barberEarningsContent').html(html);
                    }
                }
            });
        });

        // Load analytics when modal opens
        $('#analyticsModal').on('show.bs.modal', function() {
            loadAnalyticsData();
        });

        function loadAnalyticsData() {
            $.ajax({
                url: "{{ route('splitting.analytics') }}",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        initializeRevenueChart(response.daily_revenue);
                        initializeEarningsChart(response.barber_earnings);
                    }
                }
            });
        }

        function initializeRevenueChart(dailyData) {
            var ctx = document.getElementById('revenueChart').getContext('2d');
            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dailyData.map(item => item.date),
                    datasets: [{
                        label: 'Daily Revenue ($)',
                        data: dailyData.map(item => item.revenue),
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
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
        }

        function initializeEarningsChart(barberData) {
            var ctx = document.getElementById('barberEarningsChart').getContext('2d');
            earningsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: barberData.map(item => item.first_name + ' ' + item.last_name),
                    datasets: [{
                        label: 'Earnings ($)',
                        data: barberData.map(item => item.total_earnings),
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
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
        }
    });
</script>
@endpush