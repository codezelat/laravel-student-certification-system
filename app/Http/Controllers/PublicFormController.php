<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Submission;
use App\Models\SubmissionAnswer;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class PublicFormController extends Controller
{
    /**
     * Show form registration page.
     */
    public function show($slug)
    {
        $form = Form::where('slug', $slug)->firstOrFail();

        if (!$form->is_active) {
            return view('public.inactive', compact('form'));
        }

        $form->load('questions');

        if ($form->questions->isEmpty()) {
            abort(404, 'This form has no questions.');
        }

        return view('public.register', compact('form'));
    }

    /**
     * Register participant and start quiz.
     */
    public function register(Request $request, $slug)
    {
        $form = Form::where('slug', $slug)->firstOrFail();
        
        if (!$form->is_active) {
            return redirect()->route('public.show', $slug);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => ['required', 'string', 'regex:/^(\+94|0)?\s?7[0-9]{1}\s?[0-9]{3}\s?[0-9]{4}$/'],
        ], [
            'mobile.regex' => 'Please enter a valid Sri Lankan mobile number (e.g., 077 123 4567 or +94 77 123 4567).',
        ]);

        $submission = Submission::create([
            'form_id' => $form->id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'total_questions' => $form->questions()->count(),
        ]);

        session(['submission_id' => $submission->id, 'current_question' => 0]);

        return redirect()->route('public.question', ['slug' => $slug, 'index' => 0]);
    }

    /**
     * Show a specific question.
     */
    public function question($slug, $index)
    {
        $form = Form::where('slug', $slug)
            ->with(['questions.answers'])
            ->firstOrFail();

        if (!$form->is_active) {
            return redirect()->route('public.show', $slug);
        }

        $submissionId = session('submission_id');
        if (!$submissionId) {
            return redirect()->route('public.show', $slug);
        }

        $submission = Submission::findOrFail($submissionId);
        $questions = $form->questions;
        
        if ($index >= $questions->count()) {
            return redirect()->route('public.result', $slug);
        }

        $question = $questions[$index];
        $totalQuestions = $questions->count();
        $progress = round((($index) / $totalQuestions) * 100);

        return view('public.question', compact('form', 'question', 'index', 'totalQuestions', 'progress'));
    }

    /**
     * Submit answer and move to next question.
     */
    public function submitAnswer(Request $request, $slug)
    {
        $form = Form::where('slug', $slug)->firstOrFail();

        if (!$form->is_active) {
            return redirect()->route('public.show', $slug);
        }

        $submissionId = session('submission_id');
        if (!$submissionId) {
            return redirect()->route('public.show', $slug);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_id' => 'required|exists:answers,id',
            'current_index' => 'required|integer',
        ]);

        $submission = Submission::findOrFail($submissionId);
        $question = $form->questions()->findOrFail($request->question_id);
        $answer = $question->answers()->findOrFail($request->answer_id);

        // Check if already answered
        $existingAnswer = SubmissionAnswer::where('submission_id', $submissionId)
            ->where('question_id', $request->question_id)
            ->first();

        if (!$existingAnswer) {
            SubmissionAnswer::create([
                'submission_id' => $submissionId,
                'question_id' => $request->question_id,
                'answer_id' => $request->answer_id,
                'is_correct' => $answer->is_correct,
            ]);

            if ($answer->is_correct) {
                $submission->increment('score');
            }
        }

        $nextIndex = $request->current_index + 1;
        $totalQuestions = $form->questions()->count();

        if ($nextIndex >= $totalQuestions) {
            return redirect()->route('public.result', $slug);
        }

        return redirect()->route('public.question', ['slug' => $slug, 'index' => $nextIndex]);
    }

    /**
     * Show result page with certificate.
     */
    public function result($slug)
    {
        $form = Form::where('slug', $slug)->firstOrFail();

        $submissionId = session('submission_id');
        if (!$submissionId) {
            return redirect()->route('public.show', $slug);
        }

        $submission = Submission::findOrFail($submissionId);

        return view('public.result', compact('form', 'submission'));
    }

    /**
     * Download certificate.
     */
    public function downloadCertificate($slug, \App\Services\CertificateService $certificateService)
    {
        $form = Form::where('slug', $slug)->firstOrFail();
        
        $submissionId = session('submission_id');
        if (!$submissionId) {
            return redirect()->route('public.show', $slug);
        }
        
        $submission = Submission::findOrFail($submissionId);
        
        $path = $certificateService->generate($form, $submission);
        
        return response()->download($path, 'Certificate.jpg');
    }

    /**
     * View certificate image inline (for result page preview).
     */
    public function viewCertificate($slug, \App\Services\CertificateService $certificateService)
    {
        $form = Form::where('slug', $slug)->firstOrFail();
        
        $submissionId = session('submission_id');
        if (!$submissionId) {
            abort(403);
        }
        
        $submission = Submission::findOrFail($submissionId);
        
        $path = $certificateService->generate($form, $submission, 1); // scale=1 for fast preview
        
        return response()->file($path);
    }
}
