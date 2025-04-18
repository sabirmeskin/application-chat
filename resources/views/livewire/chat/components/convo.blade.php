<div class="">
@if ($conversation->isGroup())
<div class="flex items-center gap-2 sm:gap-4 text-xs m-0">

    <flux:avatar.group class="**:ring-zinc-100 dark:**:ring-zinc-800">
        @foreach ($conversation->participants->take(3) as $user)
            <flux:avatar circle size="xs" class="max-sm:size-8" name="{{ $user->name }}" color="auto" badge badge:color="yellow" badge:circle badge:position="top left" badge:size="xs" />
        @endforeach

        @if ($conversation->participants->count() > 3)
            <flux:avatar size="xs" circle>{{ $conversation->participants->count() - 3 }}+</flux:avatar>
        @endif

    </flux:avatar.group>
    <div class="flex flex-col">
        <flux:heading class="text-sm">{{ Str::limit($conversation->name  ?? 'nom invalide',10) }} </flux:heading>
    </div>
</div>
@else
<div class="flex items-center gap-2 sm:gap-4 text-xs m-0">

    <flux:avatar circle size="md" class="max-sm:size-8" name="red User" color="auto" badge badge:color="green" badge:circle badge: />

    <div class="flex flex-col">
        <flux:heading class="text-sm">{{ Str::limit($conversation->name ?? 'nom invalide',10) }} </flux:heading>
        <flux:text class="max-sm:hidden text-xs text-muted"> {{ Str::limit($conversation->lastMessage->body ?? 'No messages yet', 20) }}</flux:text>
    </div>
    <flux:badge size="xs" color="blue" class="max-sm:hidden ml-auto ">{{optional($conversation->lastMessageTime)->diffForHumans() ?? '...'}}</flux:badge>
</div>
@endif
</div>
