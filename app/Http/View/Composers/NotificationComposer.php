<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Lidheni të dhënat me pamjen.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $unreadNotifications = $user->njoftimet()->where('lexuar', false)->latest('data_krijimit')->take(5)->get();
            $unreadNotificationsCount = $user->njoftimet()->where('lexuar', false)->count();

            $view->with('unreadNotifications', $unreadNotifications)
                 ->with('unreadNotificationsCount', $unreadNotificationsCount);
        } else {
            $view->with('unreadNotifications', collect())
                 ->with('unreadNotificationsCount', 0);
        }
    }
}
