<div class="">
@if (false)
<div class="flex items-center gap-2 sm:gap-4 text-xs m-0">

    {{-- <flux:avatar circle size="md" class="max-sm:size-8" name="red User" color="auto" badge badge:color="green" badge:circle /> --}}
    <flux:avatar.group class="**:ring-zinc-100 dark:**:ring-zinc-800">
                    <flux:avatar circle size="xs" src="https://unavatar.io/x/calebporzio" />
                    <flux:avatar circle size="xs" src="https://unavatar.io/github/hugosaintemarie" />
                    <flux:avatar circle size="xs" src="https://unavatar.io/github/joshhanley" />

                    <flux:avatar size="xs" circle>3+</flux:avatar>

                </flux:avatar.group>
    <div class="flex flex-col">
        <flux:heading class="text-sm">Caleb Porzio </flux:heading>
        {{-- <flux:text class="max-sm:hidden text-xs text-muted"> {{ Str::limit('message fjdqfs sdlkfsdqf dlkfjdsqlfjsdlkfjls<jfljdgflsd<jgfls<jgf<lgf<jlk<gdkg<ldsgldsjg<l' ?? 'No messages yet', 20) }}</flux:text> --}}
    </div>
    {{-- <flux:badge size="xs" color="blue" class="max-sm:hidden ml-auto ">Il ya 30 sec</flux:badge> --}}
</div>
@else
<div class="flex items-center gap-2 sm:gap-4 text-xs m-0">

    <flux:avatar circle size="md" class="max-sm:size-8" name="red User" color="auto" badge badge:color="green" badge:circle />

    <div class="flex flex-col">
        <flux:heading class="text-sm">Caleb Porzio </flux:heading>
        <flux:text class="max-sm:hidden text-xs text-muted"> {{ Str::limit('message fjdqfs sdlkfsdqf dlkfjdsqlfjsdlkfjls<jfljdgflsd<jgfls<jgf<lgf<jlk<gdkg<ldsgldsjg<l' ?? 'No messages yet', 20) }}</flux:text>
    </div>
    <flux:badge size="xs" color="blue" class="max-sm:hidden ml-auto ">Il ya 30 sec</flux:badge>
</div>
@endif
</div>
