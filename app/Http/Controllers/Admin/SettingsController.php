<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
   

    /**
     * Booking Settings Page
     */
public function bookings()
{
    $serviceProvided = collect(Setting::getOptions('service_provided'));
    $serviceType = collect(Setting::getOptions('service_type'));

    return view('admin.settings.bookings', compact('serviceProvided', 'serviceType'));
}
    /**
     * Add new option
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|in:service_provided,service_type,cabin_type',
            'value' => 'required|string|max:255',
        ]);

        $added = Setting::addOption($validated['key'], $validated['value']);

        if ($added) {
            return redirect()->back()->with('success', 'Option added successfully!');
        } else {
            return redirect()->back()->with('error', 'This option already exists!');
        }
    }

    /**
     * Delete option
     */
    public function destroy($id)
    {
        Setting::deleteOption($id);
        return redirect()->back()->with('success', 'Option deleted successfully!');
    }

 
}
