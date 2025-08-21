<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Rule;
use App\Models\RuleCondition;
use App\Models\Variable;
use App\Models\Action;
use App\Models\Application;
use App\Services\RuleEvaluationService;

class BREController extends Controller
{
    protected $ruleEvaluationService;

    public function __construct(RuleEvaluationService $ruleEvaluationService = null)
    {
        $this->ruleEvaluationService = $ruleEvaluationService;
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'partners' => Partner::count(),
            'products' => Product::count(),
            'rules' => Rule::count(),
            'applications' => Application::count(),
            'active_rules' => Rule::where('status', true)->count(),
            'recent_applications' => Application::with(['partner', 'product'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];

        return view('bre.dashboard', compact('stats'));
    }

    /**
     * Partners Management
     */
    public function partnersIndex()
    {
        $partners = Partner::orderBy('created_at', 'desc')->get();
        return view('bre.partners.index', compact('partners'));
    }

    public function partnersStore(Request $request)
    {
        $validated = $request->validate([
            'nbfc_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:partners',
            'rbi_license_type' => 'required|string|max:255',
            'date_of_incorporation' => 'required|date',
            'business_limit' => 'required|numeric|min:0',
            'registered_address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'website_url' => 'nullable|url|max:255',
        ]);

        Partner::create($validated + [
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        return redirect()->route('bre.partners.index')
            ->with('success', 'Partner created successfully!');
    }

    public function partnerShow($id)
    {
        $partner = Partner::findOrFail($id);
        return response()->json($partner);
    }

    public function partnerProducts($id)
    {
        $products = Product::where('partner_id', $id)->get();
        return response()->json($products);
    }

    public function partnersApi()
    {
        $partners = Partner::all();
        return response()->json($partners);
    }

    public function variablesApi()
    {
        $variables = Variable::all();
        return response()->json($variables);
    }

    /**
     * Products Management
     */
    public function productsIndex()
    {
        $products = Product::with('partner')->orderBy('created_at', 'desc')->get();
        $partners = Partner::all();
        return view('bre.products.index', compact('products', 'partners'));
    }

    public function productsStore(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|max:255',
            'product_category' => 'required|string|max:255',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'min_tenure' => 'nullable|integer|min:1',
            'max_tenure' => 'nullable|integer|min:1',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        Product::create($validated + [
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        return redirect()->route('bre.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Variables Management
     */
    public function variablesIndex()
    {
        $variables = Variable::orderBy('created_at', 'desc')->get();
        $partners = Partner::all();
        return view('bre.variables.index', compact('variables', 'partners'));
    }

    public function variablesStore(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'variable_name' => 'required|string|max:255',
            'description' => 'required|string',
            'data_type' => 'required|in:string,number,boolean,date',
            'source' => 'required|string|max:255',
        ]);

        Variable::create([
            'variable_id' => 'var_' . uniqid(),
            'partner_id' => $validated['partner_id'],
            'variable_name' => $validated['variable_name'],
            'description' => $validated['description'],
            'data_type' => $validated['data_type'],
            'source' => $validated['source'],
        ]);

        return redirect()->route('bre.variables.index')
            ->with('success', 'Variable created successfully!');
    }

    /**
     * Rules Management
     */
    public function rulesIndex()
    {
        $rules = Rule::with(['partner', 'product'])->orderBy('created_at', 'desc')->get();
        $partners = Partner::all();
        $products = Product::all();
        return view('bre.rules.index', compact('rules', 'partners', 'products'));
    }

    public function rulesStore(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'product_id' => 'required|exists:products,product_id',
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|string|max:255',
            'priority' => 'required|integer|min:1',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_active' => 'boolean',
        ]);

        // Convert is_active to status for database storage
        $validated['status'] = $validated['is_active'] ?? true;
        unset($validated['is_active']);

        Rule::create($validated + [
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        return redirect()->route('bre.rules.index')
            ->with('success', 'Rule created successfully!');
    }

    /**
     * Conditions Management
     */
    public function conditionsIndex()
    {
        $conditions = RuleCondition::with('rule')->orderBy('created_at', 'desc')->get();
        $rules = Rule::all();
        $variables = Variable::all();
        return view('bre.conditions.index', compact('conditions', 'rules', 'variables'));
    }

    public function conditionsStore(Request $request)
    {
        $validated = $request->validate([
            'rule_id' => 'required|exists:rules,rule_id',
            'variable_name' => 'required|string|max:255',
            'operator' => 'required|string|max:50',
            'value' => 'required|string',
        ]);

        RuleCondition::create($validated + [
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        return redirect()->route('bre.conditions.index')
            ->with('success', 'Condition created successfully!');
    }

    /**
     * Actions Management
     */
    public function actionsIndex()
    {
        $actions = Action::with('rule')->orderBy('created_at', 'desc')->get();
        $rules = Rule::all();
        $partners = Partner::all();
        return view('bre.actions.index', compact('actions', 'rules', 'partners'));
    }

    public function actionsStore(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'rule_id' => 'required|exists:rules,rule_id',
            'action_type' => 'required|string|max:255',
            'parameters' => 'nullable|json',
        ]);

        Action::create($validated + [
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        return redirect()->route('bre.actions.index')
            ->with('success', 'Action created successfully!');
    }

    /**
     * Evaluation
     */
    public function evaluateIndex()
    {
        $partners = Partner::all();
        $products = Product::all();
        return view('bre.evaluate.index', compact('partners', 'products'));
    }

    public function evaluateStore(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'product_id' => 'required|exists:products,product_id',
            'applicant.name' => 'required|string|max:255',
            'applicant.salary' => 'required|numeric|min:0',
            'applicant.age' => 'required|integer|min:18',
            'applicant.requested_amount' => 'required|numeric|min:0',
            'applicant.requested_tenure' => 'required|integer|min:1',
        ]);

        try {
            $result = $this->ruleEvaluationService->evaluateApplication($validated);
            
            // Log the application
            if ($result['status'] !== 'error') {
                $this->ruleEvaluationService->logApplication($validated, $result);
            }

            return redirect()->route('bre.evaluate.index')
                ->with('evaluation_result', $result)
                ->with('success', 'Application evaluated successfully!');

        } catch (\Exception $e) {
            return redirect()->route('bre.evaluate.index')
                ->with('error', 'Error evaluating application: ' . $e->getMessage());
        }
    }

    /**
     * Applications
     */
    public function applicationsIndex()
    {
        $applications = Application::with(['partner', 'product'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('bre.applications.index', compact('applications'));
    }

    /**
     * API method to get all rule conditions
     */
    public function conditionsApi()
    {
        $conditions = RuleCondition::with('rule')->orderBy('created_at', 'desc')->get();
        return response()->json($conditions);
    }

    /**
     * API method to get a specific rule condition
     */
    public function conditionShow($id)
    {
        $condition = RuleCondition::with('rule')->findOrFail($id);
        return response()->json($condition);
    }

    /**
     * API method to update a rule condition
     */
    public function conditionUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'rule_id' => 'required|exists:rules,rule_id',
            'variable_name' => 'required|string|max:255',
            'operator' => 'required|string|max:50',
            'value' => 'required|string',
        ]);

        $condition = RuleCondition::findOrFail($id);
        $condition->update($validated + [
            'updated_by' => 1,
        ]);

        return response()->json($condition);
    }

    /**
     * API method to delete a rule condition
     */
    public function conditionDestroy($id)
    {
        $condition = RuleCondition::findOrFail($id);
        $condition->delete();

        return response()->json(['message' => 'Condition deleted successfully']);
    }

    /**
     * API method to get complete BRE rules for a partner and product
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductRules(Request $request)
    {
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
            $partner = Partner::find($partnerId);
            if (!$partner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Partner not found'
                ], 404);
            }

            // Find the product by name for this partner
            $product = Product::where('partner_id', $partnerId)
                              ->where('product_name', $productName)
                              ->first();
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => "Product '$productName' not found for partner '{$partner->nbfc_name}'"
                ], 404);
            }

            // Get active rules for this product
            $rules = Rule::where('product_id', $product->product_id)
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
                    // 'id' => $rule->rule_id,
                    'rule_name' => $rule->rule_name,
                    'description' => $rule->description,
                    'priority' => $rule->priority,
                    'status' => $rule->status,
                    'effective_from' => $rule->effective_from,
                    'effective_to' => $rule->effective_to,
                    'conditions' => $rule->conditions->map(function ($condition) {
                        return [
                            // 'id' => $condition->condition_id,
                            'variable_name' => $condition->variable_name,
                            'operator' => $condition->operator,
                            'value' => $condition->value
                        ];
                    }),
                    'actions' => $rule->actions->map(function ($action) {
                        return [
                            // 'id' => $action->action_id,
                            'action_type' => $action->action_type,
                            'parameters' => $action->parameters ? json_decode($action->parameters, true) : null
                        ];
                    })
                ];
            });


                // API response logging
                 \Log::info('Rules query result', [
                    'partner' => $partner->nbfc_name,
                    'product' => $product->product_name,
                    'rules_count' => $rules->count(),
                    'data' => $data->values()->all()]);

            return response()->json([
                'success' => true,
                'message' => 'Product rules retrieved successfully',
                'partner' => $partner->nbfc_name,
                'product' => $product->product_name,
                'rules_count' => $rules->count(),
                'data' => $data->values()->all()
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
    }
}
