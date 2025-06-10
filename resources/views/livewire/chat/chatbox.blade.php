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
                 {{-- <livewire:Chat.Components.group-status :user="$user" /> --}}
                </flux:tooltip>

                @endforeach

                @if ($conversation->participants->count() > 3)
                <flux:tooltip content="{{ $conversation->participants->count() - 3 }} autres" placement="top">
                    <flux:avatar size="xs" circle>{{ $conversation->participants->count() - 3 }}+</flux:avatar>
                </flux:tooltip>
                @endif

            </flux:avatar.group>

            @else

            <livewire:Chat.Components.status :conversation="$conversation" />

            @endif

            <flux:heading size="lg">{{$conversation->ConversationName()}}</flux:heading>
        </div>

            @if ($conversation->isGroup() && $conversation->ConversationAdmin()->id == Auth::id() )

        <flux:dropdown>
            <flux:button icon="circle-chevron-down" variant="ghost" class="ml-auto mr-2" />
            <flux:menu>
                <flux:menu.item :key="$conversation->id" x-on:click="$flux.modal('edit-group-modal').show()"
                    icon="pencil">Modifier Groupe</flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item wire:click="test" icon="user-x">Quitter la Conversation</flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        @endif

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
                :avatarOn="$index === 0 || (!empty($messages[$index - 1]) && $messages[$index - 1]->sender_id !== $message->sender_id)"
                 :message="$message"
                :wire:key="'message-'.$message->id"
            />
            @else

            @unless($message->deleted_at)
                <livewire:chat.components.message-bubble
                    :avatarOn="$index === 0 || (!empty($messages[$index - 1]) && $messages[$index - 1]->sender_id !== $message->sender_id)"
                    :message="$message"
                    :wire:key="'message-'.$message->id"
                />
            @endunless
            
            @endif

            @endforeach
        </div>

    </div>

    <div x-data="{
            showTyping: $wire.entangle('typingIndicator'),
        }" x-init="
            $watch('showTyping', (val) => {
                if (val) {
                    setTimeout(() => {
                        showTyping = false;
                        $wire.set('typingIndicator', false); // sync with Livewire
                    }, 3000);
                }
            });
        ">
        <template x-if="showTyping">
            <div class="flex items-end space-x-1 p-2 rounded-lg m-2 w-fit ml-20">
                <span
                    class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-lg animate-bounce [animation-delay:0ms]"></span>
                <span
                    class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-lg animate-bounce [animation-delay:200ms]"></span>
                <span
                    class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-lg animate-bounce [animation-delay:400ms]"></span>
            </div>
        </template>
    </div>




    <form wire:submit.prevent="sendMessage" x-on:submit.debounce.500ms
        class="flex w-full items-center justify-center justify-between px-4 py-4 shadow-lg border-t border-zinc-800/5 dark:border-white/10 gap-5">
        {{--
        <flux:button icon="paperclip" variant="primary" class="px-2" /> --}}
        <div x-data="{ triggerFileInput() { $refs.fileInput.click(); } }" class="relative">
            <flux:button icon="paperclip" variant="primary" class="px-2" x-on:click="triggerFileInput()" />
            <input type="file" x-ref="fileInput" wire:model="file" class="hidden"
                accept="image/*,application/pdf,application/msword,.doc,.docx" />
        </div>
        <div class="flex flex-col w-full relative">
            <div class="my-2 w-1/2">

            
                <livewire:chat.components.features.reply  />

            </div>
        <flux:input placeholder="Taper votre message" icon-trailing="send" clearable wire:model="message"
            autocomplete="off"  wire:keyup.debounce.1000ms="startTyping" />
        </div>
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
    <script>
        // When session expires detected (e.g., 419)
window.Echo.leave('user-status'); // Leave the presence channel

fetch('/broadcast-offline', {
  method: 'POST',
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    'Accept': 'application/json',
  },
}).finally(() => {
  window.location.href = '/login';
});

    </script>
    @endscript


    <livewire:chat.modals.edit-group-modal :conversation="$conversation" :key="$conversation->id">
    <livewire:chat.modals.confirm-delete />
    <livewire:chat.modals.edit-message />
    <livewire:chat.modals.copied-message-modal />
    <livewire:chat.modals.forward-message-modal />
    <livewire:chat.modals.reply-message-modal />


    </div>
