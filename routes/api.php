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
});
