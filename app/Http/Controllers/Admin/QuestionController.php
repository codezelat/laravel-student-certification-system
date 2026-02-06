<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Store a new question.
     */
    public function store(Request $request, Form $form)
    {
        $request->validate([
            'question_text' => 'required|string',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string',
            'correct_answers' => 'required|array|min:1',
        ]);

        $maxOrder = $form->questions()->max('order') ?? 0;

        $question = $form->questions()->create([
            'question_text' => $request->question_text,
            'order' => $maxOrder + 1,
        ]);

        $correctAnswers = $request->correct_answers ?? [];

        foreach ($request->answers as $index => $answerData) {
            $question->answers()->create([
                'answer_text' => $answerData['text'],
                'is_correct' => in_array($index, $correctAnswers),
                'order' => $index,
            ]);
        }

        return back()->with('success', 'Question added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Form $form, Question $question)
    {
        return view('admin.questions.edit', compact('form', 'question'));
    }

    /**
     * Update a question.
     */
    public function update(Request $request, Form $form, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string',
            'answers.*.id' => 'nullable|exists:answers,id',
            'correct_answers' => 'required|array|min:1',
        ]);

        $question->update([
            'question_text' => $request->question_text,
        ]);

        $incomingIds = [];
        $correctAnswersIndices = $request->correct_answers ?? [];

        foreach ($request->answers as $index => $answerData) {
            $isCorrect = in_array($index, $correctAnswersIndices);
            
            if (isset($answerData['id'])) {
                // Update existing
                $answer = Answer::find($answerData['id']);
                if ($answer) {
                    $answer->update([
                        'answer_text' => $answerData['text'],
                        'is_correct' => $isCorrect,
                        'order' => $index,
                    ]);
                    $incomingIds[] = $answer->id;
                }
            } else {
                // Create new
                $newAnswer = $question->answers()->create([
                    'answer_text' => $answerData['text'],
                    'is_correct' => $isCorrect,
                    'order' => $index,
                ]);
                $incomingIds[] = $newAnswer->id;
            }
        }

        // Handle deletions safely
        $existingIds = $question->answers()->pluck('id')->toArray();
        $toDelete = array_diff($existingIds, $incomingIds);

        foreach ($toDelete as $id) {
            $answer = Answer::find($id);
            // Check if used in submissions
            if (\App\Models\SubmissionAnswer::where('answer_id', $id)->exists()) {
                // If used, we arguably shouldn't delete it. 
                // But since we don't have soft deletes, 
                // we will keep it but maybe we should ideally mark it inactive.
                // For this MVP/Lite scope, let's return an error to be safe.
                return back()->with('error', "Cannot delete option '{$answer->answer_text}' because it has explicitly been chosen by students in past submissions.");
            }
            $answer->delete();
        }

        return back()->with('success', 'Question updated successfully!');
    }

    /**
     * Delete a question.
     */
    public function destroy(Form $form, Question $question)
    {
        $question->delete();
        
        // Reorder remaining questions
        $form->questions()->orderBy('order')->each(function ($q, $index) {
            $q->update(['order' => $index + 1]);
        });

        return back()->with('success', 'Question deleted successfully!');
    }

    /**
     * Reorder questions via AJAX.
     */
    public function reorder(Request $request, Form $form)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:questions,id',
        ]);

        foreach ($request->order as $index => $questionId) {
            Question::where('id', $questionId)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
