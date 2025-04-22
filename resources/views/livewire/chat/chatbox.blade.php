<div class="h-screen w-full flex flex-col" >
    @if ($conversation)
    <flux:header class="flex w-full items-center justify-between px-4 py-4   shadow-lg  m-0 sticky border-b border-zinc-800/5 dark:border-white/10">
    <div class="

    flex
    items-center
    justify-between
    gap-5
    ">
        {{-- <flux:avatar name="{{ $conversation->ConversationName() }}" color="auto" class="ml-2" /> --}}
            <flux:avatar 
                         circle 
                        badge badge:color="{{ $conversation->participants()->where('user_id', '!=', auth()->id())->first()->is_online ? 'green' : 'gray' }}"
                        badge:circle circle
                        src="https://unavatar.io/x/calebporzio"
                        />
        <flux:heading size="lg">{{$conversation->ConversationName()}}</flux:heading>
    </div>
    <flux:dropdown>
        <flux:button icon="circle-chevron-down" variant="ghost" class="ml-auto mr-2"  />
        <flux:menu>
            <flux:menu.item icon="plus">Ajouter Membre</flux:menu.item>
            <flux:menu.separator />
            <flux:menu.item icon="pencil">Modifier</flux:menu.item>

            <flux:menu.separator />
            <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
        </flux:menu>
    </flux:dropdown>

</flux:header>
<div class="overflow-y-scroll p-4 space-y-4 bg-background h-[calc(100vh-200px)]">
    @foreach ($messages as $msg)
    {{-- @dd($msg) --}}
        @if ($msg['sender_id'] == $sender_id)
        <!-- Sent Message -->
        <div class="flex items-start justify-end space-x-2">
            <flux:avatar  circle badge badge:color='green' badge:circle circle src="https://unavatar.io/x/calebporzio"/>
            <div class="bg-blue-300 rounded-lg p-3 max-w-md">
                <p class="text-primary-foreground">{{ $msg['body'] }}</p>
                <span class="text-xs text-primary-foreground/80 mt-1 block">{{
                    \Carbon\Carbon::parse($msg['created_at'])->format('h:i A') }}
                </span>
            </div>
        </div>
        @else
        <!-- Received Message -->
        <div class="flex items-start space-x-2">
            <flux:avatar  circle badge badge:color='green' badge:circle circle src="https://unavatar.io/x/calebporzio"/>
            <div class="bg-gray-200 dark:bg-gray-500 rounded-lg p-3 max-w-md">
                <p class="text-foreground">{{ $msg['body'] }}</p>
                <span class="text-xs text-muted-foreground mt-1 block">{{
                    \Carbon\Carbon::parse($msg['created_at'])->format('h:i A') }}</span>
            </div>
        </div>
        @endif
        @endforeach
        <!-- Typing Indicator -->
        <div 
             class="flex items-start space-x-2">
            <img src="" class="w-8 h-8 rounded-full object-cover" alt="Contact">
            <div class="bg-gray-200 dark:bg-gray-500 rounded-lg p-3 max-w-md">
                <p class="text-foreground italic" >  </p>
            </div>
        </div>
</div>
<flux:header class="flex w-full items-center justify-center justify-between px-4 py-4 shadow-lg border-t border-zinc-800/5 dark:border-white/10 gap-5">
        <flux:button icon="paperclip" variant="primary"  class="px-2" />
        <flux:input placeholder="Type your message"  icon-trailing="send" clearable wire:model.defer='message' wire:keyup.enter='sendMessage' autocomplete="off"  />
        <flux:button  variant="primary"  wire:click="sendMessage" > Envoyer </flux:button>

</flux:header>

    @else
    <div class="flex items-center justify-center w-full h-full text-gray-500 dark:text-gray-300">
        <p>Sélectionnez une conversation pour commencer à discuter.</p>
    </div>
    @endif
    

</div>

