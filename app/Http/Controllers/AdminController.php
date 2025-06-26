<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TravelPackage;
use App\Models\Booking;
use App\Models\Faq;
use App\Models\PrivateBooking;
use App\Models\Payment; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get total counts (including both regular and private bookings)
        $totalBookings = DB::table('bookings')->count() + DB::table('private_bookings')->count();
        
        // Get pending bookings count
        $pendingBookings = DB::table('payments as p')
            ->where('p.payment_status', 'pending')
            ->count();

        // Calculate total revenue from paid bookings
        $totalRevenue = DB::table('payments')
            ->where('payment_status', 'paid')
            ->sum('amount');

        $totalCustomers = User::count();
        
        // Get monthly revenue data for the chart
        $monthlyRevenue = DB::table('payments')
            ->where('payment_status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw("strftime('%m', created_at) as month, SUM(amount) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // Initialize revenue data array with zeros
        $revenueData = array_fill(0, 12, 0);
        
        // Fill in the actual revenue data
        foreach ($monthlyRevenue as $revenue) {
            // Convert month string to integer for array index
            $revenueData[(int)$revenue->month - 1] = (float)$revenue->revenue;
        }
        
        // Get trip status data
        $pendingTrips = DB::table('payments as p')
            ->where('p.payment_status', 'pending')
            ->count();

        $paidTrips = DB::table('payments as p')
            ->leftJoin('bookings as b', 'p.booking_id', '=', 'b.id')
            ->leftJoin('private_bookings as pb', 'p.private_booking_id', '=', 'pb.id')
            ->where('p.payment_status', 'paid')
            ->where(function($query) {
                $query->whereDate('b.available_date', '>=', Carbon::now())
                      ->orWhereDate('pb.available_date', '>=', Carbon::now());
            })
            ->count();

        $completedTrips = DB::table('payments as p')
            ->leftJoin('bookings as b', 'p.booking_id', '=', 'b.id')
            ->leftJoin('private_bookings as pb', 'p.private_booking_id', '=', 'pb.id')
            ->where('p.payment_status', 'complete')
            
            ->count();

        $canceledTrips = DB::table('payments')
            ->where('payment_status', 'cancelled')
            ->count();
        
        $totalTrips = $pendingTrips + $paidTrips + $completedTrips + $canceledTrips;
        $pendingPercentage = $totalTrips > 0 ? round(($pendingTrips / $totalTrips) * 100) : 0;
        $paidPercentage = $totalTrips > 0 ? round(($paidTrips / $totalTrips) * 100) : 0;
        $completedPercentage = $totalTrips > 0 ? round(($completedTrips / $totalTrips) * 100) : 0;
        $canceledPercentage = $totalTrips > 0 ? round(($canceledTrips / $totalTrips) * 100) : 0;
        
        // Get recent bookings
        $recentBookings = DB::table('payments as p')
            ->leftJoin('bookings as b', 'p.booking_id', '=', 'b.id')
            ->leftJoin('private_bookings as pb', 'p.private_booking_id', '=', 'pb.id')
            ->leftJoin('users as u', function($join) {
                $join->on('b.user_id', '=', 'u.id')
                     ->orOn('pb.user_id', '=', 'u.id');
            })
            ->leftJoin('travel_packages as tp1', 'b.travel_package_id', '=', 'tp1.id')
            ->leftJoin('travel_packages as tp2', 'pb.travel_package_id', '=', 'tp2.id')
            ->select(
                'u.name as user_name',
                DB::raw('COALESCE(tp1.name, tp2.name) as package_name'),
                DB::raw('CASE WHEN pb.id IS NOT NULL THEN "Private" ELSE "General" END as booking_type'),
                'p.created_at as available_date',
                'p.payment_status'
            )
            ->orderBy('p.created_at', 'desc')
            ->take(5)
            ->get();
    
    
            
        // Calculate growth rates
        $lastMonthBookings = DB::table('payments')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();
        $bookingGrowth = $lastMonthBookings > 0 ? round((($totalBookings - $lastMonthBookings) / $lastMonthBookings) * 100) : 0;
        
        $lastMonthRevenue = DB::table('payments')
            ->where('payment_status', 'paid')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->sum('amount');
        $revenueGrowth = $lastMonthRevenue > 0 ? round((($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100) : 0;
        
        $lastMonthCustomers = User::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        $customerGrowth = $lastMonthCustomers > 0 ? round((($totalCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100) : 0;
        
        $lastWeekPendingBookings = DB::table('payments')
            ->where('payment_status', 'pending')
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->count();
        $pendingGrowth = $lastWeekPendingBookings > 0 ? round((($pendingBookings - $lastWeekPendingBookings) / $lastWeekPendingBookings) * 100) : 0;
        
        // Get current month for default chart data
        $currentMonth = Carbon::now()->month;
        
        // Get data for most booked destinations chart for current month
        $destinationData = $this->getDestinationDataForMonth($currentMonth);
        $destinationLabels = $destinationData['labels'];
        $destinationDataValues = $destinationData['data'];
        
        // Get data for country chart for current month
        $countryData = $this->getCountryDataForMonth($currentMonth);
        $countryLabels = $countryData['labels'];
        $countryDataValues = $countryData['data'];

        return view('admin.dashboard', compact(
            'totalBookings', 'totalRevenue', 'totalCustomers', 'pendingBookings',
            'revenueData', 'destinationLabels', 'destinationDataValues',
            'countryLabels', 'countryDataValues',
            'completedTrips', 'pendingTrips' , 'paidTrips' , 'canceledTrips',
            'completedPercentage', 'pendingPercentage','paidPercentage', 'canceledPercentage',
            'recentBookings', 'bookingGrowth', 'revenueGrowth', 'customerGrowth', 'pendingGrowth',
            'currentMonth'
        ));
    }
    
    public function getChartsData(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        
        $destinationData = $this->getDestinationDataForMonth($month);
        $countryData = $this->getCountryDataForMonth($month);
        
        return response()->json([
            'destinationLabels' => $destinationData['labels'],
            'destinationData' => $destinationData['data'],
            'countryLabels' => $countryData['labels'],
            'countryData' => $countryData['data']
        ]);
    }
    
    private function getDestinationDataForMonth($month)
    {
        // Get regular bookings by travel package name
        $regular = DB::table('bookings as b')
            ->join('travel_packages as tp', 'b.travel_package_id', '=', 'tp.id')
            ->join('payments as p', 'p.booking_id', '=', 'b.id')
            ->where('p.payment_status', 'paid');
    
        // Add month filter only if not viewing overall data
        if ($month !== 'overall') {
            $regular->whereMonth('b.available_date', $month)
                    ->whereYear('b.available_date', Carbon::now()->year);
        }
    
        $regular->select('tp.name as destination', DB::raw('count(*) as total'))
                ->groupBy('tp.name');
    
        // Get private bookings by travel package name
        $private = DB::table('private_bookings as pb')
            ->join('travel_packages as tp', 'pb.travel_package_id', '=', 'tp.id')
            ->join('payments as p', 'p.private_booking_id', '=', 'pb.id')
            ->where('p.payment_status', 'paid');
    
        // Add month filter only if not viewing overall data
        if ($month !== 'overall') {
            $private->whereMonth('pb.available_date', $month)
                    ->whereYear('pb.available_date', Carbon::now()->year);
        }
    
        $private->select('tp.name as destination', DB::raw('count(*) as total'))
                ->groupBy('tp.name');
    
        // Rest of the method remains the same
        $combined = DB::table(DB::raw("({$regular->toSql()} UNION ALL {$private->toSql()}) as combined"))
            ->mergeBindings($regular)
            ->mergeBindings($private)
            ->select('destination', DB::raw('SUM(total) as total_bookings'))
            ->groupBy('destination')
            ->orderByDesc('total_bookings')
            ->limit(5)
            ->get();
    
        if ($combined->isEmpty()) {
            return [
                'labels' => ['No Data Available'],
                'data' => [0]
            ];
        }
    
        return [
            'labels' => $combined->pluck('destination')->toArray(),
            'data' => $combined->pluck('total_bookings')->toArray()
        ];
    }

    private function getCountryDataForMonth($month)
    {
        $countries = DB::table('travel_packages')
            ->distinct()
            ->pluck('country')
            ->toArray();
    
        $countryData = [];
    
        foreach ($countries as $country) {
            // Count regular bookings for country
            $bookings = DB::table('bookings as b')
                ->join('travel_packages as tp', 'b.travel_package_id', '=', 'tp.id')
                ->join('payments as p', 'p.booking_id', '=', 'b.id')
                ->where('p.payment_status', 'paid')
                ->where('tp.country', $country);
    
            // Add month filter only if not viewing overall data
            if ($month !== 'overall') {
                $bookings->whereMonth('b.available_date', $month)
                        ->whereYear('b.available_date', Carbon::now()->year);
            }
    
            $bookingsCount = $bookings->count();
    
            // Count private bookings for country
            $privateBookings = DB::table('private_bookings as pb')
                ->join('travel_packages as tp', 'pb.travel_package_id', '=', 'tp.id')
                ->join('payments as p', 'p.private_booking_id', '=', 'pb.id')
                ->where('p.payment_status', 'paid')
                ->where('tp.country', $country);
    
            // Add month filter only if not viewing overall data
            if ($month !== 'overall') {
                $privateBookings->whereMonth('pb.available_date', $month)
                          ->whereYear('pb.available_date', Carbon::now()->year);
            }
    
            $privateBookingsCount = $privateBookings->count();
    
            $countryData[$country] = $bookingsCount + $privateBookingsCount;
        }
    
        // Rest of the method remains the same
        if (array_sum($countryData) === 0) {
            return [
                'labels' => $countries,
                'data' => array_fill(0, count($countries), 0)
            ];
        }
    
        return [
            'labels' => array_keys($countryData),
            'data' => array_values($countryData)
        ];
    }

    


    public function getRevenueData(Request $request)
    {
        $month = $request->input('month', 0);

        $query = DB::table('payments')
            ->where('payment_status', 'paid')
            ->whereYear('created_at', Carbon::now()->year);

        if ($month > 0) {
            $query->whereMonth('created_at', $month);
        }

        $monthlyRevenue = $query
            ->selectRaw("strftime('%m', created_at) as month, SUM(amount) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Initialize revenue data array with zeros
        $revenueData = array_fill(0, 12, 0);

        // Fill in the actual revenue data
        foreach ($monthlyRevenue as $revenue) {
            $revenueData[(int)$revenue->month - 1] = (float)$revenue->revenue;
        }

        return response()->json(['revenueData' => $revenueData]);
    }


}
