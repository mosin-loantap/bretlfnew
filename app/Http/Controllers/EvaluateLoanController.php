<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RuleEvaluationService;
use Illuminate\Support\Facades\Log;

class EvaluateLoanController extends Controller
{
    protected $ruleEvaluationService;

    public function __construct(RuleEvaluationService $ruleEvaluationService)
    {
        $this->ruleEvaluationService = $ruleEvaluationService;
    }

    /**
     * Evaluate loan application against business rules
     */
    public function evaluate(Request $request)
    {
        try {
            // 1. Validate Request
            $validated = $request->validate([
                'partner_id' => 'required|exists:partners,partner_id',
                'product_id' => 'required|exists:products,product_id',
                'applicant' => 'required|array',
                'applicant.name' => 'sometimes|string',
                'applicant.requested_amount' => 'sometimes|numeric|min:0',
                'applicant.requested_tenure' => 'sometimes|integer|min:1',
                // Allow any additional fields in applicant array
                'applicant.*' => 'sometimes',
            ]);

            Log::info('Loan evaluation request received', $validated);

            // 2. Evaluate using service
            $result = $this->ruleEvaluationService->evaluateApplication($validated);

            // 3. Log the application for audit trail
            if ($result['status'] !== 'error') {
                $application = $this->ruleEvaluationService->logApplication($validated, $result);
                $result['application_id'] = $application->application_id;
            }

            // 4. Return result
            return response()->json($result, $result['status'] === 'error' ? 400 : 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed for loan evaluation', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error during loan evaluation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error during evaluation'
            ], 500);
        }
    }
}
