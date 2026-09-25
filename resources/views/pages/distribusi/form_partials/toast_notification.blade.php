        <!-- TOAST NOTIFICATION -->
        <div x-show="toast.show" x-cloak
             class="no-print fixed bottom-6 right-6 max-w-sm w-full bg-slate-900/95 border rounded-2xl p-4 shadow-2xl backdrop-blur-md flex items-center justify-between space-x-3"
             style="z-index: 100000 !important;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             :class="{
                 'border-emerald-500/40': toast.type === 'success',
                 'border-rose-500/40': toast.type === 'error',
                 'border-amber-500/40': toast.type === 'warning',
                 'border-cyan-500/40': toast.type === 'info'
             }">
            <div class="flex items-start space-x-3 flex-1 min-w-0">
                <span class="text-xl shrink-0 mt-0.5"
                      x-text="toast.type === 'success' ? '✅' : (toast.type === 'error' ? '❌' : (toast.type === 'warning' ? '⚠️' : 'ℹ️'))"></span>
                <p class="text-sm font-semibold leading-snug"
                   :class="{
                       'text-emerald-300': toast.type === 'success',
                       'text-rose-300': toast.type === 'error',
                       'text-amber-300': toast.type === 'warning',
                       'text-cyan-300': toast.type === 'info'
                   }"
                   x-text="String(toast.message || '').replace(/^[\s✅✔️☑️✓✔⚠️❌🚫⛔ℹ️🗑️✏️🔑💾]+/, '').trim()"></p>
            </div>
            <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-white text-base font-bold shrink-0">&times;</button>
        </div>
