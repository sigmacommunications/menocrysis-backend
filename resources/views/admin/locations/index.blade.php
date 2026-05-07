@extends('admin.layouts.master')
@section('title')
Location Analytics
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Location Analytics</h1>
            <p class="text-muted">Geographical analysis of barbers and bookings</p>
        </div>
        <div class="col-sm-6 text-right">
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#mapModal">
                <i class="fas fa-map-marked-alt"></i> View Map
            </button>
            <button type="button" class="btn btn-success" id="refreshData">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $locationStats['total_barbers'] }}</h3>
                    <p>Total Barbers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $locationStats['barbers_with_location'] }}</h3>
                    <p>Barbers with Location</p>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $locationStats['cities_with_barbers'] }}</h3>
                    <p>Cities Covered</p>
                </div>
                <div class="icon">
                    <i class="fas fa-city"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $locationStats['location_coverage_percentage'] }}<sup style="font-size: 20px">%</sup></h3>
                    <p>Location Coverage</p>
                </div>
                <div class="icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Popular Locations -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Popular Locations (Barber Density)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Barbers</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($popularLocations as $location)
                                <tr>
                                    <td>
                                        <strong>{{ $location['location'] }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $location['barber_count'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $location['bookings_count'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">${{ number_format($location['revenue'], 2) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Performance -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Location Performance</h3>
                </div>
                <div class="card-body">
                    <canvas id="locationRevenueChart" style="min-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Area-wise Bookings -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Area-wise Bookings Analysis</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Barbers</th>
                                    <th>Total Bookings</th>
                                    <th>Completed</th>
                                    <th>Pending</th>
                                    <th>Completion Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($areaBookings as $area)
                                <tr>
                                    <td>
                                        <strong>{{ $area['location'] }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $area['barber_count'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $area['total_bookings'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $area['completed_bookings'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ $area['pending_bookings'] }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $area['completion_rate'] > 70 ? 'success' : ($area['completion_rate'] > 40 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" style="width: {{ $area['completion_rate'] }}%" 
                                                 aria-valuenow="{{ $area['completion_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $area['completion_rate'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-barbers" 
                                                data-location="{{ $area['location'] }}">
                                            <i class="fas fa-eye"></i> View Barbers
                                        </button>
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

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Barbers Location Map</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Barbers by Location Modal -->
<div class="modal fade" id="barbersModal" tabindex="-1" role="dialog" aria-labelledby="barbersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barbersModalLabel">Barbers in <span id="locationTitle"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="barbersContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Loading barbers...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    #map { 
        height: 500px; 
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    .leaflet-popup-content {
        margin: 8px 12px;
    }
    .progress {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    $(document).ready(function() {
        let map, revenueChart;

        // Initialize Revenue Chart
        function initializeRevenueChart() {
            $.ajax({
                url: "{{ route('locations.revenue') }}",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var ctx = document.getElementById('locationRevenueChart').getContext('2d');
                        revenueChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: response.revenue_data.map(item => item.location),
                                datasets: [{
                                    label: 'Revenue ($)',
                                    data: response.revenue_data.map(item => item.revenue),
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
                }
            });
        }

        // Initialize Map
        function initializeMap() {
            map = L.map('map').setView([20.5937, 78.9629], 5); // Default to India center
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Load barber markers
            $.ajax({
                url: "{{ route('locations.map-data') }}",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        response.barbers.forEach(function(barber) {
                            var marker = L.marker([barber.lat, barber.lng]).addTo(map);
                            
                            var popupContent = `
                                <div style="min-width: 200px;">
                                    <h6><strong>${barber.name}</strong></h6>
                                    <p><i class="fas fa-map-marker-alt"></i> ${barber.location}</p>
                                    <p><i class="fas fa-phone"></i> ${barber.phone || 'N/A'}</p>
                                    <p><i class="fas fa-calendar-check"></i> ${barber.bookings_count} bookings</p>
                                    <p><i class="fas fa-star"></i> ${barber.rating}</p>
                                    <a href="${barber.profile_url}" target="_blank" class="btn btn-sm btn-primary btn-block">
                                        View Profile
                                    </a>
                                </div>
                            `;
                            
                            marker.bindPopup(popupContent);
                        });

                        // Adjust map view to show all markers
                        if (response.barbers.length > 0) {
                            var group = new L.featureGroup(response.barbers.map(b => L.marker([b.lat, b.lng])));
                            map.fitBounds(group.getBounds().pad(0.1));
                        }
                    }
                }
            });
        }

        // View barbers by location
        $('.view-barbers').click(function() {
            var location = $(this).data('location');
            $('#locationTitle').text(location);
            $('#barbersModal').modal('show');
            
            $.ajax({
                url: "{{ url('/location/barbers') }}/" + encodeURIComponent(location),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var html = `
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>${response.stats.total_barbers}</h4>
                                            <small>Total Barbers</small>
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
                                            <h4>${response.stats.average_rating.toFixed(1)}</h4>
                                            <small>Avg Rating</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4>$${response.stats.total_revenue.toFixed(2)}</h4>
                                            <small>Total Revenue</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Barber Name</th>
                                            <th>Bookings</th>
                                            <th>Rating</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        
                        response.barbers.forEach(function(barber) {
                            html += `
                                <tr>
                                    <td>${barber.first_name} ${barber.last_name}</td>
                                    <td><span class="badge badge-info">${barber.bookings_count}</span></td>
                                    <td>
                                        <span class="badge badge-${barber.reviews_avg_rating >= 4 ? 'success' : barber.reviews_avg_rating >= 3 ? 'warning' : 'danger'}">
                                            ${barber.reviews_avg_rating ? barber.reviews_avg_rating.toFixed(1) : 'N/A'}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/users/${barber.id}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                        
                        $('#barbersContent').html(html);
                    }
                }
            });
        });

        // Refresh data
        $('#refreshData').click(function() {
            location.reload();
        });

        // Initialize map when modal opens
        $('#mapModal').on('show.bs.modal', function() {
            setTimeout(function() {
                if (!map) {
                    initializeMap();
                } else {
                    map.invalidateSize();
                }
            }, 500);
        });

        // Initialize charts
        initializeRevenueChart();
    });
</script>
@endpush