<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    @fluxAppearance
</head>


<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
    <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">


        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <flux:sidebar.header>
            <flux:sidebar.brand :href="route('dashboard')" logo="/images/RajaStock.png"
                 name="Raja Stock" />
            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>


        <flux:sidebar.nav>

            <!-- Home -->
           
            <flux:sidebar.group expandable icon="home" heading="Home" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    Dashboard
                </flux:navlist.item>
            </flux:sidebar.group>

            <!-- Produk -->
            <flux:sidebar.group expandable icon="folder-open" heading="Produk" class="grid">
                <flux:navlist.item icon="folder" :href="route('items')" :current="request()->routeIs('items')"
                    wire:navigate>
                    List Barang
                </flux:navlist.item>

                <flux:navlist.item icon="folder" :href="route('merk')" :current="request()->routeIs('merk')"
                    wire:navigate>
                    List Merek
                </flux:navlist.item>
            </flux:sidebar.group>

            <!-- Contacts -->
            <flux:sidebar.group expandable icon="users" heading="Contacts" class="grid">
                <flux:navlist.item icon="building-office" :href="route('supplier')"
                    :current="request()->routeIs('supplier')" wire:navigate>
                    Supplier
                </flux:navlist.item>

                <flux:navlist.item icon="users" :href="route('customer')" :current="request()->routeIs('customer')"
                    wire:navigate>
                    Customer
                </flux:navlist.item>
            </flux:sidebar.group>

            <!-- Transaksi -->
            <flux:sidebar.group expandable icon="shopping-cart" heading="Transaksi" class="grid">
                @can('view purchases')
                <flux:navlist.item icon="shopping-cart" :href="route('purchases')"
                    :current="request()->routeIs('purchases')" wire:navigate>
                    Stok Masuk
                </flux:navlist.item>
                    
                @endcan
                @can('view sales')                   
                <flux:navlist.item icon="shopping-bag" :href="route('sales')" :current="request()->routeIs('sales')"
                    wire:navigate>
                    Stok Keluar
                </flux:navlist.item>
                @endcan
            </flux:sidebar.group>


            <!-- Retur Barang -->
            @can('view purchase returns')
            <flux:sidebar.group expandable icon="arrow-path" heading="Retur Barang" class="grid">
                <flux:navlist.item icon="shopping-cart" :href="route('purchase-returns')"
                    :current="request()->routeIs('purchase-returns')" wire:navigate>
                    Stok Masuk
                </flux:navlist.item>

                <flux:navlist.item icon="shopping-bag" :href="route('sale-returns')"
                    :current="request()->routeIs('sale-returns')" wire:navigate>
                    Stok Keluar
                </flux:navlist.item>
            </flux:sidebar.group>
                
            @endcan
            @role('superadmin|admin')
            <flux:sidebar.group expandable icon="key" heading="Manajemen User" class="grid">
                @role('superadmin')
                <flux:navlist.item icon="users" :href="route('users')" :current="request()->routeIs('users')"
                    wire:navigate>
                    Users
                </flux:navlist.item>
                @endrole
                @role('superadmin|admin')
                <flux:navlist.item icon="document-check" :href="route('audit-logs')"
                    :current="request()->routeIs('audit-logs')" wire:navigate>
                    Audit Log
                </flux:navlist.item>
                @endrole
            </flux:sidebar.group>      
             @endrole         
            <!-- Manajemen User -->

            <!-- Export -->
            @can('view reports')                
            <flux:sidebar.group expandable icon="document-arrow-down" heading="Export Laporan" class="grid">
                <flux:navlist.item icon="document-check" :href="route('stock-item-reports')"
                    :current="request()->routeIs('stock-item-reports')" wire:navigate>
                    Stok Barang
                </flux:navlist.item>
                <flux:navlist.item icon="document-check" :href="route('purchase-reports')"
                    :current="request()->routeIs('purchase-reports')" wire:navigate>
                    Laporan Stok Masuk
                </flux:navlist.item>
                <flux:navlist.item icon="document-check" :href="route('sale-reports')"
                    :current="request()->routeIs('sale-reports')" wire:navigate>
                    Laporan Stok Keluar
                </flux:navlist.item>
                <flux:navlist.item icon="document-check" :href="route('purchase-return-reports')"
                    :current="request()->routeIs('purchase-return-reports')" wire:navigate>
                    Laporan Retur Stok Masuk
                </flux:navlist.item>
                <flux:navlist.item icon="document-check" :href="route('sales-return-reports')"
                    :current="request()->routeIs('sales-return-reports')" wire:navigate>
                    Laporan Retur Stok Keluar
                </flux:navlist.item>
            </flux:sidebar.group>
            @endcan


        </flux:sidebar.nav>

        <flux:spacer />



        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full"
                        data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full"
                        data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <x-loading-overlay />

    {{ $slot }}

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('loading-overlay');

            Livewire.hook('message.sent', () => {
                overlay.classList.remove('hidden');
            });

            Livewire.hook('message.processed', () => {
                overlay.classList.add('hidden');
            });
        });
    </script>




    @fluxScripts
</body>



</html>
