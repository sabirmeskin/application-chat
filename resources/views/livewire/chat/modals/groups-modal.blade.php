<div>
    <flux:modal name="groups-modal" variant="flyout">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">group</flux:heading>
            <flux:text class="mt-2">Make changes to your personal details.</flux:text>
        </div>
        <flux:input label="Name" placeholder="Your name" />
        <flux:input label="Date of birth" type="date" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Save changes</flux:button>
        </div>
    </div>
</flux:modal>
</div>
