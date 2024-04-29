<?php

use App\Http\Controllers\ProfileController;
use App\Services\Google;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    mkdir(__DIR__ . '/../token.json', 0700, true);
});

Route::get('/createmeet', function () {
    $googleService = new Google();
    $client = $googleService->getClient();
    $httpClient = new Client();
    $response = $httpClient->post('https://meet.googleapis.com/v2/spaces', [
        'headers' => [
            'Authorization' => 'Bearer ' . $client->getAccessToken()['access_token'],
        ],
    ]);
    dd(json_decode($response->getBody(), true));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
