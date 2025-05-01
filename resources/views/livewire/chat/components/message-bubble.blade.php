<div class="">
    @if ($userId !== $message->sender_id)
    <div class="flex items-start justify-start gap-2 px-4 py-2">

        <flux:avatar name="sqdqs sqdqs" color="auto" class="ml-2 {{ !$avatarOn ? 'opacity-0' : '' }}" circle  />


        <div
            class="felx flex-col  min-w-[100px] max-w-[420px] leading-1.5 px-4 py-2 border-gray-100 bg-gray-100 rounded-e-xl rounded-es-xl dark:bg-gray-800">
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <span class="text-sm font-semibold text-gray-900 dark:text-white {{ !$avatarOn ? 'hidden' : '' }}">{{$message->sender->name}}</span>
            </div>
            <p class="text-sm font-normal py-2.5 text-gray-900 dark:text-white">{{$message->body}}</p>
            <div class="w-full flex space-x-2 justify-end">
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ">{{$message->timestamp()}}</span>
            </div>

        </div>

        <flux:dropdown style="display: flex; align-items: center;" class="my-auto">
            <flux:button icon="ellipsis-vertical" variant="ghost" class="ml-auto mr-2" />
            <flux:menu>
                <flux:menu.item icon="forward">Transférer</flux:menu.item>
                <flux:menu.item icon="pencil">Modifier</flux:menu.item>
                <flux:menu.item icon="reply">Répondre</flux:menu.item>
                <flux:menu.item icon="copy">Copier</flux:menu.item>
                <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>

    @else
    <div class="flex justify-end gap-2 px-4 py-2 ">
        <flux:dropdown style="display: flex; align-items: center;" >
            <flux:button icon="ellipsis-vertical" variant="ghost" class="ml-auto mr-2" />
            <flux:menu>
                <flux:menu.item icon="forward">Transférer</flux:menu.item>
                <flux:menu.item icon="pencil">Modifier</flux:menu.item>
                <flux:menu.item icon="reply">Répondre</flux:menu.item>
                <flux:menu.item icon="copy">Copier</flux:menu.item>
                <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
        <div
            class="felx flex-col min-w-[100px]  max-w-[420px] leading-1.5 p-4 border-gray-200 bg-gray-200 rounded-s-xl rounded-es-xl rounded-br-xl dark:bg-gray-600">
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <span class="text-sm font-semibold text-gray-900 dark:text-white {{ !$avatarOn ? 'hidden' : '' }}">{{$message->sender->name}}</span>
            </div>
            <p class="text-sm font-normal py-2.5 text-gray-900 dark:text-white">{{$message->body}}</p>
            <div class="w-full flex justify-between">
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ">{{$message->timestamp()}}</span>
                @if (!$message->conversation->isGroup())
                  @if ($message->status == 'read')
                <flux:icon icon="check-check" variant="micro" />
                @else
                <flux:icon icon="check" variant="micro" />
                @endif
                @endif


            </div>
        </div>
        <flux:avatar name="sqdqs sqdqs" color="auto" class="ml-2 {{ !$avatarOn ? 'opacity-0' : '' }}" circle  />

    </div>
    @endif





</div>
