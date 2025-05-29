<flux:modal name="copied-message-modal">
    <div class="text-center p-4">
        <h2 class="text-lg font-semibold mb-2">Message copié</h2>
        @if($message)
        <p class="text-sm text-gray-600">Le texte {{ $message->body }} a été copié dans le presse-papiers.</p>
        @endif
        <div class="mt-4">
            <flux:button color="primary" wire:click="hide" >OK</flux:button>
        </div>
    </div>
</flux:modal>

