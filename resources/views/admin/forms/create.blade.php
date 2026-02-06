@extends('admin.layouts.app')

@section('title', 'Create Form')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create New Form</h1>
        <p class="page-subtitle">Set up your questionnaire and certificate</p>
    </div>
    <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary">
        ← Back to Forms
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.forms.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="title" class="form-label">Form Title *</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       class="form-control" 
                       placeholder="e.g., JavaScript Fundamentals Quiz"
                       value="{{ old('title') }}"
                       required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" 
                          name="description" 
                          class="form-control" 
                          placeholder="Brief description of the questionnaire..."
                          rows="3">{{ old('description') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="certificate_image" class="form-label">Certificate Background Image</label>
                    <input type="file" 
                           id="certificate_image" 
                           name="certificate_image" 
                           class="form-control"
                           accept="image/jpeg,image/png,image/jpg">
                    <p class="form-hint">Upload A4 sized image (JPG, PNG). Max 5MB.</p>
                </div>

                <div class="form-group">
                    <label for="orientation" class="form-label">Certificate Orientation *</label>
                    <select id="orientation" name="orientation" class="form-control" required>
                        <option value="horizontal" {{ old('orientation') == 'horizontal' ? 'selected' : '' }}>Horizontal (Landscape)</option>
                        <option value="vertical" {{ old('orientation') == 'vertical' ? 'selected' : '' }}>Vertical (Portrait)</option>
                    </select>
                    <p class="form-hint">Choose based on your certificate design</p>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; padding-top: 1rem; border-top: 1px solid var(--border); margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">
                    Create Form & Add Questions
                </button>
                <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
