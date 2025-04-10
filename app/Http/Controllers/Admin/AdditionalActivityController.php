<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalActivity;
use Illuminate\Http\Request;

class AdditionalActivityController extends Controller
{
    public function index()
    {
        $activities = AdditionalActivity::orderBy('name')->paginate(10);
        return view('admin.activities.index', compact('activities'));
    }

    public function create()
    {
        return view('admin.activities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        AdditionalActivity::create($validated);
        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity added successfully');
    }

    public function edit(AdditionalActivity $activity)
    {
        return view('admin.activities.edit', compact('activity'));
    }

    public function update(Request $request, AdditionalActivity $activity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $activity->update($validated);
        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity updated successfully');
    }

    public function destroy(AdditionalActivity $activity)
    {
        $activity->delete();
        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity deleted successfully');
    }
}
