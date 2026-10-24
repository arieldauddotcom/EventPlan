<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mencegah error 500 jika touch() pada BladeCompiler gagal karena perbedaan user process (www-data vs CLI)
        set_error_handler(function ($severity, $message, $file, $line) {
            if (str_contains($file, 'BladeCompiler.php') && str_contains($message, 'touch()')) {
                return true;
            }
            return false;
        }, E_WARNING);
    }
}
