<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\PrivateBooking;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel all pending bookings where the travel date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $regularCount = Booking::cancelExpiredBookings();
        $privateCount = PrivateBooking::cancelExpiredBookings();
        
        $this->info("Cancelled {$regularCount} expired regular bookings.");
        $this->info("Cancelled {$privateCount} expired private bookings.");
        
        return Command::SUCCESS;
    }
}
