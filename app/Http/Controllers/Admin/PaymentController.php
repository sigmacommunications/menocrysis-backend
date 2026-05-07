<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tranasaction;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display payments overview.
     */
    public function index(Request $request)
    {

            $query = Tranasaction::with('user')
                ->where('type', 'debit') // Only show credit transactions (payments)
                ->orderBy('created_at', 'desc');

            // Search functionality
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%$search%")
                      ->orWhere('amount', 'like', "%$search%")
                      ->orWhere('reason', 'like', "%$search%")
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                      });
                });
            }

            // Filter by date
            if ($request->has('date_range') && $request->date_range != '') {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }

            

            $payments = $query->paginate(20);

            // Get payment statistics
            $paymentStats = $this->getPaymentStatistics($request);

            return view('admin.payments.index', compact('payments', 'paymentStats'));
            
        
    }

    /**
     * Get payment statistics.
     */
    private function getPaymentStatistics($request)
    {
        $baseQuery = Tranasaction::where('type', 'credit');

        // Apply date filter if present
        if ($request->has('date_range') && $request->date_range != '') {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                $baseQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        $totalRevenue = $baseQuery->sum('amount');
        $totalTransactions = $baseQuery->count();
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Today's stats
        $todayRevenue = Tranasaction::where('type', 'debit')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        // This month stats
        $monthRevenue = Tranasaction::where('type', 'debit')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // Payment methods distribution
        $paymentMethods = Tranasaction::where('type', 'debit')
            ->select(\DB::raw('COUNT(*) as count'), \DB::raw('SUM(amount) as amount'))
            ->get();

        return [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'average_transaction' => $averageTransaction,
            'today_revenue' => $todayRevenue,
            'month_revenue' => $monthRevenue,
            'payment_methods' => $paymentMethods
        ];
    }

    /**
     * Get payment statistics for charts.
     */
    public function getStats(Request $request)
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

            // Monthly revenue for last 12 months
            $monthlyRevenue = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $revenue = Tranasaction::where('type', 'debit')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');
                
                $monthlyRevenue[] = [
                    'month' => $date->format('M Y'),
                    'revenue' => $revenue
                ];
            }

            // Top earning barbers
           $topBarbers = User::where('role', 'barber')
                ->withSum(['transaction as total_earnings' => function($query) {
                    $query->where('type', 'debit');
                }], 'amount')
                ->withCount(['transaction as transaction_count'])
                ->orderBy('total_earnings', 'desc')
                ->limit(10)
                ->get(['id', 'first_name', 'last_name', 'email']);

            return response()->json([
                'success' => true,
                'daily_revenue' => $dailyRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'top_barbers' => $topBarbers
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        try {
            $payment = Tranasaction::with(['user', 'booking'])->findOrFail($id);
            return view('admin.payments.show', compact('payment'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment not found: ' . $e->getMessage());
        }
    }

    /**
     * Export payments to CSV.
     */
    public function export(Request $request)
    {
        try {
            $query = Tranasaction::with('user')
                ->where('type', 'credit')
                ->orderBy('created_at', 'desc');

            // Apply filters if present
            if ($request->has('date_range') && $request->date_range != '') {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }

            $payments = $query->get();

            $fileName = 'payments_export_' . date('Y_m_d_H_i_s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $fileName,
            ];

            $callback = function() use ($payments) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'Transaction ID',
                    'Date',
                    'Customer Name',
                    'Customer Email',
                    'Amount',
                    'Payment Method',
                    'Reason',
                    'Status'
                ]);

                // Add data rows
                foreach ($payments as $payment) {
                    fputcsv($file, [
                        $payment->id,
                        $payment->created_at->format('Y-m-d H:i:s'),
                        $payment->user ? $payment->user->first_name . ' ' . $payment->user->last_name : 'N/A',
                        $payment->user ? $payment->user->email : 'N/A',
                        '$' . number_format($payment->amount, 2),
                        $payment->payment_method ?? 'N/A',
                        $payment->reason,
                        'Completed'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting payments: ' . $e->getMessage());
        }
    }
}