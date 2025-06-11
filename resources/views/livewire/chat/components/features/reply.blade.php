<div class="w-100">
    <div class="box">
        <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded p-2 justify-between border-l-4 border-purple-300">
            {{-- Left: Name and mime type --}}
            <div class="flex flex-col justify-start flex-1 min-w-0">
                <span class="text-sm font-bold text-gray-800 dark:text-gray-600">{{ $message->sender->name }}</span>
                @if ($message->hasMedia('attachments'))
                    @php
                        $media = $message->getFirstMedia('attachments');
                        $mimeType = $media ? $media->mime_type : null;
                    @endphp
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $mimeType }}</span>
                @else
                    <div class="text-gray-700 dark:text-gray-300 text-sm">
                        {{ Str::limit($message->body, 30) }}
                    </div>
                @endif
            </div>

            {{-- Right: Attachment preview --}}
            @if ($message->hasMedia('attachments') && $media)
                <div class="flex items-center space-x-2 ml-4">
                    @if(Str::startsWith($mimeType, 'image/'))
                        <img src="{{ $media->getUrl() }}" alt="Thumbnail" class="w-10 h-10 rounded">
                    @elseif(Str::startsWith($mimeType, 'application/') || Str::startsWith($mimeType, 'text/'))
                        <div  class="flex items-center space-x-2 ">
                            <flux:icon.file size="8" class="text-gray-500 dark:text-gray-400" />
                            <span class="truncate max-w-xs">
                                {{ $media->file_name }}
                            </span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Flux (close) button --}}
            {{-- <flux:button icon="x" variant="ghost" class="ml-2" wire:click="$dispatch('cancelReply')" ></flux:button> --}}
        </div>
    </div>
</div>
