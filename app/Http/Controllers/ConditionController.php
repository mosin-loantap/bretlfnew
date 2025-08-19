<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use Illuminate\Http\Request;

class ConditionController extends Controller
{
    public function index()
    {
        return response()->json(Condition::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_id' => 'required|exists:rules,rule_id',
            'field_name' => 'required|string|max:255',
            'operator' => 'required|string|max:50',
            'value' => 'required|string|max:255',
            'logical_operator' => 'nullable|in:AND,OR',
        ]);

        $condition = Condition::create($validated + [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json($condition, 201);
    }

    public function show($id)
    {
        return response()->json(Condition::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $condition = Condition::findOrFail($id);

        $validated = $request->validate([
            'rule_id' => 'required|exists:rules,rule_id',
            'field_name' => 'required|string|max:255',
            'operator' => 'required|string|max:50',
            'value' => 'required|string|max:255',
            'logical_operator' => 'nullable|in:AND,OR',
        ]);

        $condition->update($validated + [
            'updated_by' => auth()->id(),
        ]);

        return response()->json($condition);
    }

    public function destroy($id)
    {
        Condition::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
