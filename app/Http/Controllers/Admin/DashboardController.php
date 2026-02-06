<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Submission;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_forms' => Form::count(),
            'active_forms' => Form::where('is_active', true)->count(),
            'total_submissions' => Submission::count(),
        ];
        
        $recentForms = Form::latest()->take(5)->get();
        $recentSubmissions = Submission::with('form')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentForms', 'recentSubmissions'));
    }
}
