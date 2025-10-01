{{-- Language Switcher Component --}}
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" 
            class="flex items-center gap-2 p-2 rounded-md transition-all"
            style="color: var(--text-secondary); min-height: 44px;"
            aria-label="Change language">
        {{-- Current Language Flag/Icon --}}
        <div class="w-5 h-5 rounded-sm overflow-hidden flex items-center justify-center"
             style="background-color: var(--bg-secondary);">
            @switch(app()->getLocale())
                @case('en')
                    <span class="text-xs font-medium">EN</span>
                    @break
                @case('fr')
                    <span class="text-xs font-medium">FR</span>
                    @break
                @case('rw')
                    <span class="text-xs font-medium">RW</span>
                    @break
                @default
                    <span class="text-xs font-medium">EN</span>
            @endswitch
        </div>
        
        {{-- Dropdown Arrow --}}
        <svg class="w-4 h-4 transition-transform duration-200" 
             :class="{ 'rotate-180': open }" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    {{-- Language Dropdown --}}
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-48 card"
         style="z-index: 50;">
        <div class="card-body">
            <div class="space-y-1">
                {{-- English --}}
                <a href="{{ route('language.switch', 'en') }}" 
                   class="flex items-center gap-3 p-2 rounded-md transition-all hover:bg-gray-50 dark:hover:bg-gray-800 {{ app()->getLocale() === 'en' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                   @click="open = false">
                    <div class="w-6 h-6 rounded-sm overflow-hidden flex items-center justify-center"
                         style="background-color: var(--bg-secondary);">
                        <span class="text-xs font-medium">EN</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-body font-medium">English</div>
                        <div class="text-body-small" style="color: var(--text-tertiary);">English</div>
                    </div>
                    @if(app()->getLocale() === 'en')
                        <svg class="w-4 h-4" style="color: var(--color-accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </a>
                
                {{-- French --}}
                <a href="{{ route('language.switch', 'fr') }}" 
                   class="flex items-center gap-3 p-2 rounded-md transition-all hover:bg-gray-50 dark:hover:bg-gray-800 {{ app()->getLocale() === 'fr' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                   @click="open = false">
                    <div class="w-6 h-6 rounded-sm overflow-hidden flex items-center justify-center"
                         style="background-color: var(--bg-secondary);">
                        <span class="text-xs font-medium">FR</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-body font-medium">Français</div>
                        <div class="text-body-small" style="color: var(--text-tertiary);">French</div>
                    </div>
                    @if(app()->getLocale() === 'fr')
                        <svg class="w-4 h-4" style="color: var(--color-accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </a>
                
                {{-- Kinyarwanda --}}
                <a href="{{ route('language.switch', 'rw') }}" 
                   class="flex items-center gap-3 p-2 rounded-md transition-all hover:bg-gray-50 dark:hover:bg-gray-800 {{ app()->getLocale() === 'rw' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                   @click="open = false">
                    <div class="w-6 h-6 rounded-sm overflow-hidden flex items-center justify-center"
                         style="background-color: var(--bg-secondary);">
                        <span class="text-xs font-medium">RW</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-body font-medium">Kinyarwanda</div>
                        <div class="text-body-small" style="color: var(--text-tertiary);">Kinyarwanda</div>
                    </div>
                    @if(app()->getLocale() === 'rw')
                        <svg class="w-4 h-4" style="color: var(--color-accent);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>
