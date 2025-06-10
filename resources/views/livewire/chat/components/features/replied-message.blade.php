<div   >
    <div class="box">
       <div class="flex flex-col items-start bg-gray-100 dark:bg-gray-800 rounded p-2">
    <!-- First message (parent) -->
    <div class="border-l-4 border-purple-500 pl-2 mr-2 w-full">
        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
            {{ $message->parent->sender->name }}
        </span>
        <div class="text-gray-700 dark:text-gray-300 text-sm">
            <span>{{ $message->parent->body }}</span>
        </div>
    </div>

    <!-- Current message (child) - Now appears below -->
    <div class="text-gray-700 dark:text-gray-300 text-sm mt-2 pl-4 w-full">
        {{ Str::limit($message->body ?? '', 30) }}
    </div>
</div>
    </div>
</div>