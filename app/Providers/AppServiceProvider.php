<?php

namespace App\Providers;

use App\Repositories\Books\BookRepository;
use App\Repositories\Books\BookRepositoryInterface;
use App\Services\Books\BookService;
use App\Services\Books\BookServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);
        $this->app->bind(BookServiceInterface::class, BookService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
