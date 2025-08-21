<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BREController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('pl', function () {
    return view('pljourney');
});

Route::get('sf-pl', function () {
    return view('sf-pljourney');
});

// BRE Management Routes
Route::prefix('bre')->name('bre.')->group(function () {
    // Dashboard
    Route::get('/', [BREController::class, 'dashboard'])->name('dashboard');
    
    // Partners Management
    Route::get('/partners', [BREController::class, 'partnersIndex'])->name('partners.index');
    Route::post('/partners', [BREController::class, 'partnersStore'])->name('partners.store');
    
    // Products Management
    Route::get('/products', [BREController::class, 'productsIndex'])->name('products.index');
    Route::post('/products', [BREController::class, 'productsStore'])->name('products.store');
    
    // Variables Management
    Route::get('/variables', [BREController::class, 'variablesIndex'])->name('variables.index');
    Route::post('/variables', [BREController::class, 'variablesStore'])->name('variables.store');
    
    // Rules Management
    Route::get('/rules', [BREController::class, 'rulesIndex'])->name('rules.index');
    Route::post('/rules', [BREController::class, 'rulesStore'])->name('rules.store');
    
    // Conditions Management
    Route::get('/conditions', [BREController::class, 'conditionsIndex'])->name('conditions.index');
    Route::post('/conditions', [BREController::class, 'conditionsStore'])->name('conditions.store');
    
    // Actions Management
    Route::get('/actions', [BREController::class, 'actionsIndex'])->name('actions.index');
    Route::post('/actions', [BREController::class, 'actionsStore'])->name('actions.store');
    
    // Evaluation
    Route::get('/evaluate', [BREController::class, 'evaluateIndex'])->name('evaluate.index');
    Route::post('/evaluate', [BREController::class, 'evaluateStore'])->name('evaluate.store');
    
    // Applications
    Route::get('/applications', [BREController::class, 'applicationsIndex'])->name('applications.index');
});

// BRE API Routes (for AJAX requests)
Route::prefix('api/bre')->name('api.bre.')->group(function () {
    Route::get('/partners', [BREController::class, 'partnersApi'])->name('partners.index');
    Route::get('/partners/{id}', [BREController::class, 'partnerShow'])->name('partners.show');
    Route::get('/partners/{id}/products', [BREController::class, 'partnerProducts'])->name('partners.products');
    Route::get('/products/{id}', [BREController::class, 'productShow'])->name('products.show');
    Route::get('/rules/{id}', [BREController::class, 'ruleShow'])->name('rules.show');
    Route::get('/variables', [BREController::class, 'variablesApi'])->name('variables.index');
    Route::get('/variables/{id}', [BREController::class, 'variableShow'])->name('variables.show');
    Route::get('/conditions', [BREController::class, 'conditionsApi'])->name('conditions.index');
    Route::get('/conditions/{id}', [BREController::class, 'conditionShow'])->name('conditions.show');
    Route::put('/conditions/{id}', [BREController::class, 'conditionUpdate'])->name('conditions.update');
    Route::delete('/conditions/{id}', [BREController::class, 'conditionDestroy'])->name('conditions.destroy');
    Route::get('/actions/{id}', [BREController::class, 'actionShow'])->name('actions.show');

});