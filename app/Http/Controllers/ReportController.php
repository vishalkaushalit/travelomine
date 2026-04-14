<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of reports
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Export reports data
     */
    public function export()
    {
        // Add your export logic here
        return redirect()->back()->with('success', 'Report exported successfully.');
    }
}