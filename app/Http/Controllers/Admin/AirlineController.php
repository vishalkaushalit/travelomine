<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
   public function index(Request $request)
{
    $query = Airline::query();
    
    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('airline_name', 'like', "%{$search}%")
              ->orWhere('airline_code', 'like', "%{$search}%");
        });
    }
    
    // Sorting functionality
    if ($request->filled('sort_by')) {
        if ($request->sort_by == 'name') {
            $query->orderBy('airline_name', $request->get('sort_order', 'asc'));
        } elseif ($request->sort_by == 'latest') {
            $query->latest();
        }
    } else {
        // Default sorting (you can change this as needed)
        $query->latest();
    }
    
    $airlines = $query->paginate(10); // Adjust pagination count as needed
    
    return view('admin.airlines.index', compact('airlines'));
}

    public function create()
    {
        return view('admin.airlines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'airline_code' => 'required|max:2|unique:airlines',
            'airline_name' => 'required',
            'logo' => 'nullable|image'
        ]);

        $logoPath = null;

        if ($request->hasFile('logo')) {

            $logoPath = $request->file('logo')
                ->store('airlines', 'public');
        }

        Airline::create([
            'airline_code' => strtoupper($request->airline_code),
            'airline_name' => $request->airline_name,
            'logo' => $logoPath,
        ]);

        return redirect()
            ->route('admin.airlines.index')
            ->with('success', 'Airline Added Successfully');
    }

    public function edit(Airline $airline)
    {
        return view('admin.airlines.edit', compact('airline'));
    }

    public function update(Request $request, Airline $airline)
    {
        $request->validate([
            'airline_code' => 'required|max:2|unique:airlines,airline_code,' . $airline->id,
            'airline_name' => 'required',
            'logo' => 'nullable|image'
        ]);

        $logoPath = $airline->logo;

        if ($request->hasFile('logo')) {

            $logoPath = $request->file('logo')
                ->store('airlines', 'public');
        }

        $airline->update([
            'airline_code' => strtoupper($request->airline_code),
            'airline_name' => $request->airline_name,
            'logo' => $logoPath,
        ]);

        return redirect()
            ->route('admin.airlines.index')
            ->with('success', 'Airline Updated Successfully');
    }

    public function destroy(Airline $airline)
    {
        $airline->delete();

        return back()
            ->with('success', 'Airline Deleted Successfully');
    }
}