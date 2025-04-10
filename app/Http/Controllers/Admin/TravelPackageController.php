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
        return view('admin\travel-package\index', compact('packages', 'countries', 'selectedCountry'));
    }

    public function create()
    {
        return view('admin.travel-package.create');
    }

    public function store(Request $request)
    {
        // Validate data
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
    
        // Exclude file uploads for manual handling
        $data = $request->except(['itinerary_pdfs', 'include_pdfs', 'exclude_pdfs']);
    
        // Handle file uploads and store paths
        $data['itinerary_pdfs'] = $this->uploadFiles($request, 'itinerary_pdfs', 'itinerary_pdfs');
        $data['include_pdfs'] = $this->uploadFiles($request, 'include_pdfs', 'include_pdfs');
        $data['exclude_pdfs'] = $this->uploadFiles($request, 'exclude_pdfs', 'exclude_pdfs');
    
        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }
    
        // Convert arrays to JSON for database storage
        $data['itinerary'] = $request->input('itinerary') ? json_encode($request->input('itinerary')) : null;
        $data['include'] = $request->input('include') ? json_encode($request->input('include')) : null;
        $data['exclude'] = $request->input('exclude') ? json_encode($request->input('exclude')) : null;
        $data['available_dates'] = $request->input('available_dates') ? json_encode($request->input('available_dates')) : null;
    
        // Debug data to ensure correctness before saving
        // dd($data);
    
        // Save to the database
        TravelPackage::create($data);
    
        // Redirect with success message
        return redirect()->route('admin.travel-package.index')
            ->with('success', 'Travel package created successfully!');
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

        
        // Exclude file uploads for manual handling
        $data = $request->except(['itinerary_pdfs', 'include_pdfs', 'exclude_pdfs']);

        // Handle file uploads
        $data['itinerary_pdfs'] = $this->uploadFiles($request, 'itinerary_pdfs', 'itinerary_pdfs');
        $data['include_pdfs'] = $this->uploadFiles($request, 'include_pdfs', 'include_pdfs');
        $data['exclude_pdfs'] = $this->uploadFiles($request, 'exclude_pdfs', 'exclude_pdfs');

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        // Convert arrays to JSON for database storage
        $data['itinerary'] = $request->input('itinerary') ? json_encode($request->input('itinerary')) : null;
        $data['include'] = $request->input('include') ? json_encode($request->input('include')) : null;
        $data['exclude'] = $request->input('exclude') ? json_encode($request->input('exclude')) : null;
        $data['available_dates'] = $request->input('available_dates') ? json_encode($request->input('available_dates')) : null;

        // Save to the database
        $package = TravelPackage::findOrFail($id); // Fetch the record by its ID
        $package->update($data); // Call update() on the instance
        

        // Redirect with success message
        return redirect()->route('admin.travel-package.index')
            ->with('success', 'Travel package created successfully!');
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

    public function toggleVisibility(Request $request, $id)
    {
      // Find the package
      $package = TravelPackage::findOrFail($id);

      // Toggle the visibility
      $package->is_visible = !$package->is_visible;
      $package->save();
  
      // Redirect back with a success message
      return redirect()->route('admin.travel-package.index')
          ->with('success', 'Package visibility updated successfully!');
    }

    // Method to show Thailand packages
    public function showThailandPackages()
    {
        $packages = TravelPackage::where('country', 'Thailand')
        ->where('is_visible', '!=', 0)
        ->get(); // Retrieve all Thailand packages
        return view('customer\thailand-packages', compact('packages'));
    }

    // Method to show Vietnam packages
    public function showVietnamPackages()
    {
        $packages = TravelPackage::where('country', 'Vietnam')
        ->where('is_visible', '!=', 0)
        ->get(); // Retrieve all Vietnam packages
        return view('customer\vietnam-packages', compact('packages'));
    }

    public function showIndonesiaPackages()
    {
        // Fetch Indonesia packages that are visible
        $packages = TravelPackage::where('country', 'Indonesia')
        ->where('is_visible', 1)
        ->get();
        
        // Return the view with the fetched packages
        return view('customer\indonesia-packages', compact('packages'));
    }

    public function showSouthKoreaPackages()
    {
        // Fetch Indonesia packages that are visible
        $packages = TravelPackage::where('country', 'South Korea')
        ->where('is_visible', 1)
        ->get();
        
        // Return the view with the fetched packages
        return view('customer\southkorea-packages', compact('packages'));
    }


    // Method to show package details
    public function showPackageDetails($country, $id)
    {
        $package = TravelPackage::findOrFail($id);
        return view('customer.package-details', compact('package'));
    }
    
    
}
