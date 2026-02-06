@extends('admin.layouts.app')

@section('title', 'Edit Question')

@section('content')
<div class="sm:flex sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Question</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $form->title }}</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="{{ route('admin.forms.edit', $form) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            &larr; Back to Form
        </a>
    </div>
</div>

<div class="max-w-3xl mx-auto">
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Question Details</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.questions.update', [$form, $question]) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium leading-6 text-gray-900">Question Text <span class="text-red-500">*</span></label>
                    <div class="mt-2">
                        <textarea name="question_text" rows="2" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>{{ old('question_text', $question->question_text) }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium leading-6 text-gray-900 mb-3">Answers (Check the correct ones) <span class="text-red-500">*</span></label>
                    <div class="space-y-3" id="answersContainer">
                        @foreach($question->answers as $index => $answer)
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="answers[{{ $index }}][id]" value="{{ $answer->id }}">
                                <input type="checkbox" name="correct_answers[]" value="{{ $index }}" {{ $answer->is_correct ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer">
                                <input type="text" name="answers[{{ $index }}][text]" value="{{ $answer->answer_text }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Answer option" required>
                                
                                {{-- Don't allow deleting the first two answers to enforce minimum 2 constraint simply --}}
                                @if($index >= 2)
                                    <button type="button" class="text-gray-400 hover:text-red-500 p-1" onclick="removeAnswer(this)">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </button>
                                @else
                                    <button type="button" class="text-gray-400 hover:text-red-500 invisible p-1">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addAnswer()" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        <svg class="mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                        </svg>
                        Add another answer option
                    </button>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                        Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let answerCount = {{ $question->answers->count() }};

// We need to keep tracks of indices to ensure unique names if we add/remove
let currentMaxIndex = {{ $question->answers->count() - 1 }};

function addAnswer() {
    currentMaxIndex++;
    const container = document.getElementById('answersContainer');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-3';
    row.innerHTML = `
        <input type="checkbox" name="correct_answers[]" value="${currentMaxIndex}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer">
        <input type="text" name="answers[${currentMaxIndex}][text]" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Answer option" required>
        <button type="button" class="text-gray-400 hover:text-red-500 p-1" onclick="removeAnswer(this)">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </button>
    `;
    container.appendChild(row);
}

function removeAnswer(btn) {
    btn.parentElement.remove();
}
</script>
@endpush
@endsection
