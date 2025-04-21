<div class="h-screen w-full flex flex-col" >
    <flux:header class="flex w-full items-center justify-between px-4 py-4   shadow-lg  m-0 sticky border-b border-zinc-800/5 dark:border-white/10">
        <div class="

        flex
        items-center
        justify-between
        gap-5
        ">
            <flux:avatar name="ca moi" color="auto" class="ml-2" />
            <flux:heading size="lg">name</flux:heading>
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
    <div class=" h-full">
        aloha
    </div>
    <flux:header class="flex w-full items-center justify-center justify-between px-4 py-4 shadow-lg border-t border-zinc-800/5 dark:border-white/10 gap-5">
            <flux:button icon="paperclip" variant="primary"  />
            <flux:input placeholder="Type your message"  icon-trailing="send" clearable wire:model.defer='message' wire:keyup.enter='sendMessage' autocomplete="off" />
            <flux:button  variant="primary"  wire:click='sendMessage' > Envoyer </flux:button>

    </flux:header>

</div>

