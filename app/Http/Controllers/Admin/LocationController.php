<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Tranasaction;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    /**
     * Display location analytics dashboard.
     */
    public function index(Request $request)
    {
        try {
            // Get all active barbers with locations
            $barbers = User::where('role', 'barber')
                ->where('status', 'active')
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->get(['id', 'first_name', 'last_name', 'lat', 'lng', 'location', 'created_at']);
            
            // Get location statistics
            $locationStats = $this->getLocationStatistics();
            
            // Get popular locations
            $popularLocations = $this->getPopularLocations();
            
            // Get area-wise bookings
            $areaBookings = $this->getAreaWiseBookings();
            
            return view('admin.locations.index', compact(
                'barbers', 
                'locationStats', 
                'popularLocations',
                'areaBookings'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading location analytics: ' . $e->getMessage());
        }
    }

    /**
     * Get location statistics
     */
    private function getLocationStatistics()
    {
        $totalBarbers = User::where('role', 'barber')->where('status', 'active')->count();
        $barbersWithLocation = User::where('role', 'barber')
            ->where('status', 'active')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->count();
        
        $barbersWithoutLocation = $totalBarbers - $barbersWithLocation;
        
        // Get unique cities from barber locations
        $citiesWithBarbers = User::where('role', 'barber')
            ->where('status', 'active')
            ->whereNotNull('location')
            ->distinct()
            ->count('location');
        
        // Get total bookings from locations with barbers
        $totalBookings = Booking::count();
        $bookingsWithLocation = Booking::whereHas('barber_info', function($query) {
            $query->whereNotNull('lat')->whereNotNull('lng');
        })->count();
        
        return [
            'total_barbers' => $totalBarbers,
            'barbers_with_location' => $barbersWithLocation,
            'barbers_without_location' => $barbersWithoutLocation,
            'cities_with_barbers' => $citiesWithBarbers,
            'total_bookings' => $totalBookings,
            'bookings_with_location' => $bookingsWithLocation,
            'location_coverage_percentage' => $totalBarbers > 0 ? round(($barbersWithLocation / $totalBarbers) * 100, 2) : 0
        ];
    }

    /**
     * Get popular locations based on barber density
     */
    private function getPopularLocations()
    {
        $locations = User::where('role', 'barber')
            ->where('status', 'active')
            ->whereNotNull('location')
            ->select('location', DB::raw('COUNT(*) as barber_count'))
            ->groupBy('location')
            ->orderBy('barber_count', 'desc')
            ->limit(10)
            ->get();

        return $locations->map(function($item) {
            // Get bookings count for this location
            $bookingsCount = Booking::whereHas('barber_info', function($query) use ($item) {
                $query->where('location', $item->location);
            })->count();

            // Get revenue for this location
            $revenue = Tranasaction::whereHas('user', function($query) use ($item) {
                $query->where('location', $item->location);
            })->where('type', 'credit')->sum('amount');

            return [
                'location' => $item->location ?: 'Unknown',
                'barber_count' => $item->barber_count,
                'bookings_count' => $bookingsCount,
                'revenue' => $revenue ?: 0
            ];
        });
    }

    /**
     * Get area-wise bookings analysis
     */
    private function getAreaWiseBookings()
    {
        // Get locations with barber count
        $locations = User::where('role', 'barber')
            ->where('status', 'active')
            ->whereNotNull('location')
            ->select('location', DB::raw('COUNT(*) as barber_count'))
            ->groupBy('location')
            ->orderBy('barber_count', 'desc')
            ->limit(15)
            ->get();

        $areaBookings = [];

        foreach ($locations as $location) {
            // Get bookings data for this location using join
            $bookingsData = DB::table('bookings')
                ->join('users', 'bookings.barber_id', '=', 'users.id')
                ->where('users.location', $location->location)
                ->select(
                    DB::raw('COUNT(*) as total_bookings'),
                    DB::raw('SUM(CASE WHEN bookings.status = "completed" THEN 1 ELSE 0 END) as completed_bookings'),
                    DB::raw('SUM(CASE WHEN bookings.status = "pending" THEN 1 ELSE 0 END) as pending_bookings')
                )
                ->first();

            $totalBookings = $bookingsData->total_bookings ?? 0;
            $completedBookings = $bookingsData->completed_bookings ?? 0;
            $pendingBookings = $bookingsData->pending_bookings ?? 0;

            $completionRate = $totalBookings > 0 ? 
                round(($completedBookings / $totalBookings) * 100, 2) : 0;

            $areaBookings[] = [
                'location' => $location->location ?: 'Unknown',
                'barber_count' => $location->barber_count,
                'total_bookings' => $totalBookings,
                'completed_bookings' => $completedBookings,
                'pending_bookings' => $pendingBookings,
                'completion_rate' => $completionRate
            ];
        }

        // Sort by total bookings descending
        usort($areaBookings, function($a, $b) {
            return $b['total_bookings'] - $a['total_bookings'];
        });

        return $areaBookings;
    }

    /**
     * Get barbers by location
     */
    public function getBarbersByLocation($location)
    {
        try {
            // Get barbers with bookings count using subquery
            $barbers = User::where('role', 'barber')
                ->where('status', 'active')
                ->where('location', 'like', "%$location%")
                ->get(['id', 'first_name', 'last_name', 'email', 'phone', 'location']);

            // Add bookings count and ratings to each barber
            $barbersWithStats = $barbers->map(function($barber) {
                $bookingsCount = Booking::where('barber_id', $barber->id)->count();
                $avgRating = Review::where('barber_id', $barber->id)->avg('rating');
                
                $barber->bookings_count = $bookingsCount;
                $barber->reviews_avg_rating = $avgRating ? round($avgRating, 1) : null;
                
                return $barber;
            });

            // Calculate location statistics
            $totalBookings = $barbersWithStats->sum('bookings_count');
            $totalRevenue = Tranasaction::whereIn('user_id', $barbersWithStats->pluck('id'))
                ->where('type', 'credit')
                ->sum('amount');

            // Calculate average rating
            $ratings = $barbersWithStats->pluck('reviews_avg_rating')->filter()->toArray();
            $averageRating = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : 0;

            $locationStats = [
                'total_barbers' => $barbersWithStats->count(),
                'total_bookings' => $totalBookings,
                'average_rating' => round($averageRating, 1),
                'total_revenue' => $totalRevenue
            ];
            
            return response()->json([
                'success' => true,
                'barbers' => $barbersWithStats,
                'stats' => $locationStats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading barbers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get location-wise revenue
     */
    public function getLocationRevenue()
    {
        try {
            // Get revenue by location using join
            $revenueData = DB::table('users')
                ->join('transaction', 'users.id', '=', 'transaction.user_id')
                ->where('users.role', 'barber')
                ->where('users.status', 'active')
                ->whereNotNull('users.location')
                ->where('transaction.type', 'debit')
                ->select(
                    'users.location',
                    DB::raw('SUM(transaction.amount) as total_revenue')
                )
                ->groupBy('users.location')
                ->orderBy('total_revenue', 'desc')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'location' => $item->location ?: 'Unknown',
                        'revenue' => $item->total_revenue ?: 0
                    ];
                });
            
            return response()->json([
                'success' => true,
                'revenue_data' => $revenueData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading revenue data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get map data for barbers
     */
    public function getMapData()
    {
        try {
            $barbers = User::where('role', 'barber')
                ->where('status', 'active')
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->get(['id', 'first_name', 'last_name', 'lat', 'lng', 'location', 'phone']);

            $barbersData = $barbers->map(function($barber) {
                // Get bookings count
                $bookingsCount = Booking::where('barber_id', $barber->id)->count();
                
                // Get average rating
                $avgRating = Review::where('barber_id', $barber->id)->avg('rating');

                return [
                    'id' => $barber->id,
                    'name' => $barber->first_name . ' ' . $barber->last_name,
                    'location' => $barber->location,
                    'lat' => floatval($barber->lat),
                    'lng' => floatval($barber->lng),
                    'phone' => $barber->phone,
                    'bookings_count' => $bookingsCount,
                    'rating' => $avgRating ? round($avgRating, 1) : 'No ratings',
                    'profile_url' => route('users.show', $barber->id)
                ];
            });
            
            return response()->json([
                'success' => true,
                'barbers' => $barbersData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading map data: ' . $e->getMessage()
            ], 500);
        }
    }
}