<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FormController extends Controller
{
    /**
     * Display a listing of forms.
     */
    public function index()
    {
        $forms = Form::withCount(['questions', 'submissions'])->latest()->paginate(10);
        return view('admin.forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new form.
     */
    public function create()
    {
        return view('admin.forms.create');
    }

    /**
     * Store a newly created form.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'certificate_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'orientation' => 'required|in:horizontal,vertical',
        ]);

        $form = new Form();
        $form->title = $request->title;
        $form->slug = Str::slug($request->title) . '-' . Str::random(6);
        $form->description = $request->description;
        $form->orientation = $request->orientation;
        $form->is_active = false;

        if ($request->hasFile('certificate_image')) {
            $path = $request->file('certificate_image')->store('certificates', 'public');
            $form->certificate_image = $path;
        }

        $form->save();

        return redirect()->route('admin.forms.edit', $form)->with('success', 'Form created successfully! Now add some questions.');
    }

    /**
     * Show the form for editing.
     */
    public function edit(Form $form)
    {
        $form->load(['questions.answers']);
        return view('admin.forms.edit', compact('form'));
    }

    /**
     * Update the form.
     */
    public function update(Request $request, Form $form)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'certificate_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'orientation' => 'required|in:horizontal,vertical',
        ]);

        $form->title = $request->title;
        $form->description = $request->description;
        $form->orientation = $request->orientation;

        if ($request->hasFile('certificate_image')) {
            // Delete old image if exists
            if ($form->certificate_image) {
                Storage::disk('public')->delete($form->certificate_image);
            }
            $path = $request->file('certificate_image')->store('certificates', 'public');
            $form->certificate_image = $path;
        }

        $form->save();

        return redirect()->route('admin.forms.edit', $form)->with('success', 'Form updated successfully!');
    }

    /**
     * Toggle form status.
     */
    public function toggleStatus(Form $form)
    {
        $form->is_active = !$form->is_active;
        $form->save();

        $status = $form->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Form {$status} successfully!");
    }

    /**
     * Show form submissions.
     */
    public function submissions(Form $form)
    {
        $submissions = $form->submissions()->latest()->paginate(20);
        return view('admin.forms.submissions', compact('form', 'submissions'));
    }

    /**
     * Delete a form.
     */
    public function destroy(Form $form)
    {
        if ($form->certificate_image) {
            Storage::disk('public')->delete($form->certificate_image);
        }
        
        $form->delete();
        
        return redirect()->route('admin.forms.index')->with('success', 'Form deleted successfully!');
    }

    /**
     * Export submissions to CSV (Excel compatible).
     */
    public function export(Form $form)
    {
        $fileName = 'submissions-' . $form->slug . '-' . date('Y-m-d') . '.csv';
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Full Name', 'Email', 'Mobile', 'Score', 'Total Questions', 'Percentage', 'Submitted At'];

        $callback = function() use ($form, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $form->submissions()->chunk(100, function($submissions) use ($file) {
                foreach ($submissions as $submission) {
                    fputcsv($file, [
                        $submission->id,
                        $submission->full_name,
                        $submission->email,
                        $submission->mobile,
                        $submission->score,
                        $submission->total_questions,
                        $submission->score_percentage . '%',
                        $submission->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
