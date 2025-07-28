<div class="overflow-hidden">
    @if(count($files) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="w-12 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{-- Select All Checkbox --}}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Size
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($files as $file)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50" wire:key="file-{{ $loop->index }}">
                            {{-- Checkbox --}}
                            <td class="w-12 px-6 py-4 whitespace-nowrap">
                                <input
                                    type="checkbox"
                                    wire:click="toggleSelect('{{ $file['path'] }}')"
                                    @checked(in_array($file['path'], $selectedItems))
                                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                />
                            </td>

                            {{-- Name with Icon --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    @if($file['type'] === 'dir')
                                        <x-heroicon-o-folder class="w-5 h-5 text-blue-500" />
                                    @else
                                        <x-heroicon-o-document class="w-5 h-5 text-gray-400" />
                                    @endif

                                    @if($file['type'] === 'dir')
                                        <button
                                            wire:click="navigateToFolder('{{ $file['path'] }}')"
                                            class="text-primary-600 hover:text-primary-500 font-medium dark:text-primary-400 dark:hover:text-primary-300"
                                        >
                                            {{ $file['name'] }}
                                        </button>
                                    @else
                                        <span class="text-gray-900 dark:text-gray-100">{{ $file['name'] }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($file['type'] === 'dir')
                                    <x-filament::badge color="info">
                                        Folder
                                    </x-filament::badge>
                                @else
                                    <x-filament::badge color="gray">
                                        File
                                    </x-filament::badge>
                                @endif
                            </td>

                            {{-- Size --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($file['type'] === 'file' && isset($file['size']))
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $this->formatFileSize($file['size']) }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    @if($file['type'] === 'file')
{{--                                        <x-filament::icon-button--}}
{{--                                            icon="heroicon-o-arrow-top-right-on-square"--}}
{{--                                            tooltip="Open in new tab"--}}
{{--                                            wire:click="openInNewTab('{{ $file['path'] }}')"--}}
{{--                                            size="sm"--}}
{{--                                        />--}}

{{--                                        <x-filament::icon-button--}}
{{--                                            icon="heroicon-o-arrow-down-tray"--}}
{{--                                            tooltip="Download"--}}
{{--                                            wire:click="downloadFile('{{ $file['path'] }}')"--}}
{{--                                            size="sm"--}}
{{--                                        />--}}
                                    @endif

{{--                                    <x-filament::icon-button--}}
{{--                                        icon="heroicon-o-trash"--}}
{{--                                        tooltip="Delete"--}}
{{--                                        color="danger"--}}
{{--                                        wire:click="deleteFile('{{ $file['path'] }}')"--}}
{{--                                        wire:confirm="Are you sure you want to delete this item?"--}}
{{--                                        size="sm"--}}
{{--                                    />--}}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <x-heroicon-o-folder-open class="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No files or folders</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">This directory is empty.</p>
        </div>
    @endif
</div>
