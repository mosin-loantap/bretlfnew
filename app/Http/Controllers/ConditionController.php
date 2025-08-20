<?php

namespace App\Http\Controllers;

use App\Models\RuleCondition;
use Illuminate\Http\Request;

class ConditionController extends Controller
{
    public function index()
    {
        return response()->json(['data' => RuleCondition::with('rule')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_id' => 'required|exists:rules,rule_id',
            'variable_name' => 'required|string|max:255',
            'operator' => 'required|string|max:50',
            'value' => 'required|string',
        ]);

        $condition = RuleCondition::create($validated + [
            'created_by' => 1, // Default system user
            'updated_by' => 1,
        ]);

        return response()->json($condition, 201);
    }

    public function show($id)
    {
        return response()->json(RuleCondition::with('rule')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $condition = RuleCondition::findOrFail($id);

        $validated = $request->validate([
            'rule_id' => 'required|exists:rules,rule_id',
            'variable_name' => 'required|string|max:255',
            'operator' => 'required|string|max:50',
            'value' => 'required|string',
        ]);

        $condition->update($validated + [
            'updated_by' => 1,
        ]);

        return response()->json($condition);
    }

    public function destroy($id)
    {
        RuleCondition::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
