<div class="h-screen">
    <flux:sidebar stashable
        class="border-r h-screen border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 w-90">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <div class=" flex items-center space-x-2 rtl:space-x-reverse flex-row" wire:navigate>
            <x-app-logo />

            <flux:dropdown position="bottom" align="end">
                <flux:button size="sm" icon="ellipsis-vertical" variant="ghost" />

                <flux:menu class="w-[220px]">



                    <flux:menu.item icon="user" class="cursor-pointer" x-on:click="$flux.modal('contact-modal').show()">
                        {{ __('Créer une nouvelle conversation') }}
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item icon="users" class="cursor-pointer" x-on:click="$flux.modal('groups-modal').show()">
                        {{ __('Créer Un Groupe') }}</flux:menu.item>


                </flux:menu>
            </flux:dropdown>
        </div>
        <flux:separator />

        <flux:navlist variant="outline" class="overflow-y-auto h-full">
            <flux:navlist.group :heading="__('Conversations Actifs')" class="grid overflow-y-auto overflow-x-hidden"
                style="scrollbar-width: thin;"  >
                @foreach ($conversations as $conversation)
                @if (!$conversation->isArchived())
                <flux:navlist.item class="cursor-pointer"
                    wire:click="toggleActive({{ $conversation->id }})"
                    :current="$activeId == $conversation->id"
                    :key="'convo'.$conversation->id"
                >
                    <livewire:chat.components.convo :conversation="$conversation" :key="$conversation->id"/>
                </flux:navlist.item>
                @endif
                @endforeach
            </flux:navlist.group>
        </flux:navlist>

        <flux:separator />
        <flux:navlist variant="outline" class="overflow-y-auto ">
            <flux:navlist.group expandable :expanded="false" :heading="__('Conversations Archivées')"
                class="grid overflow-y-auto  overflow-x-hidden" style="scrollbar-width: thin;">

                @foreach ($conversations as $conversation)
                @if ($conversation->isArchived())
                <flux:navlist.item class="cursor-pointer" :current="false" >
                    <livewire:chat.components.convo :conversation="$conversation" :key="$conversation->id" />
                </flux:navlist.item>
                @endif

                @endforeach


            </flux:navlist.group>
        </flux:navlist>



        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="start" class="bottom-0 relative mt-auto">
            <flux:profile :name="auth()->user()->name" :initials="strtoupper(auth()->user()->initials())"
                icon-trailing="chevrons-up" />

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
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />


        <flux:dropdown position="top" align="bottom" class="">
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
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <livewire:chat.modals.contacts-modal />
    <livewire:chat.modals.groups-modal />


</div>
