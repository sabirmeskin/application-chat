<div>
    <flux:modal name="groups-modal" variant="flyout">
    <div class="space-y-6 ">
        <div>
            <flux:heading size="lg">Groupe</flux:heading>
            <flux:text class="mt-2 mb-4">Ajouter un nouveau groupe</flux:text>

            <flux:input type="text" label="Nom du groupe"  wire:model='nom' badge="Required" />

        </div>
        <flux:separator class="mt-2 mb-4" variant="subtle" />

        <div class="">

            <div class="flex justify-between items-center flex-col">

                <flux:spacer />
                <flux:input placeholder="Rechercher des utilisateurs" class="w-full mt-2" icon-trailing="magnifying-glass" clearable wire:model.defer='search' wire:keyup='updateUsers' autocomplete="off" />
                <flux:error name="noUsers" />
            </div>
            <form action="">
            <ul class="flex flex-col gap-3 h-[calc(100vh-400px)] overflow-y-scroll mt-8 ">
                <flux:checkbox.group wire:model="selectedUsers" class="w-full " label="Sélectionner des utilisateurs" >

                    <flux:checkbox.all label="Cocher tous" class=""/>
                    <flux:separator class="mt-2 mb-4" variant="subtle" />


                @foreach ($contacts as $contact)
                    <li class="flex items-center gap-3 cursor-pointer py-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg"
                    x-on:click="" wire:loading.class="opacity-30">
                    <flux:avatar size="sm" name="{{$contact->name}}" color="auto" class="ml-2" />
                    <flux:heading>{{$contact->name}}</flux:heading>
                    <flux:checkbox value="{{ $contact->id }}" wire:key='{{ $contact->id }}' wire:model="selectedUsers"  class="ml-auto mr-2"/>
                </li>
                @endforeach



                </flux:checkbox.group>
            </ul>
        </div>
        <flux:separator class="mt-2 mb-4" variant="subtle" />

        <div class="flex flex-row space-x-4">
            <flux:button type="button" class="cursor-pointer" variant="primary" wire:click="createConversation"   >Confirmer</flux:button>

            <flux:button type="button" class="cursor-pointer" variant="filled" x-on:click="$flux.modal('groups-modal').close()" >Fermer</flux:button>
        </div>
    </form>
    </div>
</flux:modal>
@script
<script>
    $wire.on('closeModal', () => {
        $flux.modal('groups-modal').close();
    });
</script>
@endscript
</div>
