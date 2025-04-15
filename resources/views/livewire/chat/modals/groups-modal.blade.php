<div>
    <flux:modal name="groups-modal" variant="flyout">
    <div class="space-y-6 ">
        <div>
            <flux:heading size="lg">Groupe</flux:heading>
            <flux:text class="mt-2 mb-4">Ajouter un nouveau groupe</flux:text>

            <flux:input type="text" label="Nom du groupe" required/>
        </div>
        <flux:separator class="mt-2 mb-4" variant="subtle" />

        <div class="">

            <div class="flex justify-between items-center flex-col">

                <flux:spacer />
                <flux:input placeholder="Rechercher des utilisateurs" class="w-full mt-2" icon-trailing="magnifying-glass" clearable  />
            </div>
            <ul class="flex flex-col gap-3 h-[calc(100vh-400px)] overflow-y-scroll">
                <flux:spacer class="mt-2" variant="subtle" />
                <flux:checkbox.group wire:model="selectedUsers" class="w-full">
                    <flux:checkbox.all label="Cocher tous" class=""/>
                    <flux:separator class="mt-2 mb-4" variant="subtle" />

                @for ($i = 0; $i < 22; $i++)

                <li class="flex items-center gap-3 cursor-pointer py-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg"
                    x-on:click="">
                    <flux:avatar size="sm" name="Porzio" color="auto" class="ml-2" />
                    <flux:heading>Caleb Porzio</flux:heading>
                    <flux:checkbox value=""  class="ml-auto mr-2"/>
                </li>

                @endfor
                </flux:checkbox.group>
            </ul>
        </div>
        <flux:separator class="mt-2 mb-4" variant="subtle" />

        <div class="flex flex-row space-x-4">
            <flux:button type="button" class="cursor-pointer" variant="primary"  >Confirmer</flux:button>

            <flux:button type="button" class="cursor-pointer" variant="filled" x-on:click="$flux.modal('contact-modal').close()" >Fermer</flux:button>
        </div>
    </div>
</flux:modal>
</div>
