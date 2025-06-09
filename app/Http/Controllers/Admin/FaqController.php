<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQs.
     */
    public function index(Request $request)
    {
        $query = Faq::query();
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }
        
        // Apply type filter if provided
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }
        
        // Get results with ordering
        $faqs = $query->orderBy('display_order')->get();
        
        return view('admin.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create()
    {
        return view('admin.faqs.create');
    }

    /**
     * Store a newly created FAQ in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_published' => 'boolean',
            'display_order' => 'integer|nullable',  // Fixed missing comma
            'type' => 'required|string|in:all,booking,payment,travel',
        ]);

        // Set default display order if not provided
        if (!isset($validated['display_order'])) {
            $maxOrder = Faq::max('display_order') ?? 0;
            $validated['display_order'] = $maxOrder + 1;
        }

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

    /**
     * Show the form for editing the specified FAQ.
     */
    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    /**
     * Update the specified FAQ in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_published' => 'boolean',
            'display_order' => 'integer|nullable',
            'type' => 'required|string|in:all,booking,payment,travel'  // Add type validation
        ]);

        // Handle checkbox for is_published
        $validated['is_published'] = $request->has('is_published');

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    /**
     * Remove the specified FAQ from storage.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    /**
 * Toggle the visibility status of the specified FAQ.
 */
public function toggleVisibility(Faq $faq)
{
    $faq->update([
        'is_published' => !$faq->is_published
    ]);

    return redirect()->route('admin.faqs.index')
        ->with('success', $faq->is_published ? 'FAQ published successfully.' : 'FAQ unpublished successfully.');
}
}