<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tranasaction;
use App\Models\User;
use App\Models\Booking;
use Carbon\Carbon;

class SplittingController extends Controller
{
    /**
     * Display revenue distribution overview.
     */
    public function index()
    {
        try {
            // Actual data from transactions and bookings
            $revenueStats = $this->getRevenueStatistics();
            $topBarbers = $this->getTopBarbers();
            $recentTransactions = $this->getRecentTransactions();

            return view('admin.splitting.index', compact(
                'revenueStats', 
                'topBarbers',
                'recentTransactions'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading revenue data: ' . $e->getMessage());
        }
    }

    /**
     * Get actual revenue statistics from database.
     */
    private function getRevenueStatistics()
    {
        // Total revenue from all credit transactions
        $totalRevenue = Tranasaction::where('type', 'debit')->sum('amount');
        
        // Today's revenue
        $todayRevenue = Tranasaction::where('type', 'debit')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        // This month revenue
        $monthRevenue = Tranasaction::where('type', 'debit')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // Total bookings count
        $totalBookings = Booking::count();
        
        // Completed bookings
        $completedBookings = Booking::where('status', 'completed')->count();

        // Active barbers with transactions
       $activeBarbers = User::where('role', 'barber')
            ->where('status', 'active')
            ->whereHas('transaction', function($query) {
                $query->where('type', 'debit');
            })
            ->count();

        return [
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'month_revenue' => $monthRevenue,
            'total_bookings' => $totalBookings,
            'completed_bookings' => $completedBookings,
            'active_barbers' => $activeBarbers,
            'avg_booking_value' => $totalBookings > 0 ? $totalRevenue / $totalBookings : 0
        ];
    }

    /**
     * Get top earning barbers based on actual transactions.
     */
    private function getTopBarbers()
    {
        return User::where('role', 'barber')
            ->withSum(['transaction as total_earnings' => function($query) {
                $query->where('type', 'debit');
            }], 'amount')
            ->withCount(['bookings as total_bookings'])
            ->withCount(['bookings as completed_bookings' => function($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('total_earnings', 'desc')
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'phone']);
    }

    /**
     * Get recent payment transactions.
     */
    private function getRecentTransactions()
    {
        return Tranasaction::with(['user', 'booking'])
            ->where('type', 'credit')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get revenue analytics for charts.
     */
    public function getAnalytics()
    {
        try {
            // Daily revenue for last 30 days
            $dailyRevenue = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $revenue = Tranasaction::where('type', 'debit')
                    ->whereDate('created_at', $date)
                    ->sum('amount');
                
                $dailyRevenue[] = [
                    'date' => $date->format('M d'),
                    'revenue' => $revenue
                ];
            }

            // Barber earnings distribution
            $barberEarnings = User::where('role', 'barber')
                ->withSum(['transaction as total_earnings' => function($query) {
                    $query->where('type', 'debit');
                }], 'amount')
                ->having('total_earnings', '>', 0)
                ->orderBy('total_earnings', 'desc')
                ->limit(10)
                ->get(['id', 'first_name', 'last_name', 'total_earnings']);

            return response()->json([
                'success' => true,
                'daily_revenue' => $dailyRevenue,
                'barber_earnings' => $barberEarnings
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get barber detailed earnings.
     */
    public function getBarberEarnings($barberId)
    {
        try {
            $barber = User::with(['transaction' => function($query) {
                $query->where('type', 'credit')->orderBy('created_at', 'desc');
            }])
            ->withCount(['bookings as total_bookings'])
            ->withCount(['bookings as completed_bookings' => function($query) {
                $query->where('status', 'completed');
            }])
            ->findOrFail($barberId);

            $earningsStats = [
                'total_earnings' => $barber->transaction->sum('amount'),
                'total_transactions' => $barber->transaction->count(),
                'total_bookings' => $barber->total_bookings,
                'completed_bookings' => $barber->completed_bookings,
                'completion_rate' => $barber->total_bookings > 0 ? 
                    round(($barber->completed_bookings / $barber->total_bookings) * 100, 2) : 0
            ];

            return response()->json([
                'success' => true,
                'barber' => $barber,
                'stats' => $earningsStats,
                'transactions' => $barber->transaction->take(10)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading barber earnings: ' . $e->getMessage()
            ], 500);
        }
    }
}