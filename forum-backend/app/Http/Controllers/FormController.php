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
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'nullable|array', // Optional, must be array if provided
        ]);

        // Store the form
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

}



