        <!-- GLOBAL FLOATING TOAST NOTIFICATION POPUP -->
        <div x-show="toast.show" x-cloak class="no-print fixed bottom-6 right-6 max-w-sm w-full bg-slate-900/95 border rounded-2xl p-4 shadow-2xl backdrop-blur-md flex items-center justify-between space-x-3" style="z-index: 100000 !important;"
             :class="{
                 'border-emerald-500/40 text-emerald-300': toast.type === 'success',
                 'border-rose-500/40 text-rose-300': toast.type === 'error',
                 'border-amber-500/40 text-amber-300': toast.type === 'warning',
                 'border-cyan-500/40 text-cyan-300': toast.type === 'info'
             }">
            <div class="flex items-center space-x-2.5 min-w-0">
                <span class="text-base shrink-0" x-text="toast.type === 'success' ? '✅' : (toast.type === 'error' ? '⚠️' : 'ℹ️')"></span>
                <p class="text-xs font-bold leading-snug truncate" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-white text-base font-bold shrink-0">&times;</button>
        </div>
