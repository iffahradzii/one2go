protected function schedule(Schedule $schedule)
{
    // ... existing code ...
    
    // Run daily at midnight
    $schedule->command('bookings:cancel-expired')->daily();
}