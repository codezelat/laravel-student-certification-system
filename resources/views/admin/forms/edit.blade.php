@extends('admin.layouts.app')

@section('title', 'Edit Form')

@push('styles')
<style>
    .question-card {
        background: var(--bg-input);
        border-radius: var(--radius-sm);
        margin-bottom: 1rem;
        border: 1px solid var(--border);
    }
    .question-header {
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border);
    }
    .question-number {
        font-weight: 600;
        color: var(--primary-light);
    }
    .question-body {
        padding: 1.25rem;
    }
    .question-text {
        font-weight: 500;
        margin-bottom: 1rem;
    }
    .answer-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .answer-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 0.875rem;
        background: var(--bg-card);
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
    }
    .answer-item.correct {
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .answer-marker {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
    }
    .answer-item.correct .answer-marker {
        background: var(--secondary);
        border-color: var(--secondary);
        color: white;
    }
    .add-question-form {
        background: var(--bg-input);
        border-radius: var(--radius);
        padding: 1.5rem;
        border: 2px dashed var(--border);
    }
    .answers-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .answer-input-row {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .answer-input-row input[type="text"] {
        flex: 1;
    }
    .answer-input-row input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    .remove-answer-btn {
        background: var(--danger);
        color: white;
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
    }
    .add-answer-btn {
        background: transparent;
        border: 1px dashed var(--border);
        color: var(--text-secondary);
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
        cursor: pointer;
        font-size: 0.875rem;
    }
    .add-answer-btn:hover {
        border-color: var(--primary);
        color: var(--primary-light);
    }
    .share-link-box {
        background: var(--bg-input);
        border-radius: var(--radius-sm);
        padding: 1rem;
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .share-link-box input {
        flex: 1;
        background: var(--bg-card);
    }
    .certificate-preview {
        background: var(--bg-input);
        border-radius: var(--radius-sm);
        padding: 1rem;
        text-align: center;
    }
    .certificate-preview img {
        max-width: 100%;
        max-height: 200px;
        border-radius: var(--radius-sm);
    }
    .two-columns {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 1024px) {
        .two-columns {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit Form</h1>
        <p class="page-subtitle">{{ $form->title }}</p>
    </div>
    <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary">
        ← Back to Forms
    </a>
</div>

<div class="two-columns">
    <div>
        {{-- Form Details --}}
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title">Form Details</h3>
                <div class="actions">
                    <form action="{{ route('admin.forms.toggle', $form) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $form->is_active ? 'btn-warning' : 'btn-success' }}" style="{{ $form->is_active ? 'background: var(--warning);' : '' }}">
                            {{ $form->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.forms.update', $form) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="title" class="form-label">Form Title *</label>
                        <input type="text" id="title" name="title" class="form-control" 
                               value="{{ old('title', $form->title) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="2">{{ old('description', $form->description) }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="certificate_image" class="form-label">Certificate Background</label>
                            <input type="file" id="certificate_image" name="certificate_image" class="form-control" accept="image/*">
                            @if($form->certificate_image)
                                <p class="form-hint" style="color: var(--secondary);">✓ Image uploaded</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="orientation" class="form-label">Orientation</label>
                            <select id="orientation" name="orientation" class="form-control">
                                <option value="horizontal" {{ $form->orientation == 'horizontal' ? 'selected' : '' }}>Horizontal</option>
                                <option value="vertical" {{ $form->orientation == 'vertical' ? 'selected' : '' }}>Vertical</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </form>
            </div>
        </div>

        {{-- Questions List --}}
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title">Questions ({{ $form->questions->count() }})</h3>
            </div>
            <div class="card-body">
                @if($form->questions->count())
                    @foreach($form->questions as $index => $question)
                        <div class="question-card">
                            <div class="question-header">
                                <span class="question-number">Q{{ $index + 1 }}</span>
                                <div class="actions">
                                    <form action="{{ route('admin.questions.destroy', [$form, $question]) }}" method="POST" 
                                          onsubmit="return confirm('Delete this question?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <div class="question-body">
                                <div class="question-text">{{ $question->question_text }}</div>
                                <div class="answer-list">
                                    @foreach($question->answers as $answer)
                                        <div class="answer-item {{ $answer->is_correct ? 'correct' : '' }}">
                                            <span class="answer-marker">{{ $answer->is_correct ? '✓' : '' }}</span>
                                            <span>{{ $answer->answer_text }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="icon">❓</div>
                        <p>No questions added yet</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Add Question Form --}}
        <div class="add-question-form">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Add New Question</h3>
            <form action="{{ route('admin.questions.store', $form) }}" method="POST" id="addQuestionForm">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Question Text *</label>
                    <textarea name="question_text" class="form-control" placeholder="Enter your question..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Answers (check correct ones) *</label>
                    <div class="answers-container" id="answersContainer">
                        <div class="answer-input-row">
                            <input type="checkbox" name="correct_answers[]" value="0">
                            <input type="text" name="answers[0][text]" class="form-control" placeholder="Answer option 1" required>
                            <button type="button" class="remove-answer-btn" onclick="removeAnswer(this)" style="visibility: hidden;">×</button>
                        </div>
                        <div class="answer-input-row">
                            <input type="checkbox" name="correct_answers[]" value="1">
                            <input type="text" name="answers[1][text]" class="form-control" placeholder="Answer option 2" required>
                            <button type="button" class="remove-answer-btn" onclick="removeAnswer(this)" style="visibility: hidden;">×</button>
                        </div>
                    </div>
                    <button type="button" class="add-answer-btn" onclick="addAnswer()">+ Add Another Answer</button>
                </div>

                <button type="submit" class="btn btn-primary">Add Question</button>
            </form>
        </div>
    </div>

    {{-- Sidebar --}}
    <div>
        @if($form->is_active)
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Share Link</h3>
                </div>
                <div class="card-body">
                    <div class="share-link-box">
                        <input type="text" class="form-control" value="{{ $form->share_url }}" readonly id="shareLink">
                        <button type="button" class="btn btn-primary btn-sm" onclick="copyShareLink()">Copy</button>
                    </div>
                    <p class="form-hint" style="margin-top: 0.75rem;">Share this link with participants</p>
                </div>
            </div>
        @endif

        @if($form->certificate_image)
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h3 class="card-title">Certificate Preview</h3>
                </div>
                <div class="card-body">
                    <div class="certificate-preview">
                        <img src="{{ Storage::url($form->certificate_image) }}" alt="Certificate">
                        <p class="form-hint" style="margin-top: 0.5rem;">{{ ucfirst($form->orientation) }} orientation</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Stats</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: var(--text-secondary);">Questions</span>
                    <strong>{{ $form->questions->count() }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: var(--text-secondary);">Submissions</span>
                    <strong>{{ $form->submissions->count() }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Status</span>
                    @if($form->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-warning">Inactive</span>
                    @endif
                </div>

                @if($form->submissions->count())
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                        <a href="{{ route('admin.forms.submissions', $form) }}" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                            View Submissions
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div style="margin-top: 1.5rem;">
            <form action="{{ route('admin.forms.destroy', $form) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this form? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center;">
                    Delete Form
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let answerCount = 2;

function addAnswer() {
    const container = document.getElementById('answersContainer');
    const row = document.createElement('div');
    row.className = 'answer-input-row';
    row.innerHTML = `
        <input type="checkbox" name="correct_answers[]" value="${answerCount}">
        <input type="text" name="answers[${answerCount}][text]" class="form-control" placeholder="Answer option ${answerCount + 1}" required>
        <button type="button" class="remove-answer-btn" onclick="removeAnswer(this)">×</button>
    `;
    container.appendChild(row);
    answerCount++;
    updateRemoveButtons();
}

function removeAnswer(btn) {
    btn.parentElement.remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.answer-input-row');
    rows.forEach((row, index) => {
        const btn = row.querySelector('.remove-answer-btn');
        btn.style.visibility = rows.length > 2 ? 'visible' : 'hidden';
    });
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
