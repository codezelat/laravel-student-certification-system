@extends('admin.layouts.app')

@section('title', 'Design Certificate - ' . $form->title)

@section('content')
<div class="space-y-6" x-data="certificateDesigner()">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Design Certificate
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Drag and drop the name to position it. Customize style on the right.
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex gap-3">
             <a href="{{ route('admin.forms.edit', $form) }}" 
               class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 sm:w-auto">
                Back to Edit
            </a>
            <button type="button" @click="$refs.form.submit()"
                    class="inline-flex items-center justify-center rounded-xl border border-transparent bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 sm:w-auto">
                Save Design
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Canvas Area -->
        <div class="lg:col-span-3">
            <div class="bg-gray-200 rounded-xl border border-gray-300 overflow-auto shadow-inner p-4 relative flex items-center justify-center min-h-[600px]">
                
                <!-- Scaling Container to ensure it fits -->
                <div class="relative bg-white shadow-2xl transition-all duration-200 ease-out flex-shrink-0"
                     :style="`width: {{ $form->orientation === 'horizontal' ? '1123px' : '794px' }}; height: {{ $form->orientation === 'horizontal' ? '794px' : '1123px' }}; transform: scale(${scale}); transform-origin: center center;`"
                     x-ref="canvas">
                    
                    @if($form->certificate_image)
                        <img src="{{ Storage::url($form->certificate_image) }}" 
                             class="absolute inset-0 w-full h-full pointer-events-none select-none"
                             :style="`object-fit: ${backgroundFit}`">
                    @else
                        <div class="absolute inset-0 w-full h-full bg-white pointer-events-none"></div>
                    @endif

                    <!-- Draggable Name -->
                    <div class="absolute cursor-move select-none whitespace-nowrap border-2 border-transparent hover:border-dashed hover:border-primary-400 rounded transition-colors"
                         :style="`left: ${x}px; top: ${y}px; font-size: ${fontSize}px; color: ${fontColor}; font-weight: ${fontWeight}; font-style: ${fontStyle};`"
                         @mousedown="startDragging($event)"
                         x-text="'Participant Name'">
                    </div>

                </div>
                
                <div class="absolute bottom-4 right-4 text-xs text-gray-500 bg-white/80 px-2 py-1 rounded z-10">
                    Preview scale: <span x-text="Math.round(scale * 100) + '%'"></span>
                </div>
            </div>
        </div>

        <!-- Controls Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-6 sticky top-6">
                
                <!-- Zoom Controls -->
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Zoom Level</h3>
                        <span class="text-xs font-medium text-gray-700" x-text="Math.round(scale * 100) + '%'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="zoomOut()" class="p-1.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        <input type="range" min="0.2" max="1.5" step="0.05" x-model.number="scale" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <button type="button" @click="zoomIn()" class="p-1.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>

                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Style Settings</h3>

                <form action="{{ route('admin.forms.save-design', $form) }}" method="POST" x-ref="form" class="space-y-6">
                    @csrf
                    
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="x" :value="Math.round(x)">
                    <input type="hidden" name="y" :value="Math.round(y)">
                    <input type="hidden" name="font_weight" :value="fontWeight">
                    <input type="hidden" name="font_style" :value="fontStyle">
                    <input type="hidden" name="background_fit" :value="backgroundFit">

                    <!-- Background Fit -->
                    <div>
                        <label for="background_fit_select" class="block text-sm font-medium text-gray-700">Background Fit</label>
                        <div class="mt-2">
                            <select id="background_fit_select" x-model="backgroundFit" class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                <option value="fill">Fill (Stretch)</option>
                                <option value="cover">Cover (Crop)</option>
                                <option value="contain">Contain (Fit)</option>
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Adjust how your image fits the A4 page.</p>
                    </div>

                    <!-- Font Size -->
                    <div>
                        <label for="font_size" class="block text-sm font-medium text-gray-700">Font Size (px)</label>
                        <div class="mt-2 flex items-center gap-4">
                            <input type="range" id="font_size_range" min="20" max="150" x-model="fontSize" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                            <input type="number" id="font_size" name="font_size" x-model="fontSize" class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-center">
                        </div>
                    </div>

                    <!-- Font Color -->
                    <div>
                        <label for="font_color" class="block text-sm font-medium text-gray-700">Font Color</label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="color" id="font_color" name="font_color" x-model="fontColor" class="h-10 w-20 p-0 border-0 rounded cursor-pointer">
                            <input type="text" x-model="fontColor" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm uppercase" maxlength="7">
                        </div>
                    </div>

                    <!-- Typography -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Typography</label>
                        <div class="mt-2 flex items-center gap-3">
                            <button type="button" 
                                    @click="fontWeight = (fontWeight === 'bold' ? 'normal' : 'bold')"
                                    :class="fontWeight === 'bold' ? 'bg-primary-100 text-primary-700 ring-2 ring-primary-500 shadow-inner' : 'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50'"
                                    class="inline-flex items-center justify-center px-3 py-2 text-sm font-bold rounded-md transition-all w-16">
                                B
                            </button>
                            <button type="button" 
                                    @click="fontStyle = (fontStyle === 'italic' ? 'normal' : 'italic')"
                                    :class="fontStyle === 'italic' ? 'bg-primary-100 text-primary-700 ring-2 ring-primary-500 shadow-inner' : 'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50'"
                                    class="inline-flex items-center justify-center px-3 py-2 text-sm italic rounded-md transition-all w-16">
                                I
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1 md:flex md:justify-between">
                                    <p class="text-sm text-blue-700">Coordinates: <span x-text="Math.round(x)"></span>, <span x-text="Math.round(y)"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function certificateDesigner() {
        return {
            x: {{ $form->certificate_settings['x'] ?? (int)($form->orientation === 'horizontal' ? 1123/2 - 200 : 794/2 - 150) }},
            y: {{ $form->certificate_settings['y'] ?? (int)($form->orientation === 'horizontal' ? 794/2 : 1123/2) }},
            fontSize: {{ $form->certificate_settings['font_size'] ?? 42 }},
            fontColor: '{{ $form->certificate_settings['font_color'] ?? "#003366" }}',
            fontWeight: '{{ $form->certificate_settings['font_weight'] ?? "bold" }}',
            fontStyle: '{{ $form->certificate_settings['font_style'] ?? "normal" }}',
            backgroundFit: '{{ $form->certificate_settings['background_fit'] ?? "fill" }}',
            scale: 0.65,
            isDragging: false,
            startX: 0,
            startY: 0,
            initialLeft: 0,

            startDragging(e) {
                this.isDragging = true;
                this.startX = e.clientX;
                this.startY = e.clientY;
                this.initialLeft = this.x;
                this.initialTop = this.y;

                window.addEventListener('mousemove', this.handleDrag.bind(this));
                window.addEventListener('mouseup', this.stopDragging.bind(this));
            },

            handleDrag(e) {
                if (!this.isDragging) return;
                
                // Calculate scale factor using the current dynamic scale
                const dx = (e.clientX - this.startX) / this.scale;
                const dy = (e.clientY - this.startY) / this.scale;

                this.x = this.initialLeft + dx;
                this.y = this.initialTop + dy;
            },

            stopDragging() {
                this.isDragging = false;
                window.removeEventListener('mousemove', this.handleDrag.bind(this));
                window.removeEventListener('mouseup', this.stopDragging.bind(this));
            },

            zoomIn() {
                if (this.scale < 1.5) this.scale = Math.min(1.5, this.scale + 0.05);
            },

            zoomOut() {
                if (this.scale > 0.2) this.scale = Math.max(0.2, this.scale - 0.05);
            }
        }
    }
</script>
@endsection
