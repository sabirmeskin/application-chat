<div>
<flux:modal name="edit-message" variant="flyout">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Transférer un message</flux:heading>
        </div>

        <div class="bg-gray-100 p-3 rounded">
            <p class="text-sm text-gray-700">{{ $message->body ?? '—' }}</p>
        </div>

        <flux:input
            placeholder="Rechercher des utilisateurs"
            class="w-full"
            icon-trailing="magnifying-glass"
            clearable
            wire:model.debounce.300ms="search"
            wire:keyup="updateUsers"
            autocomplete="off"
        />

        <flux:separator />

        <ul class="h-[50vh] overflow-y-auto space-y-2">
            @foreach($contacts as $contact)
                <li
                    class="cursor-pointer p-2 hover:bg-zinc-200 rounded flex items-center gap-2"
                    wire:click="selectContact({{ $contact->id }})"
                >
                    <flux:avatar
                        size="sm"
                        name="{{ $contact->name }}"
                        color="auto"
                        badge
                        badge:color="{{ $contact->is_online ? 'green' : 'gray' }}"
                        badge:circle
                        badge:variant="xs"
                    />
                    <span>{{ $contact->name }}</span>
                </li>
            @endforeach
        </ul>

        <div class="flex justify-end">
            <flux:button
                variant="ghost"
                x-on:click="$flux.modal('edit-message').close()"
            >
                Fermer
            </flux:button>
        </div>
    </div>
</flux:modal>
{{-- <script>
    window.addEventListener('message-transferred', e => {
        alert('Message transféré vers la conversation #' + e.detail.conversationId);
    });
</script> --}}

</div>

