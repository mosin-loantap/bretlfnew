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
            ->where('is_active', true)
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

        // 3. Evaluate each rule with marks system
        $allRulesResults = [];
        
        foreach ($rules as $rule) {
            $ruleResult = $this->evaluateRuleWithMarks($rule, $applicant);
            $allRulesResults[] = [
                'rule_name' => $rule->rule_name,
                'priority' => $rule->priority,
                'total_marks' => $rule->total_marks,
                'achieved_marks' => $ruleResult['achieved_marks'],
                'pass_percentage' => $ruleResult['pass_percentage'],
                'mandatory_failed' => $ruleResult['mandatory_failed'],
                'failed_conditions' => $ruleResult['failed_conditions'],
                'conditions_evaluated' => $ruleResult['conditions_evaluated']
            ];
            
            // If mandatory conditions failed, this rule fails
            if (!empty($ruleResult['mandatory_failed'])) {
                continue; // Skip to next rule
            }
            
            // If rule passes the mark threshold (e.g., 60%), consider it matched
            if ($ruleResult['pass_percentage'] >= 60) {
                $action = Action::where('rule_id', $rule->rule_id)->first();
                
                // Create user-friendly condition messages
                $conditionMessages = $this->formatConditionMessages($ruleResult['conditions_evaluated']);
                
                $result = [
                    'status' => $action ? $action->action_type : 'approved',
                    'rule_name' => $rule->rule_name,
                    'achieved_marks' => $ruleResult['achieved_marks'],
                    'total_marks' => $rule->total_marks,
                    'pass_percentage' => $ruleResult['pass_percentage'],
                    'evaluation_summary' => [
                        'passed_conditions' => $conditionMessages['passed'],
                        'failed_conditions' => $conditionMessages['failed'],
                        'total_conditions' => count($ruleResult['conditions_evaluated']),
                        'mandatory_conditions_status' => empty($ruleResult['mandatory_failed']) ? 'All passed' : 'Some failed'
                    ],
                    'action' => $action && $action->parameters ? 
                        json_decode($action->parameters, true) : null
                ];

                Log::info('Rule matched with marks', $result);
                return $result;
            }
        }

        // 4. No rules passed - return detailed failure analysis
        $allFailedConditions = $this->formatAllFailedConditions($allRulesResults);
        
        $result = [
            'status' => 'rejected',
            'message' => 'Application does not meet the minimum criteria',
            'rules_evaluated' => $rules->count(),
            'failure_summary' => [
                'failed_conditions' => $allFailedConditions,
                'mandatory_failures' => $this->extractMandatoryFailures($allRulesResults),
                'highest_score_achieved' => $this->getHighestScore($allRulesResults)
            ]
        ];

        Log::info('No rules passed evaluation', $result);
        return $result;
    }

    /**
     * Evaluate a single rule with marks system
     */
    private function evaluateRuleWithMarks(Rule $rule, array $applicant): array
    {
        $conditions = RuleCondition::where('rule_id', $rule->rule_id)->get();

        $conditionsEvaluated = [];
        $achievedMarks = 0;
        $mandatoryFailed = [];
        $failedConditions = [];

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

            $conditionResult = [
                'variable' => $variableName,
                'operator' => $condition->operator,
                'expected_value' => $condition->value,
                'actual_value' => $applicantValue,
                'marks' => $condition->marks,
                'is_mandatory' => $condition->is_mandatory,
                'passed' => $conditionPassed
            ];

            $conditionsEvaluated[] = $conditionResult;

            // Award marks if condition passed
            if ($conditionPassed) {
                $achievedMarks += $condition->marks;
            } else {
                // Track failed conditions
                $failedConditions[] = $variableName . ' condition is not satisfied';
                
                // Track mandatory failures
                if ($condition->is_mandatory) {
                    $mandatoryFailed[] = $variableName . ' condition is not satisfied (mandatory)';
                }
            }
        }

        $passPercentage = $rule->total_marks > 0 ? 
            ($achievedMarks / $rule->total_marks) * 100 : 0;

        return [
            'achieved_marks' => $achievedMarks,
            'pass_percentage' => round($passPercentage, 2),
            'mandatory_failed' => $mandatoryFailed,
            'failed_conditions' => $failedConditions,
            'conditions_evaluated' => $conditionsEvaluated
        ];
    }

    /**
     * Summarize failed conditions across all rules
     */
    private function summarizeFailedConditions(array $allRulesResults): array
    {
        $summary = [];
        
        foreach ($allRulesResults as $ruleResult) {
            if (!empty($ruleResult['failed_conditions'])) {
                $summary[$ruleResult['rule_name']] = $ruleResult['failed_conditions'];
            }
            
            if (!empty($ruleResult['mandatory_failed'])) {
                $summary[$ruleResult['rule_name'] . ' (Mandatory Failures)'] = $ruleResult['mandatory_failed'];
            }
        }
        
        return $summary;
    }

    /**
     * Evaluate a single rule against applicant data (legacy method for backward compatibility)
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

    /**
     * Format condition messages for user-friendly display
     */
    private function formatConditionMessages(array $conditions): array
    {
        $passed = [];
        $failed = [];

        foreach ($conditions as $condition) {
            $message = $this->createConditionMessage($condition);
            
            if ($condition['passed']) {
                $passed[] = $message;
            } else {
                $failed[] = $message . ($condition['is_mandatory'] ? ' (Mandatory)' : '');
            }
        }

        return [
            'passed' => $passed,
            'failed' => $failed
        ];
    }

    /**
     * Create a human-readable condition message
     */
    private function createConditionMessage(array $condition): string
    {
        $variable = ucfirst(str_replace('_', ' ', $condition['variable']));
        $operator = $this->formatOperator($condition['operator']);
        $expected = $condition['expected_value'];
        $actual = $condition['actual_value'];

        if ($condition['passed']) {
            return "{$variable} {$operator} {$expected} ✓ (Your value: {$actual})";
        } else {
            return "{$variable} {$operator} {$expected} ✗ (Your value: {$actual})";
        }
    }

    /**
     * Format operator for display
     */
    private function formatOperator(string $operator): string
    {
        $operators = [
            'greater_than_or_equal' => 'should be at least',
            'less_than_or_equal' => 'should be at most',
            'greater_than' => 'should be greater than',
            'less_than' => 'should be less than',
            'equals' => 'should be',
            'not_equals' => 'should not be',
            'contains' => 'should contain',
            'not_contains' => 'should not contain',
            'in' => 'should be one of',
            'not_in' => 'should not be one of',
            'between' => 'should be between'
        ];

        return $operators[$operator] ?? $operator;
    }

    /**
     * Format all failed conditions across rules
     */
    private function formatAllFailedConditions(array $allRulesResults): array
    {
        $failedByRule = [];

        foreach ($allRulesResults as $ruleResult) {
            if (!empty($ruleResult['failed_conditions']) || !empty($ruleResult['mandatory_failed'])) {
                $ruleName = $ruleResult['rule_name'];
                $failedByRule[$ruleName] = [];

                // Add regular failed conditions
                foreach ($ruleResult['conditions_evaluated'] as $condition) {
                    if (!$condition['passed']) {
                        $message = $this->createConditionMessage($condition);
                        $failedByRule[$ruleName][] = $message;
                    }
                }
            }
        }

        return $failedByRule;
    }

    /**
     * Extract mandatory failure messages
     */
    private function extractMandatoryFailures(array $allRulesResults): array
    {
        $mandatoryFailures = [];

        foreach ($allRulesResults as $ruleResult) {
            if (!empty($ruleResult['mandatory_failed'])) {
                foreach ($ruleResult['conditions_evaluated'] as $condition) {
                    if (!$condition['passed'] && $condition['is_mandatory']) {
                        $message = $this->createConditionMessage($condition);
                        $mandatoryFailures[] = $message;
                    }
                }
            }
        }

        return array_unique($mandatoryFailures);
    }

    /**
     * Get the highest score achieved across all rules
     */
    private function getHighestScore(array $allRulesResults): array
    {
        $highest = ['rule_name' => 'None', 'percentage' => 0, 'marks' => 0, 'total' => 0];

        foreach ($allRulesResults as $ruleResult) {
            if ($ruleResult['pass_percentage'] > $highest['percentage']) {
                $highest = [
                    'rule_name' => $ruleResult['rule_name'],
                    'percentage' => $ruleResult['pass_percentage'],
                    'marks' => $ruleResult['achieved_marks'],
                    'total' => $ruleResult['total_marks']
                ];
            }
        }

        return $highest;
    }
}
