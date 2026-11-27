<x-layout.blank :pageTitle="$pageTitle ?? null">
    <x-ui.auth-shell titleKey="auth.forgot" subtitleKey="auth.loginSubtitle">
        <x-slot:footer>
            <a href="{{ route('login') }}" class="font-medium text-brand-500 hover:text-brand-600" x-text="$t('auth.loginCta')"></a>
        </x-slot:footer>

        <form class="space-y-5"
            x-data="{
                email: '', error: '', submitting: false,
                submit() {
                    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(this.email)) { this.error = $t('form.required'); return; }
                    this.error = '';
                    this.submitting = true;
                    setTimeout(() => {
                        $store.ui.notifySuccess($t('contact.sent'));
                        this.email = '';
                        this.submitting = false;
                    }, 600);
                }
            }"
            @submit.prevent="submit()">
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-heading" x-text="$t('auth.email')"></label>
                <input type="email" x-model="email" placeholder="financeiro@empresa.com" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="error ? 'border-error-500' : 'border-themed'" />
                <p x-show="error" x-cloak class="text-xs text-error-500" x-text="error"></p>
            </div>
            <button type="submit" :disabled="submitting"
                class="flex h-11 w-full items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 focus-ring disabled:opacity-60"
                x-text="$t('auth.loginCta')"></button>
        </form>
    </x-ui.auth-shell>
</x-layout.blank>
