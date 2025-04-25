<div>
    <flux:modal name="edit-group-modal" variant="flyout">
        <div class="space-y-6">
            <flux:heading size="lg">Modifier le groupe</flux:heading>
            <flux:input type="text" label="Nom du groupe" wire:model='nom' badge="*" />

            <flux:separator class="mt-2 mb-4" variant="subtle" />

            <flux:input placeholder="Rechercher" class="w-full mt-2" icon-trailing="magnifying-glass"
                wire:model.defer='search' wire:keyup='updateUsers' autocomplete="off" />

            <ul class="flex flex-col gap-3 h-[calc(100vh-400px)] overflow-y-scroll mt-4">
                <flux:checkbox.group wire:model="selectedUsers" label="Participants">
                    <flux:checkbox.all label="Tout cocher" />
                    <flux:separator class="my-3" variant="subtle" />

                    @foreach ($contacts as $contact)
                        <li class="flex items-center gap-3 py-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg">
                            <flux:avatar size="sm" name="{{ $contact->name }}" color="auto" class="ml-2"
                                badge badge:color="{{ $contact->is_online ? 'green' : 'gray' }}" badge:circle badge:position="top left" badge:variant="xs"
                            />
                            <flux:heading>{{ $contact->name }}</flux:heading>
                            <flux:checkbox wire:model="selectedUsers" value="{{ $contact->id }}"
                                wire:key="{{ $contact->id }}" class="ml-auto mr-2" />
                        </li>
                    @endforeach
                </flux:checkbox.group>
            </ul>

            <flux:separator class="mt-4" variant="subtle" />

            <div class="flex flex-row space-x-4">
                <flux:button type="button" variant="primary" wire:click="save" >Enregistrer</flux:button>
                <flux:button type="button" variant="filled"
                    x-on:click="$flux.modal('edit-group-modal').close()">Fermer</flux:button>
            </div>
        </div>
    </flux:modal>
@script
<script>
    $wire.on('closeModal', () => {
        $flux.modal('edit-group-modal').close();
    });
</script>
@endscript
</div>
