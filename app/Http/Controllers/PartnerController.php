<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        return response()->json(Partner::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nbfc_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:100|unique:partners,registration_number',
            'rbi_license_type' => 'required|string|max:50',
            'date_of_incorporation' => 'required|date',
            'business_limit' => 'required|numeric|min:0',
        ]);

        $partner = Partner::create($validated + [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json($partner, 201);
    }

    public function show($id)
    {
        return response()->json(Partner::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        $validated = $request->validate([
            'nbfc_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:100|unique:partners,registration_number,' . $partner->partner_id . ',partner_id',
            'rbi_license_type' => 'required|string|max:50',
            'date_of_incorporation' => 'required|date',
            'business_limit' => 'required|numeric|min:0',
        ]);

        $partner->update($validated + [
            'updated_by' => auth()->id(),
        ]);

        return response()->json($partner);
    }

    public function destroy($id)
    {
        Partner::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
