<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
{
    $forms = Form::all();
    return response()->json(['data' => $forms], 200);
}

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'nullable|array', 
        ]);

       
        $form = Form::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'fields' => $validated['fields'] ?? [],
        ]);

        return response()->json([
            'message' => 'Form created',
            'data' => $form,
        ], 201);
    }

     public function show($id)
    {
        $form = Form::find($id);

        if (!$form) {
            return response()->json(['message' => 'Form not found'], 404);
        }

        return response()->json(['data' => $form], 200);
    }

    public function destroy($id)
{
    $form = Form::find($id);

    if (!$form) {
        return response()->json(['message' => 'Form not found'], 404);
    }

    $form->delete();

    return response()->json(['message' => 'Form deleted successfully'], 200);
}

public function update(Request $request, $id)
{
    $form = Form::find($id);

    if (!$form) {
        return response()->json(['message' => 'Form not found'], 404);
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'fields' => 'nullable|array',
    ]);

    $form->name = $validated['name'];
    $form->description = $validated['description'] ?? null;
    $form->fields = $validated['fields'] ?? [];

    $form->save();

    return response()->json(['message' => 'Form updated', 'data' => $form], 200);
}


}



