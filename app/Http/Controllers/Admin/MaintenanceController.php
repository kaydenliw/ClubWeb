<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'required|in:true,false,0,1',
            'message' => 'nullable|string|max:500',
        ]);

        $isEnabled = in_array($validated['enabled'], ['true', '1', 1, true], true);

        Setting::set('maintenance_mode', $isEnabled ? 'true' : 'false');

        if (isset($validated['message'])) {
            Setting::set('maintenance_message', $validated['message']);
        }

        return back()->with('success',
            $isEnabled
                ? 'Maintenance mode enabled. Users cannot access the portal.'
                : 'Maintenance mode disabled. Portal is now accessible.'
        );
    }
}
