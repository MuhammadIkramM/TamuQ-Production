<?php
putenv('DB_DATABASE=' . __DIR__ . '/database/database.sqlite');
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// create a dummy user in memory to bypass auth if needed, or just run the route
// Wait, sanctum doesn't use the DB if we just use a valid route? 
// Let's just hit the endpoint without auth, it should return 401. But let's see what it returns!
$request = Illuminate\Http\Request::create('/api/skripsi/1/preview', 'GET');
$response = $kernel->handle($request);

echo "STATUS: " . $response->getStatusCode() . PHP_EOL;
echo "CONTENT_TYPE: " . $response->headers->get('Content-Type') . PHP_EOL;
echo "CONTENT: " . substr($response->getContent(), 0, 100) . PHP_EOL;
