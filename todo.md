create middleware Admin

in app.php
```php
->withMiddleware(function(Middleware $middleware) {
    $middleware->add('admin', function($request, $response, $next) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);
    });
});

in AdminMiddleware.php
```php
<?php
if (!Auth::guard('admin')->check()) {
    return response()->json(['message' => "You don't have permission to access this page!!!"], 403);
}

return $next($request);
```

in api.php
```php
Route::group(['middleware' => 'admin'], function () {
    Route dashboard
});
```
