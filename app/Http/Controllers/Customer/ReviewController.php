<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PrivateBooking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index()
    {
        // Get all reviews without requiring authentication
        $reviews = Review::with(['user', 'booking.travelPackage', 'privateBooking.travelPackage'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        return view('customer.reviews.index', compact('reviews'));
    }
    
    public function create($bookingId, $isPrivate = false)
    {
        if ($isPrivate) {
            $booking = PrivateBooking::findOrFail($bookingId);
        } else {
            $booking = Booking::findOrFail($bookingId);
        }
        
        // Check if user has already reviewed this booking
        $existingReview = Review::where('user_id', Auth::id())
            ->where($isPrivate ? 'private_booking_id' : 'booking_id', $bookingId)
            ->first();
            
        if ($existingReview) {
            return redirect()->route('customer.review.edit', $existingReview->id)
                ->with('info', 'You have already reviewed this trip. You can edit your review below.');
        }
        
        return view('customer.booking.review-form', compact('booking', 'isPrivate'));
    }
    
    public function store(Request $request, $bookingId, $isPrivate = false)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:10|max:1000',
            'review_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $review = new Review();
        $review->user_id = Auth::id();
        $review->rating = $request->rating;
        $review->review_text = $request->review_text;
        
        // Handle photo upload
        if ($request->hasFile('review_photo')) {
            $photoPath = $request->file('review_photo')->store('review-photos', 'public');
            $review->photo_path = $photoPath;
        }
        
        if ($isPrivate) {
            $review->private_booking_id = $bookingId;
        } else {
            $review->booking_id = $bookingId;
        }
        
        $review->save();
        
        return redirect()->route('customer.reviews.index')
            ->with('success', 'Your review has been submitted successfully!');
    }
    
    /**
     * Show the form for editing the specified review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $review = Review::findOrFail($id);
        
        // Check if user owns this review
        if ($review->user_id !== auth()->id()) {
            return redirect()->route('customer.reviews.index')
                ->with('error', 'You are not authorized to edit this review.');
        }
        
        // Check if review has been edited before
        if ($review->has_been_edited) {
            return redirect()->route('customer.reviews.index')
                ->with('error', 'This review has already been edited once and cannot be modified again.');
        }
        
        return view('customer.reviews.edit', compact('review'));
    }
    
    /**
     * Update the specified review in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        
        // Check if user owns this review
        if ($review->user_id !== auth()->id()) {
            return redirect()->route('customer.reviews.index')
                ->with('error', 'You are not authorized to edit this review.');
        }
        
        // Check if review has been edited before
        if ($review->has_been_edited) {
            return redirect()->route('customer.reviews.index')
                ->with('error', 'This review has already been edited once and cannot be modified again.');
        }
        
        // Validate the request
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string',
            'photo' => 'nullable|image|max:2048', // 2MB max
        ]);
        
        // Update review data
        $review->rating = $request->rating;
        $review->review_text = $request->review_text;
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($review->photo_path && Storage::disk('public')->exists($review->photo_path)) {
                Storage::disk('public')->delete($review->photo_path);
            }
            
            // Store new photo
            $photoPath = $request->file('photo')->store('reviews', 'public');
            $review->photo_path = $photoPath;
        } elseif ($request->has('remove_photo') && $request->remove_photo == 1) {
            // Remove photo if checkbox is checked
            if ($review->photo_path && Storage::disk('public')->exists($review->photo_path)) {
                Storage::disk('public')->delete($review->photo_path);
            }
            $review->photo_path = null;
        }
        
        // Mark as edited
        $review->has_been_edited = true;
        
        // Save changes
        $review->save();
        
        return redirect()->route('customer.reviews.index')
            ->with('success', 'Review updated successfully. Note that this review cannot be edited again.');
    }
    
    public function destroy($id)
    {
        $review = Review::where('user_id', Auth::id())->findOrFail($id);
        
        // Delete photo if exists
        if ($review->photo_path) {
            Storage::disk('public')->delete($review->photo_path);
        }
        
        $review->delete();
        
        return redirect()->route('customer.reviews.index')
            ->with('success', 'Your review has been deleted successfully!');
    }
}