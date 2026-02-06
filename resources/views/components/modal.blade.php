<div 
    x-data="{ 
        open: false,
        title: '',
        message: '',
        confirmText: 'Confirm',
        confirmAction: null,
        mode: 'delete', // delete or info
        show(title, message, confirmAction, mode = 'delete') {
            this.title = title;
            this.message = message;
            this.confirmAction = confirmAction; // Callback function or form ID string
            this.mode = mode;
            this.confirmText = mode === 'delete' ? 'Delete' : 'Confirm';
            this.open = true;
        },
        close() {
            this.open = false; 
        },
        confirm() {
            if (typeof this.confirmAction === 'string') {
                document.getElementById(this.confirmAction).submit();
            } else if (typeof this.confirmAction === 'function') {
                this.confirmAction();
            }
            this.close();
        }
    }" 
    @confirm-modal.window="show($event.detail.title, $event.detail.message, $event.detail.confirmAction, $event.detail.mode)"
    class="relative z-50 pointer-events-none"
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
    x-show="open"
>
    <!-- Backdrop -->
    <div 
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm pointer-events-auto"
    ></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto pointer-events-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div 
                x-show="open"
                @click.away="close()"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
            >
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div 
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10"
                            :class="mode === 'delete' ? 'bg-red-100' : 'bg-blue-100'"
                        >
                            <svg x-show="mode === 'delete'" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                            </svg>
                            <svg x-show="mode !== 'delete'" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zM12 15.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="message"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button 
                        type="button" 
                        class="inline-flex w-full justify-center rounded-lg px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto transition-colors"
                        :class="mode === 'delete' ? 'bg-red-600 hover:bg-red-500' : 'bg-primary-600 hover:bg-primary-500'"
                        @click="confirm()"
                        x-text="confirmText"
                    ></button>
                    <button 
                        type="button" 
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors" 
                        @click="close()"
                    >Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
