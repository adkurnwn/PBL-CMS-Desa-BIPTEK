<?php

use App\Models\News;
use Inertia\Inertia;
use App\Models\ProdukDesa;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\NewsController;
use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\GalleryController;
use App\Http\Controllers\Dashboard\DanaDesaController;
use App\Http\Controllers\Dashboard\FeedbackController;
use App\Http\Controllers\Dashboard\PositionController;
use App\Http\Controllers\Dashboard\DemografiController;
use App\Http\Controllers\Dashboard\ProdukDesaController;
use App\Http\Controllers\Dashboard\ProdukHukumController;
use App\Http\Controllers\Dashboard\KomentarBeritaController;
use App\Http\Controllers\Dashboard\RoleManagementController;
use App\Http\Controllers\Dashboard\UserManagementController;
use App\Http\Controllers\Dashboard\KomentarProdukHukumController;
use App\Http\Controllers\Dashboard\ProfileController as DesaProfileController;

Route::get('/', function () {
    return Inertia::render('FrontEnd/HomeView');
})->name('home');

Route::get('/profildesa', function () {
    return Inertia::render('FrontEnd/ProfileView');
})->name('profil-desa');

Route::get('/informasi', function () {
    return Inertia::render('FrontEnd/InformasiView');
})->name('informasi');

Route::get('/layanan', function () {
    return Inertia::render('FrontEnd/LayananView');
})->name('layanan');


Route::get('/berita', function () {
    return Inertia::render('FrontEnd/BeritaListTest');
})->name('berita');

Route::get('/berita/{id}', function ($id) {
    return Inertia::render('FrontEnd/BeritaDetail', [
        'id' => $id
    ]);
})->name('berita.show');

Route::get('/berita-test', function () {
    return Inertia::render('FrontEnd/BeritaListTest');
})->name('berita');

Route::get('/berita-test/{slug}', function ($slug) {
    $news = News::where('slug', $slug)->firstOrFail();
    return Inertia::render('FrontEnd/BeritaDetailTest', [
        'slug' => $slug
    ]);
})->name('berita.show');

Route::get('/produk-hukum-test', function () {
    return Inertia::render('FrontEnd/ProdukHukumListTest');
})->name('produk-hukum');

Route::get('/produk-hukum-test/{id}', function ($id) {
    return Inertia::render('FrontEnd/ProdukHukumDetailTest', [
        'id' => $id
    ]);
})->name('produk-hukum.show');

Route::middleware(['auth', 'verified', 'user.status'])->group(function () {
    Route::middleware(['permission:view_dashboard'])->get('/dashboard', function () {
        return Inertia::render('Dashboard/Dashboard');
    })->name('dashboard');

    Route::prefix('dashboard')->group(function () {

        // User management routes
        Route::middleware(['permission:read_users'])->get('/users', [UserManagementController::class, 'index'])->name('dashboard.users');
        Route::middleware(['permission:create_users'])->get('/users/create', [UserManagementController::class, 'create'])->name('dashboard.users.create');
        Route::middleware(['permission:create_users'])->post('/users', [UserManagementController::class, 'store'])->name('dashboard.users.store');
        Route::middleware(['permission:update_users'])->get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('dashboard.users.edit');
        Route::middleware(['permission:update_users'])->put('/users/{user}', [UserManagementController::class, 'update'])->name('dashboard.users.update');
        Route::middleware(['permission:delete_users'])->delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('dashboard.users.destroy');

        // Role management routes
        Route::middleware(['permission:manage_roles'])->group(function () {
            Route::get('/roles', [RoleManagementController::class, 'index'])->name('dashboard.roles');
            Route::get('/roles/{role}/edit', [RoleManagementController::class, 'edit'])->name('dashboard.roles.edit');
            Route::put('/roles/{role}', [RoleManagementController::class, 'update'])->name('dashboard.roles.update');
        });

        //Profile management routes
        Route::middleware(['permission:read_profiles'])->get('/profiles', [DesaProfileController::class, 'index'])->name('dashboard.profiles');
        Route::middleware(['permission:update_profiles'])->get('/profiles/{profile}/edit', [DesaProfileController::class, 'edit'])->name('dashboard.profiles.edit');
        Route::middleware(['permission:update_profiles'])->put('/profiles/{profile}', [DesaProfileController::class, 'update'])->name('dashboard.profiles.update');

        Route::middleware(['permission:read_news'])->get('/news', [NewsController::class, 'index'])->name('dashboard.news');
        Route::middleware(['permission:create_news'])->get('/news/create', [NewsController::class, 'create'])->name('dashboard.news.create');
        Route::middleware(['permission:create_news'])->post('/news', [NewsController::class, 'store'])->name('dashboard.news.store');
        Route::middleware(['permission:update_news'])->get('/news/{news}/edit', [NewsController::class, 'edit'])->name('dashboard.news.edit');
        Route::middleware(['permission:update_news'])->put('/news/{news}', [NewsController::class, 'update'])->name('dashboard.news.update');
        Route::middleware(['permission:archive_news'])->get('/news/archive', [NewsController::class, 'archive'])->name('dashboard.news.archive');
        Route::middleware(['permission:restore_news'])->get('/news/{news}/restore', [NewsController::class, 'restore'])->name('dashboard.news.restore')->withTrashed();
        Route::middleware(['permission:delete_news'])->delete('/news/{news}', [NewsController::class, 'destroy'])->name('dashboard.news.destroy')->withTrashed();

        Route::middleware(['permission:read_galleries'])->get('/galleries', [GalleryController::class, 'index'])->name('dashboard.galleries');
        Route::middleware(['permission:create_galleries'])->get('/galleries/create', [GalleryController::class, 'create'])->name('dashboard.galleries.create');
        Route::middleware(['permission:create_galleries'])->post('/galleries', [GalleryController::class, 'store'])->name('dashboard.galleries.store');
        Route::middleware(['permission:update_galleries'])->get('/galleries/{gallery}/edit', [GalleryController::class, 'edit'])->name('dashboard.galleries.edit');
        Route::middleware(['permission:update_galleries'])->put('/galleries/{gallery}', [GalleryController::class, 'update'])->name('dashboard.galleries.update');
        Route::middleware(['permission:delete_galleries'])->delete('/galleries/{gallery}', [GalleryController::class, 'destroy'])->name('dashboard.galleries.destroy');

        Route::middleware(['permission:read_events'])->get('/event', [EventController::class, 'index'])->name('dashboard.events');
        Route::middleware(['permission:create_events'])->get('/event/create', [EventController::class, 'create'])->name('dashboard.events.create');
        Route::middleware(['permission:create_events'])->post('/event', [EventController::class, 'store'])->name('dashboard.events.store');
        Route::middleware(['permission:update_events'])->get('/event/{event}/edit', [EventController::class, 'edit'])->name('dashboard.events.edit');
        Route::middleware(['permission:update_events'])->put('/event/{event}', [EventController::class, 'update'])->name('dashboard.events.update');
        Route::middleware(['permission:delete_events'])->delete('/event/{event}', [EventController::class, 'destroy'])->name('dashboard.events.destroy');

        Route::middleware(['permission:read_produk-hukum'])->get('/produk-hukum', [ProdukHukumController::class, 'index'])->name('dashboard.produk-hukum');
        Route::middleware(['permission:create_produk-hukum'])->get('/produk-hukum/create', [ProdukHukumController::class, 'create'])->name('dashboard.produk-hukum.create');
        Route::middleware(['permission:create_produk-hukum'])->post('/produk-hukum', [ProdukHukumController::class, 'store'])->name('dashboard.produk-hukum.store');
        Route::middleware(['permission:update_produk-hukum'])->get('/produk-hukum/{produkHukum}/edit', [ProdukHukumController::class, 'edit'])->name('dashboard.produk-hukum.edit');
        Route::middleware(['permission:update_produk-hukum'])->put('/produk-hukum/{produkHukum}', [ProdukHukumController::class, 'update'])->name('dashboard.produk-hukum.update');
        Route::middleware(['permission:delete_produk-hukum'])->delete('/produk-hukum/{produkHukum}', [ProdukHukumController::class, 'destroy'])->name('dashboard.produk-hukum.destroy');

        Route::middleware(['permission:read_dana-desa'])->get('/dana-desa', [DanaDesaController::class, 'index'])->name('dashboard.dana-desa');
        Route::middleware(['permission:create_dana-desa'])->get('/dana-desa/create', [DanaDesaController::class, 'create'])->name('dashboard.dana-desa.create');
        Route::middleware(['permission:create_dana-desa'])->post('/dana-desa', [DanaDesaController::class, 'store'])->name('dashboard.dana-desa.store');
        Route::middleware(['permission:update_dana-desa'])->get('/dana-desa/{danaDesa}/edit', [DanaDesaController::class, 'edit'])->name('dashboard.dana-desa.edit');
        Route::middleware(['permission:update_dana-desa'])->put('/dana-desa/{danaDesa}', [DanaDesaController::class, 'update'])->name('dashboard.dana-desa.update');
        Route::middleware(['permission:delete_dana-desa'])->delete('/dana-desa/{danaDesa}', [DanaDesaController::class, 'destroy'])->name('dashboard.dana-desa.destroy');

        Route::middleware(['permission:read_positions'])->get('/position', [PositionController::class, 'index'])->name('dashboard.positions');
        Route::middleware(['permission:create_positions'])->get('/position/create', [PositionController::class, 'create'])->name('dashboard.positions.create');
        Route::middleware(['permission:create_positions'])->post('/position', [PositionController::class, 'store'])->name('dashboard.positions.store');
        Route::middleware(['permission:update_positions'])->get('/position/{position}/edit', [PositionController::class, 'edit'])->name('dashboard.positions.edit');
        Route::middleware(['permission:update_positions'])->put('/position/{position}', [PositionController::class, 'update'])->name('dashboard.positions.update');
        Route::middleware(['permission:delete_positions'])->delete('/position/{position}', [PositionController::class, 'destroy'])->name('dashboard.positions.destroy');

        Route::middleware(['permission:read_produk-desa'])->get('/produk-desa', [ProdukDesaController::class, 'index'])->name('dashboard.produk-desa');
        Route::middleware(['permission:create_produk-desa'])->get('/produk-desa/create', [ProdukDesaController::class, 'create'])->name('dashboard.produk-desa.create');
        Route::middleware(['permission:create_produk-desa'])->post('/produk-desa', [ProdukDesaController::class, 'store'])->name('dashboard.produk-desa.store');
        Route::middleware(['permission:update_produk-desa'])->get('/produk-desa/{produkDesa}/edit', [ProdukDesaController::class, 'edit'])->name('dashboard.produk-desa.edit');
        Route::middleware(['permission:update_produk-desa'])->put('/produk-desa/{produkDesa}', [ProdukDesaController::class, 'update'])->name('dashboard.produk-desa.update');
        Route::middleware(['permission:delete_produk-desa'])->delete('/produk-desa/{produkDesa}', [ProdukDesaController::class, 'destroy'])->name('dashboard.produk-desa.destroy');

        Route::middleware(['permission:read_comments'])->get('/comments', [KomentarBeritaController::class, 'index'])->name('dashboard.comments');
        Route::middleware(['permission:update_comments'])->patch('/comments/{comment}/approve', [KomentarBeritaController::class, 'approve'])->name('dashboard.comments.approve');
        Route::middleware(['permission:delete_comments'])->delete('/comments/{comment}', [KomentarBeritaController::class, 'destroy'])->name('dashboard.comments.destroy');

        Route::middleware(['permission:read_comments'])->get('/produk-hukum-comments', [KomentarProdukHukumController::class, 'index'])->name('dashboard.produk-hukum-comments');
        Route::middleware(['permission:update_comments'])->patch('/produk-hukum-comments/{comment}/approve', [KomentarProdukHukumController::class, 'approve'])->name('dashboard.produk-hukum-comments.approve');
        Route::middleware(['permission:delete_comments'])->delete('/produk-hukum-comments/{comment}', [KomentarProdukHukumController::class, 'destroy'])->name('dashboard.produk-hukum-comments.destroy');

        Route::middleware(['permission:read_demografi'])->get('/demografi', [DemografiController::class, 'index'])->name('dashboard.demografi');
        Route::middleware(['permission:create_demografi'])->get('/demografi/create', [DemografiController::class, 'create'])->name('dashboard.demografi.create');
        Route::middleware(['permission:create_demografi'])->post('/demografi', [DemografiController::class, 'store'])->name('dashboard.demografi.store');
        Route::middleware(['permission:update_demografi'])->get('/demografi/{demografi}/edit', [DemografiController::class, 'edit'])->name('dashboard.demografi.edit');
        Route::middleware(['permission:update_demografi'])->put('/demografi/{demografi}', [DemografiController::class, 'update'])->name('dashboard.demografi.update');
        Route::middleware(['permission:delete_demografi'])->delete('/demografi/{demografi}', [DemografiController::class, 'destroy'])->name('dashboard.demografi.destroy');

        Route::middleware(['permission:read_feedbacks'])->get('/feedbacks', [FeedbackController::class, 'index'])->name('dashboard.feedbacks');
        Route::middleware(['permission:read_feedbacks'])->get('/feedbacks/{feedback}', [FeedbackController::class, 'show'])->name('dashboard.feedbacks.show');
        Route::middleware(['permission:update_feedbacks'])->post('/feedbacks/{feedback}/respond', [FeedbackController::class, 'respond'])->name('dashboard.feedbacks.respond');
    });
});

Route::middleware(['auth', 'user.status'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
