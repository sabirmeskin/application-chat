<flux:modal name="edit-message"  class="min-w-[100rem]">
    <div class="space-y-6">
        {{-- <div>
            <flux:heading size="lg">Supprimer?</flux:heading>
            <flux:text class="mt-2">
                <p>Vous êtes entraîn de supprimer.</p>
                <p>Cette Action est irreversible.</p>
            </flux:text>
        </div> --}}
           <form wire:submit.prevent="edit" x-on:submit.debounce.500ms
        class="flex w-full items-center justify-center justify-between px-4 py-4 shadow-lg border-t border-zinc-800/5 dark:border-white/10 gap-5">
        {{--
        <flux:button icon="paperclip" variant="primary" class="px-2" /> --}}
        <div x-data="{ triggerFileInput() { $refs.fileInput.click(); } }" class="relative">
            <flux:button icon="paperclip" variant="primary" class="px-2" x-on:click="triggerFileInput()" />
            <input type="file" x-ref="fileInput" wire:model="file" class="hidden"
                accept="image/*,application/pdf,application/msword,.doc,.docx" />
        </div>
        <flux:input placeholder="Type your message" icon-trailing="send" clearable wire:model="message"
            autocomplete="off"   />
        <flux:button type="submit" variant="primary">
            modifer
        </flux:button>
    </form>
        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">retour</flux:button>
            </flux:modal.close>
            <flux:button wire:click='modifier' variant="danger">Modifier</flux:button>
        </div>
    </div>
</flux:modal>