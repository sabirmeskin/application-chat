<flux:modal name="delete-message" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Supprimer?</flux:heading>
            <flux:text class="mt-2">
                <p>Vous êtes entraîn de supprimer.</p>
                <p>Cette Action est irreversible.</p>
            </flux:text>
        </div>
        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">retour</flux:button>
            </flux:modal.close>
            <flux:button wire:click='delete' variant="danger">Supprimer</flux:button>
        </div>
    </div>
</flux:modal>
