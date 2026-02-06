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
        $forms = Form::withCount(['questions', 'submissions'])->latest()->paginate(15);
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

    /**
     * Show the certificate designer.
     */
    public function design(Form $form)
    {
        return view('admin.forms.design', compact('form'));
    }

    /**
     * Save certificate design settings.
     */
    public function saveDesign(Request $request, Form $form)
    {
        $validated = $request->validate([
            'x' => 'nullable|integer',
            'y' => 'nullable|integer',
            'font_size' => 'required|integer|min:10|max:200',
            'font_color' => ['required', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'],
            'font_weight' => 'nullable|in:normal,bold',
            'font_style' => 'nullable|in:normal,italic',
            'background_fit' => 'nullable|in:fill,cover,contain',
            'max_width' => 'nullable|integer|min:50|max:2000',
            'text_align' => 'nullable|in:left,center,right',
            'vertical_align' => 'nullable|in:top,middle,bottom',
            'max_lines' => 'nullable|integer|min:1|max:5',
        ]);

        // Merge defaults if null, but actually we want to save exactly what is sent to maintain position
        $form->certificate_settings = [
            'x' => $request->x, // Allow null
            'y' => $request->y, // Allow null
            'font_size' => $request->font_size,
            'font_color' => $request->font_color,
            'font_weight' => $request->font_weight ?? 'bold', // Default to bold as before
            'font_style' => $request->font_style ?? 'normal',
            'background_fit' => $request->background_fit ?? 'fill',
            'max_width' => $request->max_width ?? 800,
            'text_align' => $request->text_align ?? 'center',
            'vertical_align' => $request->vertical_align ?? 'top', // Added
            'max_lines' => $request->max_lines ?? 1,
        ];
        
        $form->save();

        return back()->with('success', 'Certificate design saved successfully!');
    }

    /**
     * Generate a preview of the certificate.
     */
    public function preview(Request $request, Form $form, \App\Services\CertificateService $certificateService)
    {
        // Mock a submission for preview
        $submission = new \App\Models\Submission();
        $submission->id = 0;
        $submission->full_name = $request->input('name', "Sample Participant Name");
        
        $path = $certificateService->generate($form, $submission, 1); // scale=1 for fast preview
        
        return response()->file($path)->deleteFileAfterSend();
    }
}
