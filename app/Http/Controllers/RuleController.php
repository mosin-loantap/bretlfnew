<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function index()
    {
        return response()->json(Rule::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'product_id' => 'required|exists:products,product_id',
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|string|max:100',
            'priority' => 'required|integer|min:1',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'status' => 'required|in:active,inactive',
        ]);

        $rule = Rule::create($validated + [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json($rule, 201);
    }

    public function show($id)
    {
        return response()->json(Rule::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $rule = Rule::findOrFail($id);

        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,partner_id',
            'product_id' => 'required|exists:products,product_id',
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|string|max:100',
            'priority' => 'required|integer|min:1',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'status' => 'required|in:active,inactive',
        ]);

        $rule->update($validated + [
            'updated_by' => auth()->id(),
        ]);

        return response()->json($rule);
    }

    public function destroy($id)
    {
        Rule::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
