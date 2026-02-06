@extends('admin.layouts.app')

@section('title', 'Edit Form')

@push('styles')
<style>
    /* Clean custom checkboxes */
    .custom-checkbox {
        appearance: none;
        background-color: #fff;
        margin: 0;
        font: inherit;
        color: currentColor;
        width: 1.15em;
        height: 1.15em;
        border: 1px solid #d1d5db;
        border-radius: 0.25em;
        display: grid;
        place-content: center;
    }
    .custom-checkbox::before {
        content: "";
        width: 0.65em;
        height: 0.65em;
        transform: scale(0);
        transition: 120ms transform ease-in-out;
        box-shadow: inset 1em 1em white;
        transform-origin: center;
        clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
    }
    .custom-checkbox:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    .custom-checkbox:checked::before {
        transform: scale(1);
    }
</style>
@endpush

@section('content')
<div class="sm:flex sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Form</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $form->title }}</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="{{ route('admin.forms.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            &larr; Back to Forms
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content (2 cols) -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Form Details -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Form Settings</h3>
                <form action="{{ route('admin.forms.toggle', $form) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold shadow-sm ring-1 ring-inset transition-colors {{ $form->is_active ? 'bg-yellow-50 text-yellow-700 ring-yellow-600/20 hover:bg-yellow-100' : 'bg-green-50 text-green-700 ring-green-600/20 hover:bg-green-100' }}">
                        {{ $form->is_active ? 'Deactivate Form' : 'Activate Form' }}
                    </button>
                </form>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.forms.update', $form) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Form Title</label>
                        <div class="mt-2">
                            <input type="text" id="title" name="title" value="{{ old('title', $form->title) }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" required>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                        <div class="mt-2">
                            <textarea id="description" name="description" rows="2" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">{{ old('description', $form->description) }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                         <div>
                            <label for="certificate_image" class="block text-sm font-medium leading-6 text-gray-900">Certificate Background</label>
                            <div class="mt-2 text-sm text-gray-500">
                                 <input type="file" id="certificate_image" name="certificate_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" accept="image/*">
                            </div>
                            @if($form->certificate_image)
                                <p class="mt-2 text-xs text-green-600 font-medium">✓ Image uploaded</p>
                            @endif
                        </div>

                        <div>
                            <label for="orientation" class="block text-sm font-medium leading-6 text-gray-900">Orientation</label>
                            <div class="mt-2">
                                <select id="orientation" name="orientation" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                    <option value="horizontal" {{ $form->orientation == 'horizontal' ? 'selected' : '' }}>Horizontal</option>
                                    <option value="vertical" {{ $form->orientation == 'vertical' ? 'selected' : '' }}>Vertical</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Questions List -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Questions ({{ $form->questions->count() }})</h3>
            </div>
            <div class="p-6 space-y-6">
                @if($form->questions->count())
                    @foreach($form->questions as $index => $question)
                        <div class="group relative bg-gray-50 rounded-lg border border-gray-200">
                            <!-- Question Header -->
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-white rounded-t-lg">
                                <div class="flex items-center gap-3">
                                    <span class="flex items-center justify-center h-6 w-6 rounded-full bg-primary-100 text-primary-600 text-xs font-bold">Q{{ $index + 1 }}</span>
                                    <span class="text-xs font-medium text-gray-400">MCQ</span>
                                </div>
                                <form action="{{ route('admin.questions.destroy', [$form, $question]) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Delete Question">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Question Body -->
                            <div class="p-4">
                                <p class="text-sm font-medium text-gray-900 mb-4">{{ $question->question_text }}</p>
                                <div class="space-y-2">
                                    @foreach($question->answers as $answer)
                                        <div class="flex items-center gap-3 px-3 py-2 rounded-md border {{ $answer->is_correct ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}">
                                            <div class="flex-shrink-0">
                                                @if($answer->is_correct)
                                                    <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <div class="h-5 w-5 rounded-full border-2 border-gray-300"></div>
                                                @endif
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $answer->answer_text }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-10 border-2 border-dashed border-gray-200 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No questions yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Start building your form below.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Question Form -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500 rounded-l-xl"></div>
            <div class="px-6 py-6 sm:p-8">
                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                    </svg>
                    Add New Question
                </h3>
                
                <form action="{{ route('admin.questions.store', $form) }}" method="POST" id="addQuestionForm" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium leading-6 text-gray-900">Question Text <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <textarea name="question_text" rows="2" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Enter your question here..." required></textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium leading-6 text-gray-900 mb-3">Answers (Check the correct ones) <span class="text-red-500">*</span></label>
                        <div class="space-y-3" id="answersContainer">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="correct_answers[]" value="0" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer">
                                <input type="text" name="answers[0][text]" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Answer option 1" required>
                                <button type="button" class="text-gray-400 hover:text-red-500 invisible p-1">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="correct_answers[]" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer">
                                <input type="text" name="answers[1][text]" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Answer option 2" required>
                                <button type="button" class="text-gray-400 hover:text-red-500 invisible p-1">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addAnswer()" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            <svg class="mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                            </svg>
                            Add another answer option
                        </button>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                            Add Question to Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar (Settings & Info) -->
    <div class="space-y-8">
        
        <!-- Share Box -->
        @if($form->is_active)
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50/50">
                    <h3 class="text-sm font-semibold leading-6 text-indigo-900">Share Form</h3>
                </div>
                <div class="p-6">
                    <label class="block text-xs font-medium text-gray-500 mb-2">Public Link</label>
                    <div class="flex shadow-sm rounded-md">
                        <input type="text" value="{{ $form->share_url }}" readonly id="shareLink" class="block w-full min-w-0 rounded-none rounded-l-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-xs sm:leading-6 bg-gray-50">
                        <button type="button" onclick="copyShareLink()" class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-xs font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            Copy
                        </button>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">Share this link with your students to collect responses.</p>
                </div>
            </div>
        @endif

        <!-- Stats -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-semibold leading-6 text-gray-900">Overview</h3>
            </div>
            <div class="p-6">
                 <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-xs font-medium text-gray-500">Questions</dt>
                        <dd class="mt-1 text-2xl font-bold tracking-tight text-gray-900">{{ $form->questions->count() }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-xs font-medium text-gray-500">Submissions</dt>
                        <dd class="mt-1 text-2xl font-bold tracking-tight text-gray-900">{{ $form->submissions->count() }}</dd>
                    </div>
                    <div class="sm:col-span-2 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.forms.submissions', $form) }}" class="block w-full text-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">View Submissions</a>
                    </div>
                 </dl>
            </div>
        </div>

        <!-- Certificate Preview -->
        @if($form->certificate_image)
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-sm font-semibold leading-6 text-gray-900">Certificate Background</h3>
                </div>
                <div class="p-4 bg-gray-100 text-center">
                    <img src="{{ Storage::url($form->certificate_image) }}" alt="Certificate" class="max-h-48 rounded-lg shadow-sm mx-auto border border-gray-200">
                    <p class="mt-2 text-xs text-gray-500">{{ ucfirst($form->orientation) }} orientation</p>
                </div>
            </div>
        @endif

        <!-- Danger Zone -->
        <div class="bg-red-50 overflow-hidden shadow-sm rounded-xl border border-red-100">
             <div class="p-6 text-center">
                <form action="{{ route('admin.forms.destroy', $form) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this form? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">
                        Delete this form
                    </button>
                </form>
             </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
let answerCount = 2;

function addAnswer() {
    const container = document.getElementById('answersContainer');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-3';
    row.innerHTML = `
        <input type="checkbox" name="correct_answers[]" value="${answerCount}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer">
        <input type="text" name="answers[${answerCount}][text]" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Answer option ${answerCount + 1}" required>
        <button type="button" class="text-gray-400 hover:text-red-500 p-1" onclick="removeAnswer(this)">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </button>
    `;
    container.appendChild(row);
    answerCount++;
}

function removeAnswer(btn) {
    btn.parentElement.remove();
}

function copyShareLink() {
    const input = document.getElementById('shareLink');
    input.select();
    navigator.clipboard.writeText(input.value);
    alert('Link copied!');
}
</script>
@endpush
@endsection
