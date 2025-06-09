<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalActivity;
use App\Models\ActivityPricingTier;
use App\Models\TravelPackage;
use Illuminate\Http\Request;

class AdditionalActivityController extends Controller
{
    public function index()
    {
        $activities = AdditionalActivity::with(['travelPackage', 'pricingTiers'])->paginate(10);
        return view('admin.activities.index', compact('activities'));
    }

    public function create()
    {
        $packages = TravelPackage::all();
        return view('admin.activities.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'travel_package_id' => 'required|exists:travel_packages,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'pricing_tiers' => 'nullable|array',
            'pricing_tiers.*.participant_type' => 'required|in:adult,child,infant',
            'pricing_tiers.*.price' => 'required|numeric|min:0',
        ]);

        $activity = AdditionalActivity::create([
            'travel_package_id' => $request->travel_package_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => $request->is_active ?? true,
        ]);

        // Add pricing tiers if provided
        if ($request->has('pricing_tiers')) {
            foreach ($request->pricing_tiers as $tier) {
                ActivityPricingTier::create([
                    'additional_activity_id' => $activity->id,
                    'participant_type' => $tier['participant_type'],
                    'price' => $tier['price'],
                ]);
            }
        }

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity created successfully');
    }

    public function edit(AdditionalActivity $activity)
    {
        $packages = TravelPackage::all();
        $activity->load('pricingTiers');
        return view('admin.activities.edit', compact('activity', 'packages'));
    }

    public function update(Request $request, AdditionalActivity $activity)
    {
        $request->validate([
            'travel_package_id' => 'required|exists:travel_packages,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'pricing_tiers' => 'nullable|array',
            'pricing_tiers.*.participant_type' => 'required|in:adult,child,infant',
            'pricing_tiers.*.price' => 'required|numeric|min:0',
        ]);

        $activity->update([
            'travel_package_id' => $request->travel_package_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => $request->is_active ?? true,
        ]);

        // Update pricing tiers
        if ($request->has('pricing_tiers')) {
            // Delete existing tiers
            $activity->pricingTiers()->delete();
            
            // Create new tiers
            foreach ($request->pricing_tiers as $tier) {
                ActivityPricingTier::create([
                    'additional_activity_id' => $activity->id,
                    'participant_type' => $tier['participant_type'],
                    'price' => $tier['price'],
                ]);
            }
        }

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
