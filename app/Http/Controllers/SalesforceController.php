<?php

namespace App\Http\Controllers;

use App\Services\SalesforceService;
use Illuminate\Http\Request;

class SalesforceController extends Controller
{
    protected $salesforce;

    public function __construct(SalesforceService $salesforce)
    {
        $this->salesforce = $salesforce;
    }

    public function createLead(Request $request)
    {
        $validated = $request->validate([
            'FirstName'   => 'required|string',
            'LastName'    => 'required|string',
            'Company'     => 'required|string',
            'Email'       => 'nullable|email',
            'Phone'       => 'nullable|string'
        ]);

        try {
            $result = $this->salesforce->createLead($validated);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadFile(Request $request, $leadId)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'title' => 'required|string',
            'description' => 'nullable|string'
        ]);

        try {
            $file = $request->file('file');
            $title = $request->input('title');
            $description = $request->input('description', '');

            $result = $this->salesforce->uploadFileToLead($leadId, $file, $title, $description);
            
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

    public function logApplication(Request $request)
    {
        try {
            // Validate the full application data
            $validated = $request->validate([
                'email' => 'required|email',
                'mobile' => 'required|string',
                'pincode' => 'required|string',
                'city' => 'required|string',
                'income' => 'required|numeric',
                'name' => 'required|string',
                'pan' => 'required|string',
                'dob' => 'required|date',
                'employer' => 'required|string',
                'officialEmail' => 'required|email',
                'experience' => 'required|string',
                'gender' => 'required|string',
                'maritalStatus' => 'required|string',
                'fatherName' => 'required|string',
                'requestedLoanAmount' => 'required|numeric|min:50000|max:5000000',
                'loanPurpose' => 'required|string',
                'addressLine1' => 'required|string',
                'addressLine2' => 'nullable|string'
            ]);

            // Map the data to Salesforce Lead format
            $leadData = [
                'FirstName' => explode(' ', $validated['name'])[0],
                'LastName' => substr($validated['name'], strlen(explode(' ', $validated['name'])[0]) + 1) ?: 'N/A',
                'Company' => $validated['employer'],
                'Email' => $validated['email'],
                'Phone' => $validated['mobile'],
                'Street' => $validated['addressLine1'] . ($validated['addressLine2'] ? ', ' . $validated['addressLine2'] : ''),
                'City' => $validated['city'],
                'PostalCode' => $validated['pincode'],
                'LeadSource' => 'Website',
                'Status' => 'Open - Not Contacted',
                'Industry' => 'Financial Services',
                'Description' => "Loan Application Details:\n" .
                    "PAN: {$validated['pan']}\n" .
                    "DOB: {$validated['dob']}\n" .
                    "Gender: {$validated['gender']}\n" .
                    "Marital Status: {$validated['maritalStatus']}\n" .
                    "Father's Name: {$validated['fatherName']}\n" .
                    "Official Email: {$validated['officialEmail']}\n" .
                    "Experience: {$validated['experience']}\n" .
                    "Income: ₹{$validated['income']}\n" .
                    "Requested Loan Amount: ₹{$validated['requestedLoanAmount']}\n" .
                    "Loan Purpose: {$validated['loanPurpose']}"
            ];

            $result = $this->salesforce->createLead($leadData);

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Application logged successfully to Salesforce',
                    'lead_id' => $result['data']['id']
                ]);
            } else {
                return response()->json($result, 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to log application: ' . $e->getMessage()
            ], 500);
        }
    }
}
