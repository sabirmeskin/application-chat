<div>
    <flux:avatar circle size="md" class="max-sm:size-8" name="{{ $conversation->receiver()->name }}" color="auto"
            badge badge:color="{{$conversation->receiver()->is_online ? 'green' : 'gray' }}" badge:circle badge: />
            
</div>
