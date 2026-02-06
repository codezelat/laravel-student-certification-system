@extends('admin.layouts.app')

@section('title', 'Forms')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Forms</h1>
        <p class="page-subtitle">Manage your questionnaire forms</p>
    </div>
    <a href="{{ route('admin.forms.create') }}" class="btn btn-primary">
        <span>+</span> Create New Form
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        @if($forms->count())
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Questions</th>
                            <th>Submissions</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($forms as $form)
                            <tr>
                                <td>
                                    <strong>{{ $form->title }}</strong>
                                    @if($form->description)
                                        <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.25rem;">
                                            {{ Str::limit($form->description, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $form->questions_count }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $form->submissions_count }}</span>
                                </td>
                                <td>
                                    @if($form->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $form->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('admin.forms.edit', $form) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        
                                        <form action="{{ route('admin.forms.toggle', $form) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $form->is_active ? 'btn-warning' : 'btn-success' }}" style="{{ $form->is_active ? 'background: var(--warning);' : '' }}">
                                                {{ $form->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        @if($form->is_active)
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm" 
                                                    onclick="copyLink('{{ $form->share_url }}')"
                                                    title="Copy share link">
                                                Share
                                            </button>
                                        @endif

                                        <a href="{{ route('admin.forms.submissions', $form) }}" class="btn btn-secondary btn-sm">
                                            Submissions
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border);">
                {{ $forms->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="icon">📋</div>
                <h3>No forms yet</h3>
                <p>Create your first questionnaire form to get started</p>
                <a href="{{ route('admin.forms.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Create Form
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function copyLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        alert('Link copied to clipboard!');
    });
}
</script>
@endpush
@endsection
