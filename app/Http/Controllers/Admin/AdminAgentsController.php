<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminAgentsController extends Controller
{
    public function index()
    {
          $agents = User::where(function ($q) {
                $q->where('email', 'like', '%@callinggenie.com')
                  ->orWhere('email', 'like', '%@trafficpirates.com');
            })
            ->where('role', 'agent')
            ->withCount('bookings') // This adds the count of related bookings
            ->orderByDesc('id')
            ->get();
        return view('admin.agents.index', compact('agents'));

    }
        // admin can active and inactiavate the agents by clicking on the button in the agents list page, 
        // when admin click on the button it will send a request to the server and update the status of the agent in the database,
        //  and then return a response to the client, and then show a success message to the admin, and then refresh the page to show the updated status of the agent.
        public function toggleStatus(User $agent)
{
    $agent->update([
        'is_active' => !$agent->is_active
    ]);

    return redirect()->route('admin.agents.index')
        ->with('success', 'Agent status updated successfully.');
}
    


}