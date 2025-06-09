<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewReply;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = Review::with(['user', 'booking.travelPackage', 'privateBooking.travelPackage', 'reply'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show a single review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $review = Review::with(['user', 'booking.travelPackage', 'privateBooking.travelPackage', 'reply'])
            ->findOrFail($id);
            
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Reply to a review.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reply(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        
        $request->validate([
            'reply_text' => 'required|string|min:5',
        ]);
        
        // Check if a reply already exists
        if ($review->reply) {
            $review->reply->update([
                'reply_text' => $request->reply_text,
            ]);
            
            $message = 'Reply updated successfully!';
        } else {
            // Create a new reply
            ReviewReply::create([
                'review_id' => $review->id,
                'reply_text' => $request->reply_text,
            ]);
            
            $message = 'Reply added successfully!';
        }
        
        return redirect()->route('admin.reviews.show', $review->id)
            ->with('success', $message);
    }

    /**
     * Delete a reply.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteReply($id)
    {
        $review = Review::findOrFail($id);
        
        if ($review->reply) {
            $review->reply->delete();
            return redirect()->route('admin.reviews.show', $review->id)
                ->with('success', 'Reply deleted successfully!');
        }
        
        return redirect()->route('admin.reviews.show', $review->id)
            ->with('error', 'No reply found to delete.');
    }
}