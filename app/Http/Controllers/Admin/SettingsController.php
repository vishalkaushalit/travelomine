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
    $serviceProvided = Setting::getOptions('service_provided');
    $serviceType = Setting::getOptions('service_type');

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
    public function destroy($key, $id)
    {
        $deleted = Setting::deleteOption($key, $id);

        if ($deleted) {
            return redirect()->back()
                ->with('success', 'Option deleted successfully!');
        }

        return redirect()->back()
            ->with('error', 'Option not found!');
    }
 
}
