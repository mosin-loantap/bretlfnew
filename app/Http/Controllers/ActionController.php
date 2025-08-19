<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function index()
    {
        return response()->json(Action::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_id' => 'required|exists:rules,rule_id',
            'action_type' => 'required|string|max:100',
            'parameters' => 'nullable|json',
        ]);

        $action = Action::create($validated + [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json($action, 201);
    }

    public function show($id)
    {
        return response()->json(Action::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $action = Action::findOrFail($id);

        $validated = $request->validate([
            'rule_id' => 'required|exists:rules,rule_id',
            'action_type' => 'required|string|max:100',
            'parameters' => 'nullable|json',
        ]);

        $action->update($validated + [
            'updated_by' => auth()->id(),
        ]);

        return response()->json($action);
    }

    public function destroy($id)
    {
        Action::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
