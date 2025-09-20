<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Support\Facades\DB;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = Form::withCount('submissions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($form) {
                return [
                    'id' => $form->id,
                    'name' => $form->name,
                    'description' => $form->description,
                    'is_active' => $form->is_active,
                    'submissions_count' => $form->submissions_count,
                    'created_at' => $form->created_at,
                    'updated_at' => $form->updated_at,
                ];
            });

        return response()->json($forms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,textarea,checkbox,radio',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $form = Form::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => true
            ]);

            foreach ($validated['fields'] as $index => $fieldData) {
                FormField::create([
                    'form_id' => $form->id,
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type'],
                    'required' => $fieldData['required'] ?? false,
                    'options' => $fieldData['options'] ?? null,
                    'order_index' => $index
                ]);
            }

            DB::commit();

            $form->load('fields');
            return response()->json($form, 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create form'], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $form = Form::with('fields')->findOrFail($id);
        return response()->json($form);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $form = Form::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.id' => 'nullable|integer',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,textarea,checkbox,radio',
            'fields.*.required' => 'boolean',
            'fields.*.options' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $form->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            // Delete existing fields
            $form->fields()->delete();

            // Create new fields
            foreach ($validated['fields'] as $index => $fieldData) {
                FormField::create([
                    'form_id' => $form->id,
                    'label' => $fieldData['label'],
                    'type' => $fieldData['type'],
                    'required' => $fieldData['required'] ?? false,
                    'options' => $fieldData['options'] ?? null,
                    'order_index' => $index
                ]);
            }

            DB::commit();

            $form->load('fields');
            return response()->json($form);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to update form'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $form = Form::findOrFail($id);
        $form->delete();

        return response()->json(['message' => 'Form deleted successfully']);
    }
}
