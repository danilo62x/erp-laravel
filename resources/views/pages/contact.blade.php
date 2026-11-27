@php($channels = [
    ['icon' => 'mail', 'key' => 'email'],
    ['icon' => 'phone', 'key' => 'phone'],
    ['icon' => 'map-pin', 'key' => 'address'],
])
<x-layout.public :pageTitle="$pageTitle ?? null">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8" x-data="{ sent: false }">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-title-md font-bold text-heading" x-text="$t('contact.title')"></h1>
            <p class="mt-4 text-muted" x-text="$t('contact.subtitle')"></p>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-1">
                @foreach ($channels as $c)
                    <div class="surface-card flex items-start gap-3 rounded-2xl border p-5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                            <x-icon name="{{ $c['icon'] }}" :size="20" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-heading" x-text="$t('contact.channels.{{ $c['key'] }}.label')"></p>
                            <p class="text-sm text-muted" x-text="$t('contact.channels.{{ $c['key'] }}.value')"></p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="surface-card rounded-2xl border p-6 shadow-theme-sm lg:col-span-2 md:p-8">
                <template x-if="sent">
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-success-50 text-success-600 dark:bg-success-500/15">
                            <x-icon name="check-circle" :size="28" />
                        </span>
                        <h3 class="mt-4 text-lg font-semibold text-heading" x-text="$t('contact.thanksTitle')"></h3>
                        <p class="mt-2 text-sm text-muted" x-text="$t('contact.thanksBody')"></p>
                    </div>
                </template>
                <template x-if="!sent">
                    <form @submit.prevent="sent = true; $store.ui.notifySuccess($t('contact.sent'))" class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <input required :placeholder="$t('contact.form.name')" class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3.5 text-sm text-body placeholder:text-muted" />
                            <input required type="email" :placeholder="$t('contact.form.email')" class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3.5 text-sm text-body placeholder:text-muted" />
                        </div>
                        <input :placeholder="$t('contact.form.company')" class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3.5 text-sm text-body placeholder:text-muted" />
                        <textarea required rows="5" :placeholder="$t('contact.form.message')" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body placeholder:text-muted"></textarea>
                        <button type="submit" class="focus-ring inline-flex h-11 items-center gap-2 rounded-lg bg-brand-500 px-6 text-sm font-medium text-white transition hover:bg-brand-600">
                            <x-icon name="send" :size="16" /> <span x-text="$t('contact.form.submit')"></span>
                        </button>
                    </form>
                </template>
            </div>
        </div>
    </div>
</x-layout.public>
