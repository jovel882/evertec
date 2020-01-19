<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     * @param Dispatcher $events Evento.
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        \App\Models\Transaction::observe(\App\Observers\TransactionObserver::class);
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $menu=\App\MenuFilter::getMenu();
            foreach ($menu as $item) {
                $event->menu->add($item);
            }
        });
    }
}
