<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'jinggoycasana5@gmail.com')->first();

echo 'exists=' . ($user ? 'yes' : 'no') . PHP_EOL;
if ($user) {
    echo 'role=' . $user->role . PHP_EOL;
    echo 'password_matches=' . (Illuminate\Support\Facades\Hash::check('Admin12345', $user->password) ? 'yes' : 'no') . PHP_EOL;
}
