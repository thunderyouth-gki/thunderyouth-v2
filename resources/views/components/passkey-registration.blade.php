@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        showForm: false,
        name: '',
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        getDefaultPasskeyName() {
            const ua = navigator.userAgent;

            const browser = [
                { pattern: /Edg|Edge/, name: 'Edge' },
                { pattern: /OPR|Opera|OPiOS/, name: 'Opera' },
                { pattern: /Firefox|FxiOS/, name: 'Firefox' },
                { pattern: /Chrome|CriOS/, name: 'Chrome' },
                { pattern: /Safari/, name: 'Safari' },
            ].find(({ pattern }) => pattern.test(ua))?.name;

            const os = [
                { pattern: /iPhone/, name: 'iPhone' },
                { pattern: /iPad|Macintosh(?=.*Mobile)/, name: 'iPad' },
                { pattern: /Android/, name: 'Android' },
                { pattern: /Mac/, name: 'Mac' },
                { pattern: /Windows/, name: 'Windows' },
            ].find(({ pattern }) => pattern.test(ua))?.name;

            return [browser, os].filter(Boolean).join(' on ') || '';
        },
        init() {
            this.name = this.getDefaultPasskeyName();
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async register() {
            if (!this.name.trim()) return;

            this.loading = true;
            this.error = null;

            try {
                await window.Passkeys.register({ name: this.name });
                this.name = '';
                this.showForm = false;
                await $wire.loadPasskeys();
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
        cancel() {
            this.showForm = false;
            this.name = '';
            this.error = null;
        },
    }"
>
    <template x-if="!supported">
        <p class="text-sm text-textlight">{{ __('Passkeys are not supported in this browser.') }}</p>
    </template>

    <template x-if="supported && !showForm">
        <div>
            <button
                type="button"
                class="bg-primary hover:bg-primary/90 text-white font-bold py-2.5 px-6 rounded-xl transition shadow-soft text-sm flex items-center gap-2"
                x-on:click="showForm = true"
            >
                <i class="fa-solid fa-plus"></i> {{ __('Add passkey') }}
            </button>
        </div>
    </template>

    <template x-if="supported && showForm">
        <div class="space-y-4 rounded-xl border border-accent bg-background/50 p-5 mt-4">
            <div>
                <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">{{ __('Passkey name') }}</label>
                <input
                    type="text"
                    x-model="name"
                    placeholder="{{ __('e.g., MacBook Pro, iPhone') }}"
                    x-on:keydown.enter.prevent="register()"
                    x-ref="passkeyNameInput"
                    x-init="$nextTick(() => $refs.passkeyNameInput?.focus())"
                    class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm"
                />
                <p class="text-[11px] text-textlight mt-1.5 font-medium">{{ __('Give this passkey a name to help you identify it later.') }}</p>
            </div>

            <p x-show="error" x-text="error" x-cloak class="text-sm text-red-500 font-semibold"></p>

            <div class="flex gap-3 pt-2">
                <button
                    type="button"
                    class="bg-primary hover:bg-primary/90 text-white font-bold py-2.5 px-6 rounded-xl transition shadow-soft text-sm"
                    x-on:click="register()"
                    x-bind:disabled="loading || !name.trim()"
                >
                    <span x-show="!loading">{{ __('Register passkey') }}</span>
                    <span x-show="loading" x-cloak><i class="fa-solid fa-circle-notch fa-spin mr-1"></i> {{ __('Registering...') }}</span>
                </button>
                <button
                    type="button"
                    class="bg-surface border border-accent hover:border-primary text-textlight hover:text-text font-bold py-2.5 px-6 rounded-xl transition shadow-soft text-sm"
                    x-on:click="cancel()"
                >
                    {{ __('Cancel') }}
                </button>
            </div>
        </div>
    </template>
</div>
