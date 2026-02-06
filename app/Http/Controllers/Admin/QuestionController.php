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
     * Update a question.
     */
    public function update(Request $request, Form $form, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string',
            'correct_answers' => 'required|array|min:1',
        ]);

        $question->update([
            'question_text' => $request->question_text,
        ]);

        // Delete old answers and create new ones
        $question->answers()->delete();

        $correctAnswers = $request->correct_answers ?? [];

        foreach ($request->answers as $index => $answerData) {
            $question->answers()->create([
                'answer_text' => $answerData['text'],
                'is_correct' => in_array($index, $correctAnswers),
                'order' => $index,
            ]);
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
