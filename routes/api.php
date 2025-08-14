<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesforceController;

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