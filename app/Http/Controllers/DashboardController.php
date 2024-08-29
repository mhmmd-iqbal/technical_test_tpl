<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function __invoke()
    {
        if (auth()->check()) {
            return redirect()->route('products.index');
        }

        return redirect()->route('login');
    }
}
