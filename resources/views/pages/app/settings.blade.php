<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="{
        tab: 0,
        tabs: [
            { key: 'profile', icon: 'user' },
            { key: 'company', icon: 'building' },
            { key: 'chart', icon: 'list-checks' },
            { key: 'notifications', icon: 'bell' },
        ],
        profile: { name: $store.auth.user?.name || '', email: $store.auth.user?.email || '' },
        notif: { overdueAlert: true, dueSoonAlert: true, newInvoice: false, monthlyReport: true, reconcileReminder: false },
        notifItems: ['overdueAlert', 'dueSoonAlert', 'newInvoice', 'monthlyReport', 'reconcileReminder'],
        saveProfile() {
            $store.auth.updateProfile({ name: this.profile.name, email: this.profile.email });
            $store.ui.notifySuccess($t('settings.saved'));
        },
    }">
        <div class="mb-6">
            <nav aria-label="Breadcrumb" class="mb-2">
                <ol class="flex flex-wrap items-center gap-1.5 text-sm">
                    <li class="flex items-center gap-1.5">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-muted transition hover:text-brand-600" x-text="$t('appNav.dashboard')"></a>
                        <x-icon name="chevron-right" :size="14" class="text-gray-400" />
                    </li>
                    <li><span aria-current="page" class="font-medium text-heading" x-text="$t('appNav.settings')"></span></li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-heading" x-text="$t('settings.title')"></h1>
            <p class="mt-1 text-sm text-muted" x-text="$t('settings.subtitle')"></p>
        </div>

        <div class="surface-card rounded-2xl border p-5 shadow-theme-sm md:p-6">
            {{-- Tabs --}}
            <div class="mb-6 flex flex-wrap gap-1 border-b border-themed">
                <template x-for="(t, i) in tabs" :key="t.key">
                    <button type="button" @click="tab = i"
                        class="inline-flex items-center gap-2 border-b-2 px-4 py-2.5 text-sm font-medium transition"
                        :class="tab === i ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-muted hover:text-body'">
                        <template x-if="t.icon === 'user'"><x-icon name="user" :size="16" /></template>
                        <template x-if="t.icon === 'building'"><x-icon name="building" :size="16" /></template>
                        <template x-if="t.icon === 'list-checks'"><x-icon name="list-checks" :size="16" /></template>
                        <template x-if="t.icon === 'bell'"><x-icon name="bell" :size="16" /></template>
                        <span x-text="$t('settings.tabs.' + t.key)"></span>
                    </button>
                </template>
            </div>

            {{-- Profile --}}
            <div x-show="tab === 0">
                <form class="max-w-lg space-y-4" @submit.prevent="saveProfile()">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('auth.name')"></label>
                        <input x-model="profile.name" class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3.5 text-sm text-body" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('auth.email')"></label>
                        <input type="email" x-model="profile.email" class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3.5 text-sm text-body" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.role')"></label>
                        <input :value="$store.auth.user?.role || ''" disabled class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3.5 text-sm text-body opacity-60" />
                    </div>
                    <button type="submit" class="focus-ring rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600" x-text="$t('common.save')"></button>
                </form>
            </div>

            {{-- Company --}}
            <div x-show="tab === 1" x-cloak class="space-y-6">
                <div class="max-w-lg space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.companyName')"></label>
                        <input :value="$store.auth.user?.company || ''" class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3.5 text-sm text-body" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.cnpj')"></label>
                        <input value="12.345.678/0001-90" class="focus-ring h-11 w-full rounded-lg border border-themed bg-transparent px-3.5 text-sm text-body" />
                    </div>
                </div>
                <div>
                    <h4 class="mb-3 text-sm font-semibold text-heading" x-text="$t('settings.bankAccounts')"></h4>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <template x-for="a in $store.fin.accounts" :key="a.id">
                            <div class="surface-card flex items-center gap-3 rounded-xl border p-4">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl text-white" :style="`background-color: ${a.color}`">
                                    <x-icon name="bank" :size="18" />
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-heading" x-text="a.name"></p>
                                    <p class="text-xs text-muted" x-text="a.bank + ' · ' + a.number"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Chart of accounts --}}
            <div x-show="tab === 2" x-cloak>
                <div class="surface-card overflow-hidden rounded-xl border">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-themed text-muted">
                                <th class="px-5 py-3 font-medium" x-text="$t('entryForm.category')"></th>
                                <th class="px-5 py-3 font-medium" x-text="$t('entryForm.type')"></th>
                                <th class="px-5 py-3 font-medium" x-text="$t('settings.group')"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="c in $store.fin.categories" :key="c.id">
                                <tr class="border-b border-themed last:border-0">
                                    <td class="px-5 py-3 font-medium text-heading" x-text="c.name"></td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                            :class="c.type === 'receita' ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500' : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500'"
                                            x-text="$t('entryType.' + c.type)"></span>
                                    </td>
                                    <td class="px-5 py-3 text-muted" x-text="$t('category.' + c.group)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Notifications --}}
            <div x-show="tab === 3" x-cloak class="max-w-xl space-y-2">
                <template x-for="key in notifItems" :key="key">
                    <label class="surface-card flex cursor-pointer items-center justify-between rounded-xl border p-4">
                        <div>
                            <p class="text-sm font-medium text-heading" x-text="$t('settings.notif.' + key + '.title')"></p>
                            <p class="text-xs text-muted" x-text="$t('settings.notif.' + key + '.desc')"></p>
                        </div>
                        <input type="checkbox" x-model="notif[key]" class="h-5 w-5 rounded border-themed accent-brand-500" />
                    </label>
                </template>
            </div>
        </div>
    </div>
</x-layout.app>
