<div class="{{$reply ==false ? 'hidden' :''}}" >
    <div class="box">
        <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded p-2">
            <div class="border-l-4 border-purple-500 pl-2 mr-2">
                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200"> {{$message->sender->name ?? '' }} </span>
                @if ($url)
                <img src="{{$url}}" alt="Thumbnail" class="w-10 h-10 rounded mr-2 ">
                @else
                    <div class="text-gray-700 dark:text-gray-300 text-sm">
                    {{-- {{ Str::limit('Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt, rerum?', 30) }} --}}
                    
                    {{ Str::limit($message->body ?? '' , 30) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
