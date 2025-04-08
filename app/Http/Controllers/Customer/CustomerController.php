<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::all(); // Retrieve all customers
        return view('admin.customer.index', compact('customers'));
    }

    public function edit($id)
    {
        // Fetch the customer by ID
        $customer = User::findOrFail($id);

        // Return the edit view with the customer data
        return view('admin.customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'phone' => 'required|string|max:15', // Adjust validation rules as necessary
        ]);
    
        // Fetch the customer by ID
        $customer = User::findOrFail($id);
    
        // Update the phone number
        $customer->phone = $request->input('phone');
        $customer->save();
    
        // Redirect back with a success message
        return redirect()->route('admin.customer.index')->with('success', 'Phone number updated successfully.');
    }
    
}
