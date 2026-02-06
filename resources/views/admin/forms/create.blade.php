@extends('admin.layouts.app')

@section('title', 'Create Form')

@section('content')
<div class="sm:flex sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Create New Form</h1>
        <p class="mt-1 text-sm text-gray-500">Set up your questionnaire and certificate</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="{{ route('admin.forms.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            &larr; Back to Forms
        </a>
    </div>
</div>

<div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 max-w-3xl">
    <div class="px-6 py-6 sm:p-8">
        <form action="{{ route('admin.forms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div>
                <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Form Title <span class="text-red-500">*</span></label>
                <div class="mt-2">
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" 
                           placeholder="e.g., JavaScript Fundamentals Quiz"
                           value="{{ old('title') }}"
                           required>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                <div class="mt-2">
                    <textarea id="description" 
                              name="description" 
                              rows="3" 
                              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" 
                              placeholder="Brief description of the questionnaire...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                <div>
                    <label for="certificate_image" class="block text-sm font-medium leading-6 text-gray-900">Certificate Background</label>
                    <div class="mt-2 text-sm text-gray-500">
                         <input type="file" 
                               id="certificate_image" 
                               name="certificate_image" 
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                               accept="image/jpeg,image/png,image/jpg">
                    </div>
                     <p class="mt-2 text-xs text-gray-500">A4 sized image (JPG, PNG). Max 5MB.</p>
                </div>

                <div>
                    <label for="orientation" class="block text-sm font-medium leading-6 text-gray-900">Orientation <span class="text-red-500">*</span></label>
                    <div class="mt-2">
                        <select id="orientation" name="orientation" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6" required>
                            <option value="horizontal" {{ old('orientation') == 'horizontal' ? 'selected' : '' }}>Horizontal (Landscape)</option>
                            <option value="vertical" {{ old('orientation') == 'vertical' ? 'selected' : '' }}>Vertical (Portrait)</option>
                        </select>
                    </div>
                     <p class="mt-2 text-xs text-gray-500">Choose based on your design</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-x-4 pt-6 mt-6 border-t border-gray-900/10">
                <a href="{{ route('admin.forms.index') }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-gray-500">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    Create Form & Add Questions
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
