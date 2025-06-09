<?php
try {
    // Get database path from Laravel config
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    $databasePath = config('database.connections.sqlite.database');
    
    // Connect to SQLite database
    $db = new PDO('sqlite:' . $databasePath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Disable foreign key constraints
    $db->exec('PRAGMA foreign_keys = OFF');
    
    // Tables to clear
    $tables = [
        "payments",
        "bookings",
        "booking_travelers",
        "private_bookings",
        "private_booking_participants",
        "private_booking_activities",
        "private_booking_custom_days",
        "reviews",
        "review_replies"
    ];
    
    // Clear each table
    foreach ($tables as $table) {
        // Delete all records
        $sql = "DELETE FROM $table";
        $count = $db->exec($sql);
        echo "Table $table: $count records deleted<br>";
        
        // Reset auto-increment counter
        $db->exec("DELETE FROM sqlite_sequence WHERE name='$table'");
        echo "Auto-increment for $table reset<br>";
    }
    
    // Re-enable foreign key constraints
    $db->exec('PRAGMA foreign_keys = ON');
    
    // Optimize database size
    $db->exec('VACUUM');
    echo "Database vacuumed to reclaim space<br>";
    
    echo "<br>All specified tables have been cleared successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>