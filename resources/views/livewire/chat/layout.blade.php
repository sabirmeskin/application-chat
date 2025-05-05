<div x-data="{ sidebarOpen: window.innerWidth >= 768 }" class="h-full">
    <div class="flex h-full relative">
        <!-- Sidebar - hidden on small screens (md:), visible on medium+ -->
        <div id="sidebar"
             class="w-90 bg-white dark:bg-gray-800 shadow-md transform transition-transform duration-300 ease-in-out
                    fixed md:relative z-20 h-full overflow-y-auto"
             :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
             @resize.window="sidebarOpen = (window.innerWidth >= 768) ? true : sidebarOpen">
            <livewire:chat.sidebar />
        </div>

        <!-- Main content area - takes full width without extra margin -->
        <div class="flex-1 h-full">
            <!-- Mobile overlay - separate from content -->
            <div
                x-show="sidebarOpen && window.innerWidth < 768"
                @click="sidebarOpen = false"
                class="fixed inset-0 bg-black opacity-25 z-10 md:hidden"
                style="pointer-events: auto;"
            ></div>

            <!-- Toggle button - visible only on small screens -->
            <button
                class="md:hidden fixed top-3 left-0 z-30 p-2 rounded-md hover:scale-110 transition-all ease-in-out duration-200
                 bg-transparent dark:bg-transparent cursor-pointer" :class="{'hidden' : sidebarOpen}"
                @click="sidebarOpen = !sidebarOpen">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>


            <div class="h-screen"  >
                @if($conversation)
                    <livewire:chat.chatbox :conversation="$conversation" :key="$conversation->id" />
                @else
                    <div class="flex items-center justify-center w-full h-full text-gray-500 dark:text-gray-300">
                        <p>Sélectionnez une conversation pour commencer à discuter.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
