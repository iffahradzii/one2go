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
        $pendingBookings = DB::table('payments')
            ->where('payment_status', 'pending')
            ->count();

        // Calculate total revenue from paid bookings
        $totalRevenue = DB::table('payments')
            ->where('payment_status', 'paid')
            ->sum('amount');

        $totalCustomers = User::count();

        $currentMonth = Carbon::now()->month; 

        return view('admin.dashboard', compact(
            'totalBookings',
            'pendingBookings',
            'totalRevenue',
            'totalCustomers',
            'currentMonth',
        ));
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
        $topDestinations = DB::table('bookings as b')
            ->join('travel_packages as tp', 'b.travel_package_id', '=', 'tp.id')
            ->join('payments as p', 'p.booking_id', '=', 'b.id')
            ->whereMonth('p.created_at', $month)
            ->whereYear('p.created_at', Carbon::now()->year)
            ->where('p.payment_status', 'paid')
            ->select('tp.name', DB::raw('count(*) as total_bookings'))
            ->groupBy('tp.name')
            ->orderByDesc('total_bookings')
            ->limit(5)
            ->get();

        if ($topDestinations->isEmpty()) {
            return [
                'labels' => ['No Data Available'],
                'data' => [0]
            ];
        }

        return [
            'labels' => $topDestinations->pluck('name')->toArray(),
            'data' => $topDestinations->pluck('total_bookings')->toArray()
        ];
    }

    private function getCountryDataForMonth($month)
    {
        $countries = ['Thailand', 'Vietnam', 'South Korea', 'Indonesia'];
        $countryData = [];

        foreach ($countries as $country) {
            // Count regular bookings
            $bookings = DB::table('bookings as b')
                ->join('travel_packages as tp', 'b.travel_package_id', '=', 'tp.id')
                ->join('payments as p', 'p.booking_id', '=', 'b.id')
                ->whereMonth('p.created_at', $month)
                ->whereYear('p.created_at', Carbon::now()->year)
                ->where('p.payment_status', 'paid')
                ->where('tp.country', $country)
                ->count();

            // Count private bookings
            $privateBookings = DB::table('private_bookings as pb')
                ->join('travel_packages as tp', 'pb.travel_package_id', '=', 'tp.id')
                ->join('payments as p', 'p.booking_id', '=', 'pb.id')
                ->whereMonth('p.created_at', $month)
                ->whereYear('p.created_at', Carbon::now()->year)
                ->where('p.payment_status', 'paid')
                ->where('tp.country', $country)
                ->count();

            $countryData[$country] = $bookings + $privateBookings;
        }

        // If no data for any country, return default
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
}
