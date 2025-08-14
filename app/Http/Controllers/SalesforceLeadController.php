<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SalesforceService;

class SalesforceLeadController extends Controller
{
    public function store(Request $request, SalesforceService $sfService)
    {
        // Validate incoming request
        $validated = $request->validate([
            'FirstName' => 'required|string',
            'LastName'  => 'required|string',
            'Company'   => 'required|string',
            'Email'     => 'nullable|email',
            'Phone'     => 'nullable|string'
        ]);

        try {
            $result = $sfService->createLead($validated);
            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadFile(Request $request, SalesforceService $sfService)
    {
        // Validate incoming request
        $validated = $request->validate([
            'lead_id' => 'required|string',
            'file' => 'required|file|max:25000', // Max 25MB
            'title' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        try {
            $file = $request->file('file');
            $title = $validated['title'] ?? $file->getClientOriginalName();
            $description = $validated['description'] ?? 'Uploaded via API';

            $result = $sfService->uploadFileToLead(
                $validated['lead_id'],
                $file,
                $title,
                $description
            );

            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
