<div>
    <flux:modal name="contact-modal" variant="flyout">
    <div class="space-y-6 ">
        <div>
            <flux:heading size="lg">Contacts</flux:heading>
            <flux:text class="mt-2">Ajouter une nouvelle conversation</flux:text>
        </div>

        <div class="">
            <div class="flex justify-between  flex-col">
                <flux:spacer />

                <span>
                    <flux:input placeholder="Rechercher des utilisateurs" class="w-full mt-2" icon-trailing="magnifying-glass" clearable   />
                </span>
            </div>
            <flux:separator class="mt-2 mb-4" variant="subtle" />
            <ul class="flex flex-col gap-3 h-[calc(100vh-300px)] overflow-y-scroll">
                @foreach ($contacts as $contact)
                    <li class="flex items-center gap-2 cursor-pointer py-2 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg" wire:click='selectContact({{ $contact->id }})' x-on:click="$flux.modal('contact-modal').close()">
                    <flux:avatar size="sm" name="Caleb Porzio" color="auto" class="ml-3" />
                    <flux:heading>{{$contact->name}}</flux:heading>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="flex">
            <flux:spacer />

            <flux:button type="button" class="cursor-pointer" variant="filled" x-on:click="$flux.modal('contact-modal').close()" >Fermer</flux:button>
        </div>
    </div>
</flux:modal>
</div>
