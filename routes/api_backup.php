<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesforceController;
use App\Http\Controllers\{
    PartnerController,
    ProductController,
    RuleController,
    ConditionController,
    ActionController,
    ApplicationController,
    EvaluateLoanController,
    BREController
};
    

Route::post('salesforce/lead', [SalesforceController::class, 'createLead']);
Route::post('salesforce/lead/{lead_id}/upload', [SalesforceController::class, 'uploadFile']);

Route::post('log-application', [SalesforceController::class, 'logApplication']);

// Test route to check Salesforce CLI
Route::get('test-sf-cli', function() {
    $username = "mosin.sayyed827@agentforce.com";
    $cmd = "sf org display --target-org {$username} --json";
    exec($cmd, $output, $returnCode);
    
    return response()->json([
        'command' => $cmd,
        'return_code' => $returnCode,
        'output' => $output,
        'working_directory' => getcwd(),
        'path' => getenv('PATH')
    ]);
});

Route::apiResource('partners', PartnerController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('rules', RuleController::class);
Route::apiResource('conditions', ConditionController::class);
Route::apiResource('actions', ActionController::class);
Route::apiResource('applications', ApplicationController::class);

// Rule evaluation endpoint
Route::post('evaluate', [EvaluateLoanController::class, 'evaluate']);

// BRE API endpoints
Route::prefix('bre')->group(function () {
    Route::post('product-rules', [BREController::class, 'getProductRules']);
    Route::get('test', function() {
        return response()->json(['message' => 'API is working']);
    });
    Route::post('test-post', function(Request $request) {
        return response()->json(['message' => 'POST is working', 'data' => $request->all()]);
    });
    Route::post('product-rules-simple', function(Request $request) {
        try {
            // Debug: Log the request
            \Log::info('Product Rules API called', $request->all());
            
            // Validate input
            $request->validate([
                'partner_id' => 'required|integer|exists:partners,partner_id',
                'product_name' => 'required|string'
            ]);

            $partnerId = $request->partner_id;
            $productName = $request->product_name;

            // Find the partner
            $partner = \App\Models\Partner::find($partnerId);
            if (!$partner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Partner not found'
                ], 404);
            }

            // Find the product by name for this partner
            $product = \App\Models\Product::where('partner_id', $partnerId)
                                          ->where('product_name', $productName)
                                          ->first();
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => "Product '$productName' not found for partner '{$partner->nbfc_name}'"
                ], 404);
            }

            // Get active rules for this product
            $rules = \App\Models\Rule::where('product_id', $product->product_id)
                                      ->where('status', 1)
                                      ->with(['conditions', 'actions'])
                                      ->orderBy('priority', 'desc')
                                      ->get();

            \Log::info('Rules query result', [
                'product_id' => $product->product_id,
                'rules_count' => $rules->count(),
                'rule_ids' => $rules->pluck('rule_id')->toArray()
            ]);

            // Format response
            $data = $rules->map(function ($rule) {
                return [
                    'id' => $rule->rule_id,
                    'rule_name' => $rule->rule_name,
                    'description' => $rule->description,
                    'priority' => $rule->priority,
                    'status' => $rule->status,
                    'effective_from' => $rule->effective_from,
                    'effective_to' => $rule->effective_to,
                    'conditions' => $rule->conditions->map(function ($condition) {
                        return [
                            'id' => $condition->condition_id,
                            'variable_name' => $condition->variable_name,
                            'operator' => $condition->operator,
                            'value' => $condition->value
                        ];
                    }),
                    'actions' => $rule->actions->map(function ($action) {
                        return [
                            'id' => $action->action_id,
                            'action_type' => $action->action_type,
                            'parameters' => $action->parameters ? json_decode($action->parameters, true) : null
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Product rules retrieved successfully',
                'partner' => $partner->nbfc_name,
                'product' => $product->product_name,
                'rules_count' => $rules->count(),
                'data' => $data->values()->all()  // Ensure it's an array
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Product Rules API Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    });
});