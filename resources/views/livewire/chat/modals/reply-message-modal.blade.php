<flux:modal name="reply-message-modal"  class="min-w-[22rem]">
   

   {{-- <div class="flex flex-col w-full max-w-[320px] leading-1.5 p-4 border-gray-200 bg-gray-100 rounded-e-xl rounded-es-xl dark:bg-gray-700"> --}}
      <div class="flex items-center space-x-2 rtl:space-x-reverse">
         <span class="text-sm font-semibold text-gray-900 dark:text-white">{{$message->sender->name ?? null}}</span>
         <span class="text-sm font-normal text-gray-500 dark:text-gray-400">11:46</span>
      </div>
      {{-- <p class="text-sm font-normal py-2.5 text-gray-900 dark:text-white"> {{$message->body ?? null}} </p> --}}
      <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{$message->body ?? null}}</span>
      {{-- <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Répondre</label> --}}
      <flux:input wire:model="content" label="réponse" type="text" wire:keydown.enter="reply"/>
      <flux:button wire:click='reply' class="m-2" variant="primary">Répondre</flux:button>

   {{-- </div> --}}


</flux:modal>