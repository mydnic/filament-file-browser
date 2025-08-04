<x-filament::page>
    {{-- Header with Disk Selector --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{-- File Browser Section --}}
    <div class="space-y-4">
        {{-- Breadcrumb Navigation --}}
        @if(count($breadcrumbs) > 1)
            <x-filament::section>
                <x-slot name="heading">
                    Current Path
                </x-slot>

                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        @foreach($breadcrumbs as $index => $breadcrumb)
                            <li class="inline-flex items-center">
                                @if($index > 0)
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif

                                @if($loop->last)
                                    <span class="text-sm font-medium text-gray-500">{{ $breadcrumb['name'] }}</span>
                                @else
                                    <button
                                        wire:click="navigateToFolder('{{ $breadcrumb['path'] }}')"
                                        class="text-sm font-medium text-primary-600 hover:text-primary-500"
                                    >
                                        {{ $breadcrumb['name'] }}
                                    </button>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </x-filament::section>
        @endif

        {{-- Bulk Actions --}}
        @if(count($selectedItems) > 0)
            <x-filament::section>
                <x-slot name="heading">
                    {{ count($selectedItems) }} item(s) selected
                </x-slot>

                <div class="flex space-x-2">
                    <x-filament::button
                        wire:click="downloadSelected"
                        icon="heroicon-o-arrow-down-tray"
                        color="primary"
                        size="sm"
                    >
                        Download
                    </x-filament::button>

                    <x-filament::button
                        wire:click="deleteSelected"
                        icon="heroicon-o-trash"
                        color="danger"
                        size="sm"
                        wire:confirm="Are you sure you want to delete the selected items?"
                    >
                        Delete
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endif

        {{-- File Browser Grid --}}
        <x-filament::section>
            <x-slot name="heading">
                Files & Folders
            </x-slot>
            @include('filament-file-browser::components.file-browser-list')
        </x-filament::section>
    </div>
</x-filament::page>
