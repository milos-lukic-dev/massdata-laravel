<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Controller responsible for displaying dashboard.
 *
 * @class DashboardController
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     *
     * @return View The rendered dashboard view.
     */
    public function index(): View
    {
        return view('dashboard');
    }
}
