<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TravelPackageController extends Controller
{
    public function index(Request $request)
    {
        // Define available countries
        $countries = ['Indonesia', 'Thailand', 'Vietnam', 'South Korea'];

        // Get the selected country from the request for filtering
        $selectedCountry = $request->get('country', ''); // Default is an empty string

        // Filter packages based on the selected country
        $query = TravelPackage::query();
        if (!empty($selectedCountry)) {
            $query->where('country', $selectedCountry);
        }
        $packages = $query->get();

        // Pass variables to the view
        return view('admin.travel-package.index', compact('packages', 'countries', 'selectedCountry'));
    }

    public function create()
    {
        return view('admin.travel-package.create');
    }

    public function store(Request $request)
    {
        // Validate data with additional fields
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|in:Indonesia,Thailand,Vietnam,South Korea',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1', // Add duration validation
            'description' => 'required|string',
            'itinerary' => 'required|array', // Changed to required
            'include' => 'required|array', // Changed to required
            'exclude' => 'required|array', // Changed to required
            'itinerary_pdfs.*' => 'nullable|file|mimes:pdf|max:2048',
            'include_pdfs.*' => 'nullable|file|mimes:pdf|max:2048',
            'exclude_pdfs.*' => 'nullable|file|mimes:pdf|max:2048',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Changed to required
            'available_dates' => 'required|array', // Changed to required
            'activities' => 'nullable|array', // Add activities validation
            'activity_prices' => 'nullable|array', // Add activity prices validation
        ]);
    
        // Exclude file uploads for manual handling
        $data = $request->except(['itinerary_pdfs', 'include_pdfs', 'exclude_pdfs', 'image', 'activities', 'activity_prices']);
    
        // Handle file uploads and store paths
        $data['itinerary_pdfs'] = $this->uploadFiles($request, 'itinerary_pdfs', 'itinerary_pdfs');
        $data['include_pdfs'] = $this->uploadFiles($request, 'include_pdfs', 'include_pdfs');
        $data['exclude_pdfs'] = $this->uploadFiles($request, 'exclude_pdfs', 'exclude_pdfs');
    
        // Handle image upload - make sure it's stored correctly
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        } else {
            return redirect()->back()->withInput()->withErrors(['image' => 'The image field is required.']);
        }
    
        // Convert arrays to JSON for database storage
        $data['itinerary'] = $request->input('itinerary') ? json_encode($request->input('itinerary')) : null;
        $data['include'] = $request->input('include') ? json_encode($request->input('include')) : null;
        $data['exclude'] = $request->input('exclude') ? json_encode($request->input('exclude')) : null;
        $data['available_dates'] = $request->input('available_dates') ? json_encode($request->input('available_dates')) : null;
        
        // Handle activities and prices
        if ($request->has('activities') && $request->has('activity_prices')) {
            $activities = [];
            foreach ($request->activities as $index => $activity) {
                if (!empty($activity) && isset($request->activity_prices[$index])) {
                    $activities[] = [
                        'name' => $activity,
                        'price' => $request->activity_prices[$index]
                    ];
                }
            }
            $data['activities'] = json_encode($activities);
        }
        
        // Set default visibility
        $data['is_visible'] = 1; // Make packages visible by default
    
        // Debug data to ensure correctness before saving
        // dd($data);
    
        // Save to the database
        try {
            TravelPackage::create($data);
            return redirect()->route('admin.travel-package.index')
                ->with('success', 'Travel package created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Error creating travel package: ' . $e->getMessage());
        }
    }

    public function edit(TravelPackage $travel_package)
    {
        return view('admin.travel-package.edit', ['package' => $travel_package]);
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|in:Indonesia,Thailand,Vietnam,South Korea',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'itinerary' => 'nullable|array',
            'include' => 'nullable|array',
            'exclude' => 'nullable|array',
            'itinerary_pdfs.*' => 'nullable|file|mimes:pdf|max:2048',
            'include_pdfs.*' => 'nullable|file|mimes:pdf|max:2048',
            'exclude_pdfs.*' => 'nullable|file|mimes:pdf|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'available_dates' => 'nullable|array',
        ]);

        // Fetch the existing package
        $package = TravelPackage::findOrFail($id);
        
        // Exclude file uploads and fields that shouldn't be mass-updated
        $data = $request->except([
            'itinerary_pdfs', 
            'include_pdfs', 
            'exclude_pdfs',
            'duration' // Prevent duration from being changed
        ]);
        
        // Preserve the original duration
        $data['duration'] = $package->duration;

        // Handle file uploads
        $data['itinerary_pdfs'] = $this->uploadFiles($request, 'itinerary_pdfs', 'itinerary_pdfs');
        $data['include_pdfs'] = $this->uploadFiles($request, 'include_pdfs', 'include_pdfs');
        $data['exclude_pdfs'] = $this->uploadFiles($request, 'exclude_pdfs', 'exclude_pdfs');

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        // Handle itinerary - ensure we only update existing items, not add new ones
        $existingItinerary = is_string($package->itinerary) 
            ? json_decode($package->itinerary, true) 
            : $package->itinerary;
            
        if ($request->has('itinerary') && is_array($existingItinerary)) {
            $updatedItinerary = [];
            foreach ($request->input('itinerary') as $index => $item) {
                // Only update items that exist in the original itinerary
                if (isset($existingItinerary[$index])) {
                    $updatedItinerary[$index] = $item;
                }
            }
            // Ensure we maintain the same number of items
            if (count($updatedItinerary) === count($existingItinerary)) {
                $data['itinerary'] = json_encode($updatedItinerary);
            } else {
                $data['itinerary'] = $package->itinerary; // Keep original if counts don't match
            }
        }

        // Convert arrays to JSON for database storage
        $data['include'] = $request->input('include') ? json_encode($request->input('include')) : $package->include;
        $data['exclude'] = $request->input('exclude') ? json_encode($request->input('exclude')) : $package->exclude;
        $data['available_dates'] = $request->input('available_dates') ? json_encode($request->input('available_dates')) : $package->available_dates;

        // Handle activities if provided
        if ($request->has('activities') && $request->has('activity_prices')) {
            $activities = [];
            foreach ($request->activities as $index => $activity) {
                if (!empty($activity) && isset($request->activity_prices[$index])) {
                    $activities[] = [
                        'name' => $activity,
                        'price' => $request->activity_prices[$index]
                    ];
                }
            }
            $data['activities'] = json_encode($activities);
        }
        
        // Handle the visibility checkbox
        $data['is_visible'] = $request->has('is_visible') ? 1 : 0;
        
        // Save to the database
        $package->update($data);
        
        // Redirect with success message
        return redirect()->route('admin.travel-package.index')
            ->with('success', 'Travel package updated successfully!');
    }
    
    // New methods for deleting specific items
    
    public function deleteIncludeItem(Request $request, $packageId, $index)
    {
        $package = TravelPackage::findOrFail($packageId);
        $includeItems = is_string($package->include) 
            ? json_decode($package->include, true) 
            : $package->include;
        
        if (isset($includeItems[$index])) {
            array_splice($includeItems, $index, 1);
            $package->include = json_encode($includeItems);
            $package->save();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }
    
    public function deleteExcludeItem(Request $request, $packageId, $index)
    {
        $package = TravelPackage::findOrFail($packageId);
        $excludeItems = is_string($package->exclude) 
            ? json_decode($package->exclude, true) 
            : $package->exclude;
        
        if (isset($excludeItems[$index])) {
            array_splice($excludeItems, $index, 1);
            $package->exclude = json_encode($excludeItems);
            $package->save();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }
    
    public function deleteAvailableDate(Request $request, $packageId, $index)
    {
        $package = TravelPackage::findOrFail($packageId);
        $dates = is_string($package->available_dates) 
            ? json_decode($package->available_dates, true) 
            : $package->available_dates;
        
        if (isset($dates[$index])) {
            array_splice($dates, $index, 1);
            $package->available_dates = json_encode($dates);
            $package->save();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }

    public function destroy($id)
    {
        $package = TravelPackage::findOrFail($id);

        // Delete image and PDFs if they exist
        if ($package->image) {
            Storage::delete('public/' . $package->image);
        }
        if ($package->itinerary_pdfs) {
            $this->deleteFiles(json_decode($package->itinerary_pdfs));
        }
        if ($package->include_pdfs) {
            $this->deleteFiles(json_decode($package->include_pdfs));
        }
        if ($package->exclude_pdfs) {
            $this->deleteFiles(json_decode($package->exclude_pdfs));
        }

        $package->delete();

        return redirect()->route('admin.travel-package.index')
            ->with('success', 'Travel package deleted successfully!');
    }
    private function uploadFiles(Request $request, string $fieldName, string $folder)
    {
        $uploadedFiles = [];
    
        if ($request->hasFile($fieldName)) {
            foreach ($request->file($fieldName) as $file) {
                $uploadedFiles[] = $file->store("public/{$folder}");
            }
        }
    
        return $uploadedFiles ? json_encode($uploadedFiles) : null;
    }

    // Helper method to store multiple files
    private function storeMultipleFiles($request, $fieldName, $directory)
    {
        if ($request->hasFile($fieldName)) {
            return array_map(function ($file) use ($directory) {
                return $file->store($directory, 'public');
            }, $request->file($fieldName));
        }
        return [];
    }

    private function deleteFiles($files)
    {
        foreach ($files as $file) {
            Storage::delete('public/' . $file);
        }
    }

    public function toggleVisibility($id)
    {
        $package = TravelPackage::findOrFail($id);
        $package->is_visible = !$package->is_visible;
        $package->save();
        
        return redirect()->route('admin.travel-package.index')
            ->with('success', 'Package visibility updated successfully!');
    }

    // Method to show Thailand packages
    public function showThailandPackages()
    {
        $packages = TravelPackage::where('country', 'Thailand')
        ->where('is_visible', '!=', 0)
        ->get(); // Retrieve all Thailand packages
        return view('customer.thailand-packages', compact('packages'));
    }

    // Method to show Vietnam packages
    public function showVietnamPackages()
    {
        $packages = TravelPackage::where('country', 'Vietnam')
        ->where('is_visible', '!=', 0)
        ->get(); // Retrieve all Vietnam packages
        return view('customer.vietnam-packages', compact('packages'));
    }

    public function showIndonesiaPackages()
    {
        // Fetch Indonesia packages that are visible
        $packages = TravelPackage::where('country', 'Indonesia')
        ->where('is_visible', 1)
        ->get();
        
        // Return the view with the fetched packages
        return view('customer.indonesia-packages', compact('packages'));
    }

    public function showSouthKoreaPackages()
    {
        // Fetch Indonesia packages that are visible
        $packages = TravelPackage::where('country', 'South Korea')
        ->where('is_visible', 1)
        ->get();
        
        // Return the view with the fetched packages
        return view('customer.southKorea-packages', compact('packages'));
    }


    // Method to show package details
    public function showPackageDetails($country, $id)
    {
        $package = TravelPackage::findOrFail($id);
        return view('customer.package-details', compact('package'));
    }
    
    
}
