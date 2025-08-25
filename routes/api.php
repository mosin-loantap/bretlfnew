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
    
    // Partners API
    Route::get('/partners', [BREController::class, 'partnersApi']);
    Route::get('/partners/{id}', [BREController::class, 'partnerShow']);
    Route::get('/partners/{id}/products', [BREController::class, 'partnerProducts']);
    
    // Products API
    Route::get('/products/{id}', [BREController::class, 'productShow']);
    
    // Rules API
    Route::get('/rules', [BREController::class, 'rulesApi']);
    Route::get('/rules/{id}', [BREController::class, 'ruleShow']);
    Route::put('/rules/{id}', [BREController::class, 'ruleUpdate']);
    Route::delete('/rules/{id}', [BREController::class, 'ruleDestroy']);
    
    // Variables API
    Route::get('/variables', [BREController::class, 'variablesApi']);
    Route::get('/variables/{id}', [BREController::class, 'variableShow']);
    
    // Conditions API
    Route::get('/conditions', [BREController::class, 'conditionsApi']);
    Route::get('/conditions/{id}', [BREController::class, 'conditionShow']);
    Route::put('/conditions/{id}', [BREController::class, 'conditionUpdate']);
    Route::delete('/conditions/{id}', [BREController::class, 'conditionDestroy']);
    
    // Actions API
    Route::get('/actions/{id}', [BREController::class, 'actionShow']);
    
    // Evaluation API
    Route::post('/evaluate', [BREController::class, 'evaluateStore']);
});
