<!-- ========================================================================= -->
<!-- MODAL PERINGATAN VALIDASI DATA (Dark Luxury Modal)                        -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showValidationModal" x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="showValidationModal = false"
             x-show="showValidationModal"
             x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-slate-900 border border-rose-500/50 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl shadow-rose-950/70 space-y-4 relative overflow-hidden">
            
            <!-- Top Gradient Accent Strip -->
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-amber-500 via-rose-500 to-amber-500"></div>

            <div class="flex items-start space-x-3.5 pt-1">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center shrink-0 shadow-inner text-xl">
                    ⚠️
                </div>
                <div class="space-y-1 min-w-0 flex-1">
                    <span class="text-[10px] uppercase font-bold text-rose-400 tracking-wider block" x-text="validationModal.badge || 'Pemeriksaan Formulir'"></span>
                    <h3 class="text-base sm:text-lg font-black text-white leading-snug tracking-tight" x-text="validationModal.title || 'Data Belum Lengkap'"></h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed whitespace-pre-line" x-text="validationModal.message"></p>
                </div>
            </div>

            <!-- Missing Fields Checklist -->
            <template x-if="validationModal.missingFields && validationModal.missingFields.length > 0">
                <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-2">
                    <span class="text-[10px] uppercase font-bold text-amber-400 block tracking-wider">Field yang Perlu Dilengkapi:</span>
                    <ul class="space-y-1.5 text-xs text-slate-300">
                        <template x-for="(f, idx) in validationModal.missingFields" :key="idx">
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-rose-400 shrink-0"></span>
                                <span class="font-medium text-slate-200" x-text="f"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </template>

            <!-- Action Button -->
            <div class="pt-3 border-t border-slate-800 flex items-center justify-end">
                <button type="button" @click="showValidationModal = false"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-500 hover:to-amber-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-rose-600/30 transition-all active:scale-95 cursor-pointer">
                    Mengerti &amp; Lengkapi Data ↵
                </button>
            </div>
        </div>
    </div>
</template>

<!-- Floating Notification Toast -->
<div x-show="toast.show" 
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
     class="fixed bottom-6 right-6 z-50 max-w-md w-full p-4 rounded-2xl border shadow-2xl backdrop-blur-xl flex items-start space-x-3"
     :class="{
         'bg-emerald-950/90 border-emerald-500/50 text-emerald-100 shadow-emerald-950/50': toast.type === 'success',
         'bg-rose-950/90 border-rose-500/50 text-rose-100 shadow-rose-950/50': toast.type === 'error',
         'bg-amber-950/90 border-amber-500/50 text-amber-100 shadow-amber-950/50': toast.type === 'warning'
     }"
     x-cloak>
    <div class="text-xl shrink-0 mt-0.5" x-text="toast.type === 'success' ? '🎉' : (toast.type === 'warning' ? '⚠️' : '❌')"></div>
    <div class="flex-1 min-w-0">
        <h4 class="text-xs font-bold uppercase tracking-wider mb-0.5" x-text="toast.title"></h4>
        <p class="text-xs leading-relaxed opacity-90 whitespace-pre-line" x-text="toast.message"></p>
    </div>
    <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-white shrink-0 p-1 cursor-pointer">
        ✕
    </button>
</div>
