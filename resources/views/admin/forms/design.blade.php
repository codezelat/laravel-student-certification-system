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

                    <!-- Draggable Name Box -->
                    <div class="absolute cursor-move select-none border-2 border-dashed border-primary-300/50 hover:border-primary-500 bg-primary-50/10 transition-colors"
                         :style="`left: ${x}px; top: ${y}px; width: ${maxWidth}px; min-height: ${fontSize * 1.5}px; text-align: ${textAlign}; transform: translateY(${verticalAlign === 'middle' ? '-50%' : (verticalAlign === 'bottom' ? '-100%' : '0')});`"
                         @mousedown="startDragging($event)">
                        <span :style="`font-size: ${fontSize}px; color: ${fontColor}; font-weight: ${fontWeight}; font-style: ${fontStyle}; line-height: 1.2;`" 
                              class="px-1 break-words block"
                              x-text="'Participant Name'"></span>
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
                
                <!-- Test Button -->
                <div class="mb-6">
                    <button type="button" @click="showPreviewModal = true; refreshPreview()" class="w-full flex justify-center items-center gap-2 bg-gradient-to-r from-gray-800 to-gray-900 border border-transparent text-white font-medium py-2.5 px-4 rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Test Design with Name
                    </button>
                    <p class="mt-2 text-xs text-center text-gray-500">Preview with real names to check sizing.</p>
                </div>

                <!-- Zoom Controls -->
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Zoom</h3>
                        <span class="text-xs font-medium text-gray-700 font-mono" x-text="Math.round(scale * 100) + '%'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="zoomOut()" class="p-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        <input type="range" min="0.2" max="1.5" step="0.05" x-model.number="scale" class="flex-1 min-w-0 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <button type="button" @click="zoomIn()" class="p-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors flex-shrink-0">
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
                    <input type="hidden" name="max_width" :value="maxWidth">
                    <input type="hidden" name="text_align" :value="textAlign">
                    <input type="hidden" name="vertical_align" :value="verticalAlign">

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
                    </div>

                    <!-- Text Alignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Text Alignment</label>
                        <div class="mt-2 flex items-center gap-1 bg-gray-100 p-1 rounded-lg">
                            <button type="button" @click="textAlign = 'left'" 
                                    :class="textAlign === 'left' ? 'bg-white shadow text-primary-600' : 'text-gray-500 hover:text-gray-700'"
                                    class="flex-1 p-1.5 rounded-md transition-all flex justify-center" title="Left Align">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"/></svg>
                            </button>
                            <button type="button" @click="textAlign = 'center'" 
                                    :class="textAlign === 'center' ? 'bg-white shadow text-primary-600' : 'text-gray-500 hover:text-gray-700'"
                                    class="flex-1 p-1.5 rounded-md transition-all flex justify-center" title="Center Align">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M4 18h16"/></svg>
                            </button>
                            <button type="button" @click="textAlign = 'right'" 
                                    :class="textAlign === 'right' ? 'bg-white shadow text-primary-600' : 'text-gray-500 hover:text-gray-700'"
                                    class="flex-1 p-1.5 rounded-md transition-all flex justify-center" title="Right Align">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M4 18h16"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Max Lines -->
                    <div>
                        <label for="max_lines" class="block text-sm font-medium text-gray-700">Max Lines</label>
                        <div class="mt-2">
                             <input type="number" id="max_lines" name="max_lines" x-model.number="maxLines" min="1" max="5" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Allow names to wrap to multiple lines if needed.</p>
                    </div>

                    <!-- Text Area Width -->
                    <div>
                        <label for="max_width" class="block text-sm font-medium text-gray-700">Max Width (px)</label>
                        <div class="mt-2 flex items-center gap-4">
                            <input type="range" id="max_width_range" min="100" max="1500" step="10" x-model.number="maxWidth" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                            <input type="number" id="max_width" x-model.number="maxWidth" class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-center">
                        </div>
                    </div>

                    <!-- Font Size -->
                    <div>
                        <label for="font_size" class="block text-sm font-medium text-gray-700">Max Font Size (px)</label>
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

    <!-- Preview Modal -->
    <div x-show="showPreviewModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition.opacity>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showPreviewModal = false">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                    Test Certificate Design
                                </h3>
                                <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-4 text-sm text-blue-800 flex items-start">
                                <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                <p><strong>Tip:</strong> Ensure you hit "Save Settings" before testing to see your latest changes applied.</p>
                            </div>
                            
                            <!-- Input -->
                            <div class="flex gap-4 mb-6">
                                <div class="flex-1">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Pass Name</label>
                                    <input type="text" x-model="testName" @keydown.enter="refreshPreview()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Enter a student name (e.g. Christopher Alexander Smith)">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="refreshPreview()" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none sm:text-sm tracking-wide uppercase">
                                        Generate
                                    </button>
                                </div>
                            </div>

                            <!-- Preview Area -->
                            <div class="bg-gray-100 rounded-xl p-8 flex justify-center items-center min-h-[400px] border border-gray-200 overflow-auto relative">
                                <div x-show="previewLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100/80 z-10 transition-opacity">
                                    <svg class="animate-spin h-10 w-10 text-primary-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-gray-600 font-medium animate-pulse">Generating Preview...</span>
                                </div>
                                <img x-show="previewUrl" :src="previewUrl" class="max-w-full h-auto shadow-2xl rounded" @load="previewLoading = false">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <a :href="previewUrl" download="certificate-test.jpg" class="w-full inline-flex justify-center items-center gap-2 rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-900 text-base font-medium text-white hover:bg-gray-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Image
                    </a>
                    <button type="button" @click="showPreviewModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
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
            maxWidth: {{ $form->certificate_settings['max_width'] ?? 800 }},
            textAlign: '{{ $form->certificate_settings['text_align'] ?? "center" }}',
            verticalAlign: '{{ $form->certificate_settings['vertical_align'] ?? "top" }}',
            maxLines: {{ $form->certificate_settings['max_lines'] ?? 1 }},
            
            // Zoom
            scale: 0.65,
            zoomIn() {
                if (this.scale < 1.5) this.scale = Math.min(1.5, this.scale + 0.05);
            },
            zoomOut() {
                if (this.scale > 0.2) this.scale = Math.max(0.2, this.scale - 0.05);
            },

            // Preview Modal
            showPreviewModal: false,
            testName: 'Christopher Alexander Smith',
            previewUrl: '',
            previewLoading: false,
            refreshPreview() {
                this.previewLoading = true;
                this.previewUrl = `{{ route('admin.forms.preview', $form) }}?name=${encodeURIComponent(this.testName)}&t=${Date.now()}`;
            },

            // Dragging
            isDragging: false,
            startX: 0,
            startY: 0,
            initialLeft: 0,
            initialTop: 0,
            dragHandler: null,
            upHandler: null,

            startDragging(e) {
                this.isDragging = true;
                this.startX = e.clientX;
                this.startY = e.clientY;
                this.initialLeft = this.x;
                this.initialTop = this.y;

                // Create stable handlers
                this.dragHandler = (ev) => this.handleDrag(ev);
                this.upHandler = () => this.stopDragging();

                window.addEventListener('mousemove', this.dragHandler);
                window.addEventListener('mouseup', this.upHandler);
            },

            handleDrag(e) {
                if (!this.isDragging) return;
                
                // Calculate scale factor using the current dynamic scale
                const dx = (e.clientX - this.startX) / this.scale;
                const dy = (e.clientY - this.startY) / this.scale;

                this.x = Math.round(this.initialLeft + dx);
                this.y = Math.round(this.initialTop + dy);
            },

            stopDragging() {
                this.isDragging = false;
                if (this.dragHandler) {
                    window.removeEventListener('mousemove', this.dragHandler);
                    this.dragHandler = null;
                }
                if (this.upHandler) {
                    window.removeEventListener('mouseup', this.upHandler);
                    this.upHandler = null;
                }
            }
        }
    }
</script>
@endsection
