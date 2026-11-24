<?php

use App\Http\Controllers\Admin\YouTubeOAuthController;
use App\Http\Controllers\AdPromotionController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShopRequestController;
use App\Http\Controllers\VideoPostController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/api/location/approximate', [LocationController::class, 'approximate'])->name('api.location.approximate');
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::post('/feedback', [FeedbackController::class, 'store'])->middleware('throttle:6,1')->name('feedback.store');

Route::get('/manifest.json', function () {
    return response(file_get_contents(public_path('manifest.json')), 200, [
        'Content-Type' => 'application/manifest+json; charset=UTF-8',
        'Cache-Control' => 'no-cache',
    ]);
})->name('pwa.manifest');

Route::get('/sw.js', function () {
    return response(file_get_contents(public_path('sw.js')), 200, [
        'Content-Type' => 'application/javascript; charset=UTF-8',
        'Service-Worker-Allowed' => '/',
        'Cache-Control' => 'no-cache',
    ]);
})->name('pwa.sw');

Route::get('/sellers/{user}', [ProfileController::class, 'show'])->name('sellers.show');
Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');

// Vaqtinchalik xodim profili (test sahifalari)
Route::get('/test-profile', function () {
    return response()->file(public_path('test/index.php'), [
        'Content-Type' => 'text/html; charset=UTF-8',
        'X-Robots-Tag' => 'noindex, nofollow',
    ]);
});
Route::get('/test-abdujalilova', function () {
    return response()->file(public_path('test/abdujalilova.php'), [
        'Content-Type' => 'text/html; charset=UTF-8',
        'X-Robots-Tag' => 'noindex, nofollow',
    ]);
});

// Motrix Quiz routes (Haftalik viktorina tizimi)
Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index');
Route::get('/quiz/{quiz:slug}', [QuizController::class, 'show'])->name('quiz.show');
Route::post('/quiz/{quiz:slug}/check', [QuizController::class, 'checkAnswer'])->name('quiz.check');
Route::post('/quiz/{quiz:slug}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
Route::get('/quiz/{quiz:slug}/result/{attempt}', [QuizController::class, 'result'])->name('quiz.result');
Route::get('/quiz/{quiz:slug}/leaderboard', [QuizController::class, 'leaderboard'])->name('quiz.leaderboard');

// Video Postlar (Ijtimoiy tarmoqlar va sayt videolari)
Route::get('/video-posts', [VideoPostController::class, 'index'])->name('video-posts.index');
Route::get('/video-posts/{slug}', [VideoPostController::class, 'show'])->name('video-posts.show');

// YouTube Data API OAuth ulanishi
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/youtube/auth', [YouTubeOAuthController::class, 'redirect'])->name('admin.youtube.auth');
    Route::get('/admin/youtube/callback', [YouTubeOAuthController::class, 'callback'])->name('admin.youtube.callback');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && ($user->can('platform.access-admin') || $user->hasRole('administrator'))) {
        return redirect('/admin');
    }
    return redirect()->route('profile.edit');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/promotions', [AdPromotionController::class, 'store'])->name('promotions.store');

    Route::get('/shops-create-request', [ShopRequestController::class, 'create'])->name('shop-requests.create');
    Route::post('/shop-requests', [ShopRequestController::class, 'store'])->name('shop-requests.store');
    Route::put('/profile/shop/{shop}', [ShopController::class, 'update'])->name('profile.shop.update');
});

require __DIR__.'/auth.php';
