<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enforce Asia/Jakarta (WIB) timezone across all PHP date/time operations
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));

        // Enforce HTTPS for assets, routes, and redirects in production / Vercel
        if (config('app.env') === 'production' || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Currency formatting Blade directive
        Blade::directive('currency', function ($expression) {
            return "<?php echo format_rupiah($expression); ?>";
        });

        // Server-side Authorization Gates
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('cashier', function (User $user) {
            return $user->isCashier();
        });

        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-master-data', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-suppliers', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('view-reports', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('cancel-sale', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('access-pos', function (User $user) {
            return $user->is_active;
        });

        Gate::define('manage-inventory', function (User $user) {
            return $user->is_active;
        });

        Gate::define('manage-customers', function (User $user) {
            return $user->is_active;
        });
    }
}
