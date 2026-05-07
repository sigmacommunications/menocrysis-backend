<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminInfo;
use Validator;
use App\Models\User;
use App\Models\Booking;
use App\Models\Tranasaction;
use App\Models\Service;
use App\Models\Amenity;
use App\Models\Location;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function dashboard()
    {
        try {
            // Total Statistics
            $totalUsers = User::whereNot('role', 'admin')->count();
            $totalBarbers = User::where('role', 'barber')->count();
            $totalCustomers = User::where('role', 'customer')->count();
            
            // Booking Statistics
            $totalBookings = Booking::count();
            $todayBookings = Booking::whereDate('created_at', Carbon::today())->count();
            $pendingBookings = Booking::where('status', 'pending')->count();
            $completedBookings = Booking::where('status', 'completed')->count();
            
            // Revenue Statistics
            $totalRevenue = Tranasaction::where('type', 'debit')->sum('amount');
            $todayRevenue = Tranasaction::where('type', 'debit')
                ->whereDate('created_at', Carbon::today())
                ->sum('amount');
            $monthlyRevenue = Tranasaction::where('type', 'debit')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('amount');
            
            // Recent Activities
            $recentBookings = Booking::with(['member_info', 'barber_info'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            
            $recentTransactions = Tranasaction::with('user')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            
            // Chart Data - Monthly Revenue (Last 6 months)
            $monthlyRevenueData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $revenue = Tranasaction::where('type', 'debit')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount');
                
                $monthlyRevenueData[] = [
                    'month' => $month->format('M Y'),
                    'revenue' => $revenue
                ];
            }
            
            // Chart Data - Booking Status Distribution
            $bookingStatusData = [
                'pending' => Booking::where('status', 'waiting for approval')->count(),
                'confirmed' => Booking::where('status', 'accept')->count(),
                'completed' => Booking::where('status', 'complete')->count(),
                'cancelled' => Booking::where('status', 'reject')->count(),
            ];

            return view('admin.dashboard', compact(
                'totalUsers',
                'totalBarbers',
                'totalCustomers',
                'totalBookings',
                'todayBookings',
                'pendingBookings',
                'completedBookings',
                'totalRevenue',
                'todayRevenue',
                'monthlyRevenue',
                'recentBookings',
                'recentTransactions',
                'monthlyRevenueData',
                'bookingStatusData'
            ));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

   public function admin_info()
   {
      $admin =AdminInfo::first();
      return view('admin.info',compact('admin'));
   } 

   public function admin_info_post(Request $request)
   {
      try{
         $validator = Validator::make($request->all(),[
             'official_email' => 'required|email',
             'phone'=>'required|numeric'
         ]);
         if($validator->fails())
         {
             return redirect()->back()->with(['error'=>$validator->errors()->first()]); 
         }
         if($request->id != null)
         {
            $admin =AdminInfo::first();
            $admin->official_email = $request->official_email;
            $admin->phone = $request->phone;
            $admin->save();
         }else{
            $admin =new AdminInfo();
            $admin->official_email = $request->official_email;
            $admin->phone = $request->phone;
            $admin->save();
         }
         return redirect()->back()->with(['success'=>'Record Created Successfully']);
      
      }catch(\Exception $e){
            return redirect()->back()->with(['error'=>$e->getMessage()]);
        }
   }
}
