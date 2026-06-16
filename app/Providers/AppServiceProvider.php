<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use App\Models\Payment;
use App\Models\Expense;
use App\Observers\PaymentObserver;
use App\Observers\ExpenseObserver;

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
        Vite::prefetch(concurrency: 3);
        Payment::observe(PaymentObserver::class);
        Expense::observe(ExpenseObserver::class);
    }
}
