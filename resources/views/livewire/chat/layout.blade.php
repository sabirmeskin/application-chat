<div class=" flex flex-row">
    <livewire:chat.sidebar  />

<div class="w-full">

    @if($conversation)
    <livewire:chat.chatbox :conversation="$conversation" />
    @else
    <div class="flex items-center justify-center w-full h-full text-gray-500 dark:text-gray-300">
        <p>Sélectionnez une conversation pour commencer à discuter.</p>
    </div>
    @endif
</div>

</div>
