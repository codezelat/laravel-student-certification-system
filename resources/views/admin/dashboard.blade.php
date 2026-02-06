@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Overview of your certification system</p>
    </div>
    <a href="{{ route('admin.forms.create') }}" class="btn btn-primary">
        <span>+</span> Create New Form
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple">📋</div>
        <div>
            <div class="stat-value">{{ $stats['total_forms'] }}</div>
            <div class="stat-label">Total Forms</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✓</div>
        <div>
            <div class="stat-value">{{ $stats['active_forms'] }}</div>
            <div class="stat-label">Active Forms</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">📝</div>
        <div>
            <div class="stat-value">{{ $stats['total_submissions'] }}</div>
            <div class="stat-label">Total Submissions</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Forms</h3>
            <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="card-body" style="padding: 0;">
            @if($recentForms->count())
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentForms as $form)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.forms.edit', $form) }}" style="color: var(--text-primary); text-decoration: none;">
                                        {{ $form->title }}
                                    </a>
                                </td>
                                <td>
                                    @if($form->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="icon">📋</div>
                    <p>No forms created yet</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Submissions</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            @if($recentSubmissions->count())
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Form</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSubmissions as $submission)
                            <tr>
                                <td>{{ $submission->full_name }}</td>
                                <td>{{ $submission->form->title ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $submission->score }}/{{ $submission->total_questions }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="icon">📝</div>
                    <p>No submissions yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
