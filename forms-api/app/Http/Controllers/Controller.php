<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\Request;

class FormController extends Controller
{
    // ✅ Create a new form with fields
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array',
        ]);

        $form = Form::create([
            'name' => $data['title'],
        ]);

        foreach ($data['fields'] as $index => $field) {
            FormField::create([
                'form_id' => $form->id,
                'label' => $field['label'],
                'type' => $field['type'],
                'is_required' => $field['required'] ?? false,
                'options' => isset($field['options']) ? json_encode($field['options']) : null,
                'position' => $index,
            ]);
        }

        return response()->json($form->load('fields'), 201);
    }

    // ✅ List all forms with fields
    public function index()
    {
        $forms = Form::with('fields')->get();
        return response()->json($forms);
    }

    // ✅ Show a single form with fields
    public function show($id)
    {
        $form = Form::with('fields')->findOrFail($id);
        return response()->json($form);
    }
}
