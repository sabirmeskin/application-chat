<div class="flex h-full  flex-row gap-3" >
    <div class=" border-r border-gray-300 pr-2 dark:border-gray-700">

            <div  class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </div>

                @livewire('chat.partials.group-modal')

            <flux:separator />
            <flux:spacer />
            <div class="flex flex-row">

                <flux:button>Button</flux:button>
                <flux:button>Button2</flux:button>

            </div>
            <flux:spacer />

            </div>
        </div>
        <flux:separator />
        <flux:navlist class="w-full" class="overflow-y-auto h-[calc(100vh-200px)]">
            <flux:navlist.group heading="Groupes" expandable :expanded="false">
                @foreach ($conversations as $convo)
                @if ($convo->isGroup())
                <flux:navlist.item icon="users"   badge-color="green" >
                    <div class="flex items-center space-x-3 cursor-pointer" wire:click="setConversation({{ $convo->id }})">
                        <div class="flex-1">
                            <h3 class="font-semibold text-foreground">{{$convo->name}}</h3>
                            <p class="text-xs text-muted-foreground truncate font-thin">
                                {{ $convo->lastMessage->body ?? 'No messages yet' }}
                            </p>
                        </div>
                        <span class="text-xs text-muted-foreground">
                            {{ optional($convo->lastMessage)->created_at ?
                            $convo->lastMessage->created_at->diffForHumans() : '' }}
                        </span>

                    </div>
                </flux:navlist.item>
                @endif

                @endforeach

            </flux:navlist.group>
            <flux:navlist.group heading="Contacts" expandable>
                @foreach ($conversations as $convo)
                @if (!$convo->isGroup())
                <flux:navlist.item icon="user" iconDot="success"  badge-color="green" >

                {{$convo->participants->except(auth()->user()->id)->first()->is_online ? 'Online' : 'Offline'}}

                    <div class="flex items-center space-x-3 cursor-pointer" wire:click="setConversation({{ $convo->id }})">
                        <div class="flex-1">
                            <h3 class="font-semibold text-foreground">{{$convo->participants()->where('user_id','!=',auth()->id())->first()->name}}</h3>
                            <p class="text-sm text-muted-foreground truncate font-thin">
                                {{ $convo->lastMessage->body ?? 'No messages yet' }}
                            </p>
                        </div>
                        <span class="text-xs text-muted-foreground font-thin">
                            {{ optional($convo->lastMessage)->created_at ?
                            $convo->lastMessage->created_at->diffForHumans() : '' }}
                        </span>

                    </div>
                </flux:navlist.item>
                @endif
                @endforeach
            </flux:navlist.group>

        </flux:navlist>
        <flux:separator />



        <div class="p-4 ">
            <div class="flex space-x-2.5 flex-wrap space-y-2">



                <flux:button icon="settings" href="{{ route('settings.appearance') }}">

                </flux:button>


                    <flux:button 
                    variant="danger" 
                    icon="log-out" 
                    href="{{ route('logout') }}"
                    wire:click="logout"
                    onclick="event.preventDefault(); 
                            setTimeout(function() { 
                                document.getElementById('logout-form').submit(); 
                            }, 300);"
                >
                </flux:button>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>


</div>
