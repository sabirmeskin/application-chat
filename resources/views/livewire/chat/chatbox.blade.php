<div class="h-screen w-full flex flex-col" wire.loading.class="opacity-50">

    <flux:header
        class="flex w-full items-center justify-between px-4 py-4 shadow-lg m-0 sticky border-b border-zinc-800/5 dark:border-white/10">
        <div class=" ml-5 flex items-center justify-between gap-5">
            @if ($conversation->isGroup())
            <flux:avatar.group class="**:ring-zinc-100 dark:**:ring-zinc-800">
                @foreach ($conversation->participants->take(3) as $user)
                <flux:tooltip content="{{ $user->name }}" placement="top">
                    <flux:avatar circle size="xs" class="max-sm:size-8" name="{{ $user->name }}" color="auto" badge
                        badge:color="{{ $user->is_online ? 'green' : 'gray' }}" badge:circle badge:position="top left"
                        badge:variant="xs" />
                </flux:tooltip>
                @endforeach
                @if ($conversation->participants->count() > 3)
                <flux:tooltip content="{{ $conversation->participants->count() - 3 }} autres" placement="top">
                    <flux:avatar size="xs" circle>{{ $conversation->participants->count() - 3 }}+</flux:avatar>
                </flux:tooltip>
                @endif
            </flux:avatar.group>
            @else
            <flux:avatar badge badge:color="{{ $conversation->receiver()->is_online ? 'green' : 'gray' }}" badge:circle
                badge:position="top left" badge:variant="xs" name="{{ $conversation->ConversationName() }}" color="auto"
                class="ml-2" />
            @endif
            <flux:heading size="lg">{{$conversation->ConversationName()}}</flux:heading>
        </div> @if ($conversation->isGroup() && $conversation->ConversationAdmin()->id == Auth::id() )
        <flux:dropdown>
            <flux:button icon="circle-chevron-down" variant="ghost" class="ml-auto mr-2" />
            <flux:menu>

                <flux:menu.item :key="$conversation->id" x-on:click="$flux.modal('edit-group-modal').show()"
                    icon="pencil">Modifier Groupe</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item icon="user-x">Quitter la Conversation</flux:menu.item>
                    <flux:menu.separator />

                <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
            </flux:menu>
        </flux:dropdown>@endif
    </flux:header>

    <div class="flex flex-col h-full  overflow-y-scroll " id="scrollArea"
        x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)" id="messages-container">
        <div>
            @foreach ($messages as $index => $message)
            @if ($loop->last)
            <div x-data="{ observer: null, messageId : {{ $message->id }} }" x-init="
                        observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    @this.markLastMessageAsSeen(messageId);
                                }
                            });
                        });
                        observer.observe($el);
                    " x-destroy="if (observer) observer.disconnect()">

            </div>
            @endif

            @if ($message->type === 'media')



            <livewire:chat.components.media-message
                :avatarOn="$index === 0 || $messages[$index - 1]->sender_id !== $message->sender_id" :message="$message"
                :key="$message->id . '-' . $message->status" />

            @else

            <livewire:chat.components.message-bubble
                :avatarOn="$index === 0 || $messages[$index - 1]->sender_id !== $message->sender_id" :message="$message"
                :key="$message->id . '-' . $message->status" />

            @endif

            @endforeach
        </div>
        <!-- Typing Indicator -->

    </div>

        <div
        x-data="{
            showTyping: $wire.entangle('typingIndicator'),
        }"
        x-init="
            $watch('showTyping', (val) => {
                if (val) {
                    setTimeout(() => {
                        showTyping = false;
                        $wire.set('typingIndicator', false); // sync with Livewire
                    }, 3000);
                }
            });
        "
    >
        <template x-if="showTyping">
            <div class="flex items-end space-x-1 p-2 rounded-lg m-2 w-fit ml-20">
                <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-lg animate-bounce [animation-delay:0ms]"></span>
                <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-lg animate-bounce [animation-delay:200ms]"></span>
                <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-lg animate-bounce [animation-delay:400ms]"></span>
            </div>
        </template>
        </div>




    <form wire:submit.prevent="sendMessage"  x-on:submit.debounce.500ms

        class="flex w-full items-center justify-center justify-between px-4 py-4 shadow-lg border-t border-zinc-800/5 dark:border-white/10 gap-5">

        <flux:button icon="paperclip" variant="primary" class="px-2" />
        <flux:input placeholder="Type your message" icon-trailing="send" clearable wire:model="message"
            autocomplete="off"
             {{-- wire:keydown.debounce.2000ms="stopTyping" --}}
             {{-- wire:keydown="startTyping" wire:keydown.debounce.3000ms="stopTyping" --}}
             wire:keyup.debounce.1000ms="startTyping"
             />
        <flux:button type="submit" variant="primary">
            Envoyer
        </flux:button>
    </form>

    @script
    <script>
        $wire.on('scrollToBottom', () => {
            const scrollArea = document.querySelector('#scrollArea');
            if (scrollArea) {
                setTimeout(() => {
                    scrollArea.scrollTo({
                    top: scrollArea.scrollHeight,
                    behavior: 'smooth'
                });
                }, 100);
            }
        });
    </script>
    @endscript
    <livewire:chat.modals.edit-group-modal :conversation="$conversation" :key="$conversation->id">
</div>
