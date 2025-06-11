<div
    class="flex flex-col w-full max-w-[320px] leading-1.5 p-4 border-gray-300 dark:border-gray-100  rounded-e-xl rounded-es-xl ">

    <div class="flex items-start rounded-xl p-2 bg-gray-600  dark:bg-gray-400">
        <div class="me-2">
            <span class="flex items-center gap-2 text-sm font-medium  dark:text-white text-white pb-2">
                <flux:icon.file size="8" />
                @php
                $media = $message->getFirstMedia('attachments');
                @endphp
                {{ $media->file_name }}
            </span>
            <span class="flex text-xs font-normal text-white dark:text-white gap-2">

                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="self-center" width="3" height="4"
                    viewBox="0 0 3 4" fill="none">
                    <circle cx="1.5" cy="2" r="1.5" fill="#6B7280" />
                </svg>
                {{ round($media->size / 1024, 2) }} KB
                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="self-center" width="3" height="4"
                    viewBox="0 0 3 4" fill="none">
                    <circle cx="1.5" cy="2" r="1.5" fill="#6B7280" />
                </svg>
                {{ $media->extension }}
            </span>
        </div>
        <div class="inline-flex self-center items-center">
            <a href="{{ $message->getFirstMediaUrl('attachments') }}" download >
                <flux:button icon="download" class="text-white"  variant="ghost" />
            </a>
        </div>
    </div>
</div>
