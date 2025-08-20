<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\Product;
use App\Models\Rule;
use App\Models\RuleCondition;
use App\Models\Variable;
use App\Models\Action;
use App\Models\Application;
use Illuminate\Support\Facades\Log;

class RuleEvaluationService
{
    /**
     * Evaluate loan application against business rules
     */
    public function evaluateApplication(array $data): array
    {
        $partnerId = $data['partner_id'];
        $productId = $data['product_id'];
        $applicant = $data['applicant'];

        // 1. Validate partner and product
        $partner = Partner::find($partnerId);
        $product = Product::find($productId);

        if (!$partner) {
            return ['status' => 'error', 'message' => 'Invalid Partner'];
        }

        if (!$product) {
            return ['status' => 'error', 'message' => 'Invalid Product'];
        }

        // 2. Get active rules for the product, ordered by priority
        $rules = Rule::where('product_id', $productId)
            ->where('status', true)
            ->whereDate('effective_from', '<=', now())
            ->where(function($query) {
                $query->whereNull('effective_to')
                      ->orWhereDate('effective_to', '>=', now());
            })
            ->orderBy('priority', 'desc')
            ->get();

        if ($rules->isEmpty()) {
            return ['status' => 'error', 'message' => 'No active rules configured for this product'];
        }

        Log::info('Evaluating loan application', [
            'partner_id' => $partnerId,
            'product_id' => $productId,
            'rules_count' => $rules->count(),
            'applicant' => $applicant
        ]);

        // 3. Evaluate each rule
        foreach ($rules as $rule) {
            $ruleResult = $this->evaluateRule($rule, $applicant);
            
            if ($ruleResult['matched']) {
                $action = Action::where('rule_id', $rule->rule_id)->first();
                
                $result = [
                    'status' => $action ? $action->action_type : 'approved',
                    'rule_id' => $rule->rule_id,
                    'rule_name' => $rule->rule_name,
                    'conditions_evaluated' => $ruleResult['conditions_evaluated'],
                    'action' => $action && $action->parameters ? 
                        json_decode($action->parameters, true) : null,
                ];

                Log::info('Rule matched', $result);
                return $result;
            }
        }

        // 4. No rules matched - default to rejection
        $result = [
            'status' => 'rejected',
            'message' => 'No matching rules found for applicant criteria',
            'rules_evaluated' => $rules->count()
        ];

        Log::info('No rules matched', $result);
        return $result;
    }

    /**
     * Evaluate a single rule against applicant data
     */
    private function evaluateRule(Rule $rule, array $applicant): array
    {
        $conditions = RuleCondition::where('rule_id', $rule->rule_id)->get();

        $conditionsEvaluated = [];
        $allConditionsPass = true;

        foreach ($conditions as $condition) {
            $variableName = $condition->variable_name;
            $applicantValue = $applicant[$variableName] ?? null;
            
            // Get data type from variables table for proper conversion
            $dataType = $condition->getVariableDataType();
            
            $conditionPassed = $this->evaluateCondition(
                $applicantValue, 
                $condition->operator, 
                $condition->value,
                $dataType
            );

            $conditionsEvaluated[] = [
                'variable' => $variableName,
                'operator' => $condition->operator,
                'expected_value' => $condition->value,
                'actual_value' => $applicantValue,
                'passed' => $conditionPassed
            ];

            if (!$conditionPassed) {
                $allConditionsPass = false;
                // Continue evaluating other conditions for debugging purposes
            }
        }

        return [
            'matched' => $allConditionsPass,
            'conditions_evaluated' => $conditionsEvaluated
        ];
    }

    /**
     * Evaluate a single condition
     */
    private function evaluateCondition($applicantValue, string $operator, $expectedValue, string $dataType): bool
    {
        // Handle null/missing values
        if ($applicantValue === null) {
            return $operator === 'is_null' || 
                   ($operator === '=' && $expectedValue === null);
        }

        // Convert values based on data type
        $applicantValue = $this->convertValueByDataType($applicantValue, $dataType);
        $expectedValue = $this->convertValueByDataType($expectedValue, $dataType);

        switch ($operator) {
            case '=':
            case 'equals':
                return $applicantValue == $expectedValue;
                
            case '!=':
            case 'not_equals':
                return $applicantValue != $expectedValue;
                
            case '>':
            case 'greater_than':
                return $applicantValue > $expectedValue;
                
            case '<':
            case 'less_than':
                return $applicantValue < $expectedValue;
                
            case '>=':
            case 'greater_than_or_equal':
                return $applicantValue >= $expectedValue;
                
            case '<=':
            case 'less_than_or_equal':
                return $applicantValue <= $expectedValue;
                
            case 'in':
                $expectedValues = is_array($expectedValue) ? 
                    $expectedValue : explode(',', $expectedValue);
                return in_array($applicantValue, array_map('trim', $expectedValues));
                
            case 'not_in':
                $expectedValues = is_array($expectedValue) ? 
                    $expectedValue : explode(',', $expectedValue);
                return !in_array($applicantValue, array_map('trim', $expectedValues));
                
            case 'contains':
                return str_contains(strtolower($applicantValue), strtolower($expectedValue));
                
            case 'not_contains':
                return !str_contains(strtolower($applicantValue), strtolower($expectedValue));
                
            case 'starts_with':
                return str_starts_with(strtolower($applicantValue), strtolower($expectedValue));
                
            case 'ends_with':
                return str_ends_with(strtolower($applicantValue), strtolower($expectedValue));
                
            case 'is_null':
                return $applicantValue === null;
                
            case 'is_not_null':
                return $applicantValue !== null;
                
            case 'between':
                $range = explode(',', $expectedValue);
                if (count($range) === 2) {
                    $min = $this->convertValueByDataType(trim($range[0]), $dataType);
                    $max = $this->convertValueByDataType(trim($range[1]), $dataType);
                    return $applicantValue >= $min && $applicantValue <= $max;
                }
                return false;
                
            default:
                Log::warning('Unknown operator in rule condition', [
                    'operator' => $operator,
                    'applicant_value' => $applicantValue,
                    'expected_value' => $expectedValue
                ]);
                return false;
        }
    }

    /**
     * Convert value based on data type
     */
    private function convertValueByDataType($value, string $dataType)
    {
        if ($value === null) {
            return null;
        }

        switch ($dataType) {
            case 'integer':
            case 'int':
                return (int) $value;
                
            case 'decimal':
            case 'float':
            case 'double':
                return (float) $value;
                
            case 'boolean':
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                
            case 'string':
            case 'text':
            default:
                return (string) $value;
        }
    }

    /**
     * Store the application for audit trail
     */
    public function logApplication(array $data, array $result): Application
    {
        return Application::create([
            'partner_id' => $data['partner_id'],
            'product_id' => $data['product_id'],
            'customer_name' => $data['applicant']['name'] ?? 'Unknown',
            'requested_amount' => $data['applicant']['requested_amount'] ?? 0,
            'requested_tenure' => $data['applicant']['requested_tenure'] ?? 0,
            'status' => $result['status'],
            'created_by' => 1, // System user
            'updated_by' => 1
        ]);
    }
}
