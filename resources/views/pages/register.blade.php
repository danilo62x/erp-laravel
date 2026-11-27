<x-layout.blank :pageTitle="$pageTitle ?? null">
    <x-ui.auth-shell titleKey="auth.registerTitle" subtitleKey="auth.registerSubtitle">
        <x-slot:footer>
            <span x-text="$t('auth.haveAccount')"></span>
            <a href="{{ route('login') }}" class="font-medium text-brand-500 hover:text-brand-600" x-text="$t('auth.loginCta')"></a>
        </x-slot:footer>

        <form method="POST" action="{{ route('register.submit') }}" class="space-y-5"
            x-data="{
                form: { name: '{{ old('name') }}', email: '{{ old('email') }}', password: '', confirm: '' },
                errors: {},
                validate(e) {
                    const err = {};
                    if (!this.form.name.trim()) err.name = $t('form.required');
                    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(this.form.email)) err.email = $t('form.required');
                    if (this.form.password.length < 6) err.password = $t('form.min', { n: 6 });
                    if (this.form.confirm !== this.form.password) err.confirm = $t('form.required');
                    this.errors = err;
                    if (Object.keys(err).length) { e.preventDefault(); }
                    else { $store.auth.setUser({ id: 1, name: this.form.name, email: this.form.email, role: 'Administrador', company: 'Minha Empresa' }); }
                }
            }"
            @submit="validate($event)">
            @csrf

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-heading" x-text="$t('auth.name')"></label>
                <input name="name" x-model="form.name" :placeholder="$t('auth.namePh')" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.name ? 'border-error-500' : 'border-themed'" />
                <p x-show="errors.name" x-cloak class="text-xs text-error-500" x-text="errors.name"></p>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-heading" x-text="$t('auth.email')"></label>
                <input name="email" type="email" x-model="form.email" placeholder="financeiro@empresa.com" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.email ? 'border-error-500' : 'border-themed'" />
                <p x-show="errors.email" x-cloak class="text-xs text-error-500" x-text="errors.email"></p>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-heading" x-text="$t('auth.password')"></label>
                <input name="password" type="password" x-model="form.password" placeholder="••••••••" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.password ? 'border-error-500' : 'border-themed'" />
                <p x-show="errors.password" x-cloak class="text-xs text-error-500" x-text="errors.password"></p>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-heading" x-text="$t('auth.password')"></label>
                <input name="password_confirmation" type="password" x-model="form.confirm" placeholder="••••••••" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.confirm ? 'border-error-500' : 'border-themed'" />
                <p x-show="errors.confirm" x-cloak class="text-xs text-error-500" x-text="errors.confirm"></p>
            </div>

            <button type="submit"
                class="flex h-11 w-full items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 focus-ring"
                x-text="$t('auth.registerCta')"></button>
        </form>
    </x-ui.auth-shell>
</x-layout.blank>
