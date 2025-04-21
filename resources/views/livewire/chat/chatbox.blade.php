<div class="flex flex-col w-full">
    @if ($conversation)
    <div class="p-4  bg-card flex items-center justify-between pb-3">

        <div class="flex items-center space-x-5">
            @if ($conversation)
                @if ($conversation->isGroup())
                    <flux:avatar.group class="**:ring-zinc-100 dark:**:ring-zinc-800">

                        @foreach ($conversation->participants()->where('user_id', '!=', auth()->id())->get() as $participant)
<flux:avatar 
                        circle 
                        badge badge:color="{{ $participant->is_activce_in_conversation ? 'green' : 'gray' }}"
                        badge:circle circle
                        src="https://unavatar.io/x/calebporzio"
                        />
                        @endforeach

                        <flux:avatar circle> +{{ $conversation->participants()->where('user_id', '!=', auth()->id())->get()->count() - 3 }} </flux:avatar>
                </flux:avatar.group>
            <div>
                <h2 class="font-semibold text-foreground"> {{ $conversation->name }} </h2>
            </div>
                @else
<flux:avatar 
                badge badge:color="{{ $conversation->participants()->where('user_id', '!=', auth()->id())->first()->is_online? 'green': 'gray' }}"
                badge:circle circle
                src="https://unavatar.io/x/calebporzio"
                />

            <div>
                <h2 class="font-semibold text-foreground">{{ $conversation->participants()->where('user_id', '!=', auth()->id())->first()->name }} </h2>
            </div>
            <div>
                <p class="text-sm text-green">{{ $conversation->participants()->where('user_id', '!=', auth()->id())->first()->is_activce_in_conversation == true? 'is active': 'not active' }}  </p>
            </div>
                @endif
            @endif
        </div>

        <flux:dropdown>
            <flux:button icon="ellipsis-vertical"></flux:button>
            <flux:menu>
                <flux:menu.item icon="pencil" x-on:click="$flux.modal('edit-group-modal').show()">
                    Editer le groupe
                </flux:menu.item>
                <flux:menu.item icon="user">View Group Members</flux:menu.item>
                <flux:menu.item icon="plus" >Ajouter Membre</flux:menu.item>
                <flux:menu.item icon="plus" wire:click="archiveConversationt()" >Archiver cette conversation</flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item variant="danger" wire:click="deleteConversationt()" icon="trash">Supprimer</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
    <flux:separator />

    {{-- message area --}}
    <div class="overflow-y-scroll p-4 space-y-4 bg-background h-[calc(100vh-200px)]"
    x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
    >
    <!-- Sent Message -->
    @foreach ($conversation->messages as $msg)
        @if ($msg['sender_id'] == $this->conversation->sender()->first()->id)
<!-- Sent Message -->
        <div class="flex items-start justify-end space-x-2">
            <div class="bg-blue-300 rounded-lg p-3 max-w-md">
                <p class="text-primary-foreground">{{ $msg['body'] }}</p>
                <span class="text-xs text-primary-foreground/80 mt-1 block">{{ \Carbon\Carbon::parse($msg['created_at'])->format('h:i A') }}
                </span>
            </div>
        </div>
        @else
<!-- Received Message -->
        <div class="flex items-start space-x-2">
            <img src="" class="w-8 h-8 rounded-full object-cover" alt="Contact">
            <div class="bg-gray-200 dark:bg-gray-500 rounded-lg p-3 max-w-md">
                <p class="text-foreground">{{ $msg['body'] }}</p>
                <span class="text-xs text-muted-foreground mt-1 block">{{ \Carbon\Carbon::parse($msg['created_at'])->format('h:i A') }}</span>
            </div>
        </div>
         @endif
    @endforeach

                        <!-- Received Message -->
                        <div class="flex items-start space-x-2">
                        <img src="" class="w-8 h-8 rounded-full object-cover" alt="Contact">
                        <div class="bg-gray-200 dark:bg-gray-500 rounded-lg p-3 max-w-md">
                        <p class="text-foreground">Lorem ipsum dolor sit amet consectetur
                        adipisicing.</p>
                        <a href="">
                        <img src="" alt="Image" class="w-32 h-32 rounded-lg">
                        </a>
                        <span class="text-xs text-muted-foreground mt-1 block">il y 12 mins </span>
                        </div>
                        </div>
                        <!-- Typing Indicator -->
                        <div class="flex items-start space-x-2">
                        <img src="" class="w-8 h-8 rounded-full object-cover" alt="Contact">
                        <div class="bg-gray-200 dark:bg-gray-500 rounded-lg p-3 max-w-md">
                        <p class="text-foreground italic">typing ...</p>
                        </div>
                        </div>
                        <livewire:chat.modals.edit-group-modal :conversation="$conversation" :key="$conversation->id">

                        </div>



                <flux:separator />
                <div class="p-4  bg-card">
                <div class="flex items-center space-x-3">

                <flux:button icon="paperclip" class="p-2">
                <input wire:model="files" multiple type="file" class=""/>
                </flux:button>

                <input type="text" placeholder="Typer un message..." wire:model="message"
                wire:keydown.enter="sendMessage()" wire:keydown="startTyping()"
                wire:keydown.debounce.2000ms="stopTyping()"
                class="flex-1 p-2 rounded-lg bg-muted text-foreground focus:outline-none focus:ring-2 dark:border-gray-700 border-gray-200 border focus:ring-primary">
                <flux:button icon="send" class="" wire:click="sendMessage"></flux:button>
                </div>
        @else
                <div class="overflow-y-scroll p-4 space-y-4 bg-background h-[calc(100vh-200px)]"
                x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
                >
                click to start a conversation
                </div>
        @endif
        
</div>
