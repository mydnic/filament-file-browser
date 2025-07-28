<div class="filament-widget">
    <div class="p-2 space-y-4">
        <div class="flex items-center justify-between">
            <x-filament::input.wrapper>
                <x-filament::input.select
                    wire:model="disk"
                    wire:change="changeDisk($event.target.value)"
                >
                    @foreach($this->getAvailableDisks() as $diskName => $diskLabel)
                        <option value="{{ $diskName }}">{{ $diskLabel }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>

            <div class="flex items-center space-x-2">
                <x-filament::button
                    wire:click="navigateUp"
                    icon="heroicon-o-arrow-up"
                    color="secondary"
                    size="sm"
                >
                    Up
                </x-filament::button>

                <form wire:submit.prevent="uploadFiles">
                    <div class="flex items-center space-x-2">
                        <input
                            type="file"
                            wire:model="uploadedFiles"
                            class="hidden"
                            id="file-upload"
                            multiple
                        />
                        <label for="file-upload" class="cursor-pointer">
                            <x-filament::button
                                type="button"
                                icon="heroicon-o-arrow-up-tray"
                                color="primary"
                                size="sm"
                            >
                                Upload
                            </x-filament::button>
                        </label>

                        @if(count($uploadedFiles) > 0)
                            <x-filament::button
                                type="submit"
                                icon="heroicon-o-check"
                                color="success"
                                size="sm"
                            >
                                Save {{ count($uploadedFiles) }} files
                            </x-filament::button>
                        @endif
                    </div>
                </form>

                <div class="flex items-center space-x-2">
                    @if(count($selectedItems) > 0)
                        <x-filament::button
                            wire:click="downloadAsZip"
                            icon="heroicon-o-archive-box-arrow-down"
                            color="success"
                            size="sm"
                        >
                            Download ({{ count($selectedItems) }})
                        </x-filament::button>

                        <x-filament::button
                            wire:click="deleteSelected"
                            icon="heroicon-o-trash"
                            color="danger"
                            size="sm"
                        >
                            Delete ({{ count($selectedItems) }})
                        </x-filament::button>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-2 text-sm">
            <div class="flex items-center space-x-1">
                @foreach($breadcrumbs as $breadcrumb)
                    <button
                        wire:click="navigateToFolder('{{ $breadcrumb['path'] }}')"
                        class="hover:underline text-primary-600"
                    >
                        {{ $breadcrumb['name'] }}
                    </button>

                    @if(!$loop->last)
                        <span>/</span>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Custom file browser table -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
                                <!-- Checkbox column -->
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
                                <!-- Type column -->
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Size
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($files as $file)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input
                                        type="checkbox"
                                        wire:click="toggleSelect('{{ $file['path'] }}')"
                                        @if($this->isSelected($file['path'])) checked @endif
                                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                    >
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($file['type'] === 'dir')
                                        <i class="fas fa-folder text-yellow-500"></i>
                                    @else
                                        <i class="fas fa-file text-blue-500"></i>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if($file['type'] === 'dir')
                                        <button
                                            wire:click="navigateToFolder('{{ $file['path'] }}')"
                                            class="hover:underline text-primary-600"
                                        >
                                            {{ $file['name'] }}
                                        </button>
                                    @else
                                        {{ $file['name'] }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($file['type'] === 'dir')
                                        -
                                    @else
                                        {{ $this->formatBytes($file['size']) }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        @if($file['type'] === 'dir')
                                            <button
                                                wire:click="navigateToFolder('{{ $file['path'] }}')"
                                                class="text-primary-600 hover:text-primary-900"
                                                title="Open"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1H8a3 3 0 00-3 3v1.5a1.5 1.5 0 01-3 0V6z" clip-rule="evenodd" />
                                                    <path d="M6 12a2 2 0 012-2h8a2 2 0 012 2v2a2 2 0 01-2 2H2h2a2 2 0 002-2v-2z" />
                                                </svg>
                                            </button>
                                        @else
                                            <a
                                                href="{{ route('filament-file-browser.download-file', ['disk' => $disk, 'path' => $file['path']]) }}"
                                                target="_blank"
                                                class="text-primary-600 hover:text-primary-900"
                                                title="Download"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </a>

                                            <a
                                                href="{{ Storage::disk($disk)->url($file['path']) }}"
                                                target="_blank"
                                                class="text-primary-600 hover:text-primary-900"
                                                title="Open in new tab"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                                </svg>
                                            </a>
                                        @endif

                                        <button
                                            wire:click="deleteItem('{{ $file['path'] }}')"
                                            class="text-red-600 hover:text-red-900"
                                            title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this item?')"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No files or folders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
