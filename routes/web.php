<?php

use Illuminate\Support\Facades\Route;

// Dashboard
use App\Http\Controllers\Backend\Dashboard\DashboardAdminController;

// My profile
use App\Http\Controllers\Backend\MyProfile\AccountController;
use App\Http\Controllers\Backend\MyProfile\ActivityController;
use App\Http\Controllers\Backend\MyProfile\LoginSessionController;
use App\Http\Controllers\Backend\MyProfile\ProfileController;
use App\Http\Controllers\Backend\MyProfile\SecurityController;

// User management
use App\Http\Controllers\Backend\UserManagement\RoleController;
use App\Http\Controllers\Backend\UserManagement\UserController;

// Help / system
use App\Http\Controllers\Backend\Help\LogActivityController;
use App\Http\Controllers\Backend\Settings\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Portfolio (admin)
use App\Http\Controllers\Backend\Portfolio\ExperienceController;
use App\Http\Controllers\Backend\Portfolio\MessageController;
use App\Http\Controllers\Backend\Portfolio\NavItemController;
use App\Http\Controllers\Backend\Portfolio\PageContentController;
use App\Http\Controllers\Backend\Portfolio\ProjectCategoryController;
use App\Http\Controllers\Backend\Portfolio\ProjectController;
use App\Http\Controllers\Backend\Portfolio\ServiceController;
use App\Http\Controllers\Backend\Portfolio\SkillController;
use App\Http\Controllers\Backend\Portfolio\SocialLinkController;
use App\Http\Controllers\Backend\Portfolio\StatController;
use App\Http\Controllers\Backend\Portfolio\TestimonialController;

// Portfolio (public)
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\PortfolioController;

/*
|--------------------------------------------------------------------------
| Public portfolio
|--------------------------------------------------------------------------
*/
Route::get('/', [PortfolioController::class, 'home'])->name('home');
Route::get('/projects', [PortfolioController::class, 'projects'])->name('portfolio.projects');
Route::get('/projects/{slug}', [PortfolioController::class, 'project'])->name('portfolio.project');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact')->name('portfolio.contact');
Route::get('/sitemap.xml', [PortfolioController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PortfolioController::class, 'robots'])->name('robots');

Route::middleware(['auth', 'forbid-banned-user'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Portfolio content management
    |----------------------------------------------------------------------
    */
    Route::prefix('admin/portfolio')->name('pf.')->middleware('can:manage_portfolio')->group(function () {
        Route::get('content', [PageContentController::class, 'index'])->name('content.index');
        Route::get('export', [PageContentController::class, 'export'])->name('export');
        Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');

        Route::middleware('throttle:write')->group(function () {
            Route::post('content', [PageContentController::class, 'update'])->name('content.update');
            Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        });

        $resources = [
            'projects' => ProjectController::class,
            'categories' => ProjectCategoryController::class,
            'nav' => NavItemController::class,
            'stats' => StatController::class,
            'services' => ServiceController::class,
            'skills' => SkillController::class,
            'experiences' => ExperienceController::class,
            'testimonials' => TestimonialController::class,
            'socials' => SocialLinkController::class,
        ];

        foreach ($resources as $uri => $controller) {
            Route::resource($uri, $controller)->only(['index', 'create', 'edit', 'show']);
            Route::resource($uri, $controller)->only(['store', 'update', 'destroy'])->middleware('throttle:write');
        }
    });

    // --- Dashboard (every authenticated role) ---
    Route::get('/admin/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

    // --- My account (every authenticated user) ---
    Route::get('/admin/my-account', [AccountController::class, 'index'])->name('account.index');
    Route::get('/admin/my-account/{id}/avatar', [AccountController::class, 'editAvatar'])->name('avatar-edit');

    Route::get('/admin/my-activity', [ActivityController::class, 'index'])->name('my-activity.index');
    Route::get('/admin/mget-my-activity', [ActivityController::class, 'getActivity'])->name('get-my-activity');

    Route::get('/admin/mmy-login-session', [LoginSessionController::class, 'index'])->name('my-login-session.index');
    Route::get('/admin/mget-my-login-session', [LoginSessionController::class, 'getLoginSession'])->name('get-my-login-session');

    // --- Activity log ---
    // Accessible to every authenticated user; the controller scopes the query
    // so a non-Superadmin only ever sees the entries they caused.
    Route::get('/admin/log-activity', [LogActivityController::class, 'index'])->name('log-activity.index');
    Route::get('/admin/get-datalogactivity', [LogActivityController::class, 'getDataLogActivity'])->name('get-datalogactivity');
    Route::get('/admin/log-activity/{id}/detail', [LogActivityController::class, 'detail'])->name('log-activity.detail');
    Route::get('/admin/log-activity/{id}', [LogActivityController::class, 'show'])->name('log-activity.show');

    // Shared helpers used by the role/user forms.
    Route::get('/admin/select/role', [RoleController::class, 'select'])->name('role.select');

    /*
    |----------------------------------------------------------------------
    | Write operations — tighter rate limit than plain page reads.
    |----------------------------------------------------------------------
    */
    Route::middleware('throttle:write')->group(function () {

        Route::post('/admin/my-account/{id}/update-avatar', [AccountController::class, 'updateAvatar'])->name('avatar-update');
        Route::post('/admin/my-security', [SecurityController::class, 'store'])->name('change.password');
        Route::post('/admin/my-security/logout-other-devices', [SecurityController::class, 'logoutOtherDevices'])
            ->name('security.logout-other-devices');

        // --- Settings (Superadmin) ---
        Route::middleware('can:view_resources')->group(function () {
            Route::post('/admin/settings/update', [SettingController::class, 'update'])->name('settings.update');
            Route::post('/admin/settings/regenerate-branding', [SettingController::class, 'regenerateBranding'])
                ->name('settings.branding');

            Route::post('/admin/roles/generate-permissions', [RoleController::class, 'generatePermissions'])->name('roles.generate');
            Route::post('/admin/users/mass-delete', [UserController::class, 'massDelete'])->name('users.mass-delete');
            Route::post('/admin/users/{id}/ban', [UserController::class, 'ban'])->name('users.ban');
            Route::post('/admin/users/{id}/unban', [UserController::class, 'unban'])->name('users.unban');
            Route::post('/admin/roles/mass-delete', [RoleController::class, 'massDelete'])->name('roles.mass-delete');
        });
    });

    // --- Profile & security pages (resource controllers keep their own verbs) ---
    Route::resource('/admin/my-profile', ProfileController::class);
    Route::resource('/admin/my-security', SecurityController::class);

    // --- Settings page (read) ---
    Route::get('/admin/settings', [SettingController::class, 'index'])
        ->middleware('can:view_resources')
        ->name('settings.index');

    /*
    |----------------------------------------------------------------------
    | User & role management — Superadmin only.
    |----------------------------------------------------------------------
    */
    Route::middleware('can:view_resources')->group(function () {
        Route::resource('/admin/users', UserController::class);
        Route::get('/admin/get-datauser', [UserController::class, 'getDataUsers'])->name('get-users');
        Route::get('/admin/get-user-show-log/{id}', [UserController::class, 'getLoginSession'])->name('get-user-show-log');
        Route::get('/admin/get-user-show-log-activity/{id}', [UserController::class, 'getActivity'])->name('get-user-show-log-activity');

        Route::resource('/admin/roles', RoleController::class);
        Route::get('/admin/get-datarole', [RoleController::class, 'getDataRoles'])->name('get-datarole');
    });
});

require __DIR__ . '/auth.php';
