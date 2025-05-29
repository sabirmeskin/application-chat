<div>
    {{-- Success is as dangerous as failure. --}}
    <flux:avatar circle size="xs" class="max-sm:size-8" name="{{ $user->name }}" color="auto" badge
                    badge:color="{{ $user->is_online ? 'green' : 'gray' }}" badge:circle badge:position="top left"
                    badge:variant="xs" />
</div>
