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
    <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-white shrink-0 p-1">
        ✕
    </button>
</div>
