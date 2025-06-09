protected $routeMiddleware = [
    // ... other middlewares
    'check' => \App\Http\Middleware\YourCheckMiddleware::class,
];