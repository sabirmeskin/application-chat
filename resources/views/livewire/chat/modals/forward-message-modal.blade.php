<flux:modal name="forward-message-modal" class="min-w-[80rem] max-w-full">
  <div class="space-y-6">
    <div>
      <flux:heading size="lg">Choisissez une cible</flux:heading>
      <flux:input
        placeholder="Rechercher..."
        wire:model.defer="search"
        wire:keyup="updateItems" 
        class="w-full mt-2"
      />
    </div>

    <ul class="max-h-[60vh] overflow-y-auto space-y-2">
      @foreach($items as $item)
        <li
          class="flex items-center justify-between p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded cursor-pointer"
          wire:click="selectItem({{ $item->id }}, '{{ $item->type }}')"
        >
            {{-- @dump($item->type)     --}}
          <div class="flex items-center gap-3">
            @if($item['type'] === 'user')
              <flux:avatar size="sm" name="{{ $item->name }}" color="auto" />
            @else
              <flux:icon icon="users" />
            @endif
            <span class="font-medium">{{ $item->name }}</span>
          </div>
          <flux:button size="sm" variant="ghost">
            @if($item->type !== 'group') Privé @else Groupe @endif
          </flux:button>
        </li>
      @endforeach
    </ul>

    <div class="flex justify-end">
      <flux:button variant="outline" x-on:click="$flux.modal('forward-message-modal').close()">
        Annuler
      </flux:button>
    </div>
  </div>
</flux:modal>
