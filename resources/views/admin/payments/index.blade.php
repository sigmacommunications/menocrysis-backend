@extends('admin.layouts.master')
@section('title')
Total Payments
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Total Payments</h1>
            <p class="text-muted">Complete payment history and revenue analytics</p>
        </div>
        <div class="col-sm-6 text-right">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#statsModal">
                <i class="fas fa-chart-bar"></i> Analytics
            </button>
            <a href="{{ route('payments.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
               class="btn btn-primary">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format($paymentStats['total_revenue'], 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($paymentStats['total_transactions']) }}</h3>
                    <p>Total Transactions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>${{ number_format($paymentStats['average_transaction'], 2) }}</h3>
                    <p>Avg. Transaction</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calculator"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>${{ number_format($paymentStats['today_revenue'], 2) }}</h3>
                    <p>Today's Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ number_format($paymentStats['month_revenue'], 2) }}</h3>
                    <p>Monthly Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $paymentStats['payment_methods']->count() }}</h3>
                    <p>Payment Methods</p>
                </div>
                <div class="icon">
                    <i class="fas fa-credit-card"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Distribution -->
    <div class="row">
        @foreach($paymentStats['payment_methods'] as $method)
        <div class="col-md-3 mb-3">
            <div class="info-box">
                <span class="info-box-icon bg-{{ $method->payment_method == 'stripe' ? 'success' : ($method->payment_method == 'wallet' ? 'info' : 'warning') }}">
                    <i class="fas fa-{{ $method->payment_method == 'stripe' ? 'credit-card' : ($method->payment_method == 'wallet' ? 'wallet' : 'money-bill') }}"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ ucfirst($method->payment_method) }}</span>
                    <span class="info-box-number">
                        ${{ number_format($method->amount, 2) }}
                    </span>
                    <small>{{ $method->count }} transactions</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Payment Transactions</h3>
            
            <!-- Search and Filter Form -->
            <div class="card-tools">
                <form action="{{ route('payments.index') }}" method="GET">
                    <div class="input-group input-group-sm" style="width: 400px;">
                        <input type="text" name="search" class="form-control float-right" placeholder="Search transactions..." value="{{ request('search') }}">
                        
                        <input type="text" name="date_range" class="form-control float-right" id="dateRangePicker" placeholder="Date range" value="{{ request('date_range') }}" style="width: 150px;">
                        
                        <select name="payment_method" class="form-control" style="width: 120px;">
                            <option value="">All Methods</option>
                            <option value="stripe" {{ request('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                            <option value="wallet" {{ request('payment_method') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        </select>
                        
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>#{{ $payment->id }}</td>
                        <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            @if($payment->user)
                                <strong>{{ $payment->user->first_name }} {{ $payment->user->last_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $payment->user->email }}</small>
                            @else
                                <span class="text-danger">User not found</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-success">${{ number_format($payment->amount, 2) }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-{{ $payment->payment_method == 'stripe' ? 'success' : ($payment->payment_method == 'wallet' ? 'info' : 'warning') }}">
                                {{ ucfirst($payment->payment_method ?? 'N/A') }}
                            </span>
                        </td>
                        <td>{{ $payment->reason }}</td>
                        <td>
                            <span class="badge badge-success">Completed</span>
                        </td>
                        <td>
                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-info btn-sm" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No payment transactions found.</p>
                            @if(request()->anyFilled(['search', 'date_range', 'payment_method']))
                                <a href="{{ route('payments.index') }}" class="btn btn-primary">Clear Filters</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer clearfix">
            {{ $payments->links('custom-pagination-links') }}
        </div>
    </div>
</div>

<!-- Analytics Modal -->
<div class="modal fade" id="statsModal" tabindex="-1" role="dialog" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statsModalLabel">Payment Analytics</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Daily Revenue (Last 30 Days)</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="dailyRevenueChart" style="min-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Monthly Revenue (Last 12 Months)</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyRevenueChart" style="min-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Top Earning Barbers</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="topBarbersTable">
                                <thead>
                                    <tr>
                                        <th>Barber Name</th>
                                        <th>Total Earnings</th>
                                        <th>Transactions</th>
                                        <th>Avg. Transaction</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/plugins/daterangepicker/daterangepicker.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('admin/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Date Range Picker
        $('#dateRangePicker').daterangepicker({
            opens: 'left',
            locale: {
                format: 'MM/DD/YYYY'
            }
        });

        let dailyChart, monthlyChart;

        // Load analytics when modal opens
        $('#statsModal').on('show.bs.modal', function() {
            loadAnalyticsData();
        });

        function loadAnalyticsData() {
            $.ajax({
                url: "{{ route('payments.stats') }}",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        initializeDailyChart(response.daily_revenue);
                        initializeMonthlyChart(response.monthly_revenue);
                        populateTopBarbers(response.top_barbers);
                    }
                }
            });
        }

        function initializeDailyChart(dailyData) {
            var ctx = document.getElementById('dailyRevenueChart').getContext('2d');
            dailyChart = new Chart(ctx, {
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

        function initializeMonthlyChart(monthlyData) {
            var ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
            monthlyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [{
                        label: 'Monthly Revenue ($)',
                        data: monthlyData.map(item => item.revenue),
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

        function populateTopBarbers(barbers) {
            var html = '';
            barbers.forEach(function(barber) {
                var avgTransaction = barber.transaction_count > 0 ? barber.total_earnings / barber.transaction_count : 0;
                html += `
                    <tr>
                        <td>
                            <strong>${barber.first_name} ${barber.last_name}</strong>
                            <br>
                            <small class="text-muted">${barber.email}</small>
                        </td>
                        <td><strong class="text-success">$${barber.total_earnings ? barber.total_earnings.toFixed(2) : '0.00'}</strong></td>
                        <td><span class="badge badge-info">${barber.transaction_count}</span></td>
                        <td>$${avgTransaction.toFixed(2)}</td>
                    </tr>
                `;
            });
            $('#topBarbersTable tbody').html(html);
        }
    });
</script>
@endpush