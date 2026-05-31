<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        /**
         * @active('pattern') — outputs 'active' if current URL matches the pattern.
         * Usage in Blade: class="nav-link @active('admin/dashboard')"
         */
        Blade::directive('active', function (string $expression) {
            // Strip surrounding quotes
            $pattern = trim($expression, "'\"");
            return "<?php echo request()->is('{$pattern}') ? 'active' : ''; ?>";
        });
    }
}
