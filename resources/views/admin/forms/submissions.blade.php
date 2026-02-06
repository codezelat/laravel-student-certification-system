@extends('admin.layouts.app')

@section('title', 'Submissions')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Submissions</h1>
        <p class="page-subtitle">{{ $form->title }}</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('admin.forms.export', $form) }}" class="btn btn-primary" style="background: #107c41;">
            <span>📊</span> Export Excel
        </a>
        <a href="{{ route('admin.forms.edit', $form) }}" class="btn btn-secondary">
            ← Back to Form
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        @if($submissions->count())
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Score</th>
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $submission)
                            <tr>
                                <td><strong>{{ $submission->full_name }}</strong></td>
                                <td>{{ $submission->email }}</td>
                                <td>{{ $submission->mobile }}</td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $submission->score }}/{{ $submission->total_questions }}
                                        ({{ $submission->score_percentage }}%)
                                    </span>
                                </td>
                                <td>{{ $submission->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border);">
                {{ $submissions->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="icon">📝</div>
                <h3>No submissions yet</h3>
                <p>Share your form to start receiving submissions</p>
            </div>
        @endif
    </div>
</div>
@endsection
