<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        return response()->json(Application::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'product_id' => 'required|exists:products,product_id',
            'customer_name' => 'required|string|max:255',
            'customer_dob' => 'required|date',
            'customer_pan' => 'required|string|max:20|unique:applications,customer_pan',
            'requested_amount' => 'required|numeric|min:0',
            'requested_tenure' => 'required|integer|min:1',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $application = Application::create($validated + [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json($application, 201);
    }

    public function show($id)
    {
        return response()->json(Application::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'product_id' => 'required|exists:products,product_id',
            'customer_name' => 'required|string|max:255',
            'customer_dob' => 'required|date',
            'customer_pan' => 'required|string|max:20|unique:applications,customer_pan,' . $application->application_id . ',application_id',
            'requested_amount' => 'required|numeric|min:0',
            'requested_tenure' => 'required|integer|min:1',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $application->update($validated + [
            'updated_by' => auth()->id(),
        ]);

        return response()->json($application);
    }

    public function destroy($id)
    {
        Application::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
