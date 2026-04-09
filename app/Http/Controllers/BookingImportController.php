<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Imports\OldBookingsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookingImportController extends Controller
{
    public function create()
    {
        return view('mis.bookings.import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        try {
            $import = new OldBookingsImport(auth()->id());
            Excel::import($import, $request->file('file'));

            return back()->with('success',
                "Import completed. Inserted: {$import->inserted}, Skipped: {$import->skipped}, Failed: {$import->failed}"
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}