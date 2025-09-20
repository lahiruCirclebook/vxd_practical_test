<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\SubmissionAnswer;
use Illuminate\Support\Facades\DB;

class SubmissionController extends Controller
{
    public function store(Request $request, $formId)
    {
        $form = Form::with('fields')->findOrFail($formId);

        // Build validation rules dynamically
        $rules = [];
        foreach ($form->fields as $field) {
            $fieldRules = [];

            if ($field->required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field->type) {
                case 'text':
                    $fieldRules[] = 'string|max:255';
                    break;
                case 'textarea':
                    $fieldRules[] = 'string|max:5000';
                    break;
                case 'checkbox':
                    $fieldRules[] = 'array';
                    break;
                case 'radio':
                    $fieldRules[] = 'string';
                    break;
            }

            $rules["field_{$field->id}"] = implode('|', $fieldRules);
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $submission = FormSubmission::create([
                'form_id' => $form->id,
                'ip_address' => $request->ip(),
                'submitted_at' => now()
            ]);

            foreach ($form->fields as $field) {
                $fieldKey = "field_{$field->id}";

                if (isset($validated[$fieldKey])) {
                    $value = $validated[$fieldKey];

                    // Handle different field types
                    if ($field->type === 'checkbox' && is_array($value)) {
                        $answerValue = $value;
                    } else {
                        $answerValue = [$value];
                    }

                    SubmissionAnswer::create([
                        'submission_id' => $submission->id,
                        'form_field_id' => $field->id,
                        'answer_value' => $answerValue
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Form submitted successfully',
                'submission_id' => $submission->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to submit form'], 500);
        }
    }

    public function index($formId)
    {
        $form = Form::findOrFail($formId);

        $submissions = FormSubmission::where('form_id', $formId)
            ->with(['answers.field'])
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->map(function ($submission) {
                $submissionData = [
                    'id' => $submission->id,
                    'submitted_at' => $submission->submitted_at,
                    'ip_address' => $submission->ip_address,
                    'answers' => []
                ];

                foreach ($submission->answers as $answer) {
                    $submissionData['answers'][] = [
                        'field_label' => $answer->field->label,
                        'field_type' => $answer->field->type,
                        'value' => $answer->answer_value
                    ];
                }

                return $submissionData;
            });

        return response()->json([
            'form' => $form,
            'submissions' => $submissions
        ]);
    }

    public function show($formId, $submissionId)
    {
        $submission = FormSubmission::where('form_id', $formId)
            ->where('id', $submissionId)
            ->with(['answers.field', 'form'])
            ->firstOrFail();

        return response()->json($submission);
    }
}
