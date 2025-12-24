{{-- Delete Modal --}}
<div
    x-data="{
        open: false,
        id: null,
        title: '',
        busy: false,

        openModal(payload){
            this.id = payload.id
            this.title = payload.title || ''
            this.open = true
        },

        close(){
            if(this.busy) return
            this.open = false
            this.id = null
            this.title = ''
        },

        async confirm(){
            if(!this.id) return
            this.busy = true

            try{
                await $wire.deleteDocument(this.id)

                // fermer la modal (sans passer par close() car busy=true)
                this.open = false
                this.id = null
                this.title = ''
            } finally {
                this.busy = false
            }
        }
    }"
    x-on:open-delete-doc.window="openModal($event.detail)"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50"
    aria-labelledby="delete-title"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
        @click="close()"
    ></div>

    {{-- Panel --}}
    <div class="relative flex min-h-full items-end sm:items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 sm:translate-y-0 sm:scale-95"
            @keydown.escape.window="close()"
            class="w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl
                   dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="p-5 sm:p-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600
                                dark:bg-red-900/20 dark:text-red-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v3m0 4h.01M10.29 3.86l-7.4 12.84A1.5 1.5 0 004.2 19h15.6a1.5 1.5 0 001.3-2.3l-7.4-12.84a1.5 1.5 0 00-2.6 0z"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h3 id="delete-title" class="text-base font-semibold text-gray-900 dark:text-white">
                            Supprimer ce document ?
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Cette action est irréversible.
                            <span class="font-medium text-gray-900 dark:text-white" x-text="title ? `« ${title} »` : ''"></span>
                        </p>
                    </div>
                </div>

                <div class="mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button
                        type="button"
                        @click="close()"
                        :disabled="busy"
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold
                               border border-gray-200 bg-white text-gray-700 hover:bg-gray-50
                               dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700/40
                               disabled:opacity-60"
                    >
                        Annuler
                    </button>

                    <button
                        type="button"
                        @click="confirm()"
                        :disabled="busy"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold
                               bg-red-600 text-white hover:bg-red-700
                               focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2
                               dark:focus-visible:ring-offset-gray-900
                               disabled:opacity-60"
                    >
                        <svg x-show="busy" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                        </svg>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
