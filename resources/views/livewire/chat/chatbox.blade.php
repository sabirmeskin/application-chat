<div class="h-screen w-full flex flex-col" >
    @if ($conversation)
    <flux:header class="flex w-full items-center justify-between px-4 py-4   shadow-lg  m-0 sticky border-b border-zinc-800/5 dark:border-white/10">
    <div class="

    flex
    items-center
    justify-between
    gap-5
    ">
        @if ($conversation->isGroup())
        <flux:avatar.group class="**:ring-zinc-100 dark:**:ring-zinc-800">
        @foreach ($conversation->participants->take(3) as $user)
        <flux:tooltip content="{{ $user->name }}" placement="top">
            <flux:avatar circle size="xs" class="max-sm:size-8" name="{{ $user->name }}" color="auto" badge badge:color="{{ $user->is_online ? 'green' : 'gray' }}" badge:circle badge:position="top left" badge:variant="xs" />
        </flux:tooltip>
        @endforeach
        @if ($conversation->participants->count() > 3)
        <flux:tooltip content="{{ $conversation->participants->count() - 3 }} autres" placement="top" >
            <flux:avatar size="xs" circle>{{ $conversation->participants->count() - 3 }}+</flux:avatar>
        </flux:tooltip>
        @endif
    </flux:avatar.group>
        @else
        <flux:avatar name="{{ $conversation->ConversationName() }}" color="auto" class="ml-2"  />
        @endif
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

    <div class="flex flex-col h-full overflow-y-scroll">
        @foreach ($messages as $message)
        <livewire:chat.components.message-bubble :message="$message" :key="$message->id" />
        @endforeach
    </div>



<flux:header class="flex w-full items-center justify-center justify-between px-4 py-4 shadow-lg border-t border-zinc-800/5 dark:border-white/10 gap-5">
        <flux:button icon="paperclip" variant="primary"  class="px-2" />
        <flux:input placeholder="Type your message"  icon-trailing="send" clearable wire:model.defer='message' wire:keyup.enter='sendMessage' autocomplete="off"  />
        <flux:button  variant="primary"  wire:click='sendMessage' > Envoyer </flux:button>

</flux:header>

    @else
    <div class="flex items-center justify-center w-full h-full text-gray-500 dark:text-gray-300">
        <p>Sélectionnez une conversation pour commencer à discuter.</p>
    </div>
    @endif

</div>

