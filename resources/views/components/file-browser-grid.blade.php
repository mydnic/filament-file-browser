<div class="overflow-hidden">
    @if(count($files) > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4 p-4">
            @foreach($files as $file)
                <div
                    class="relative group bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-600 hover:shadow-md transition-all duration-200"
                    wire:key="file-grid-{{ $loop->index }}"
                >
                    {{-- Selection Checkbox --}}
{{--                    <div class="absolute top-2 left-2 z-10">--}}
{{--                        <input--}}
{{--                            type="checkbox"--}}
{{--                            wire:click="toggleSelect({{ json_encode($file['path']) }})"--}}
{{--                            @checked(in_array($file['path'], $selectedItems))--}}
{{--                            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"--}}
{{--                        />--}}
{{--                    </div>--}}

                    {{-- File/Folder Content --}}
                    <div class="p-4 text-center">
                        {{-- Icon --}}
                        <div class="mb-3 flex justify-center">
                            @if($file['type'] === 'dir')
                                <x-heroicon-o-folder class="w-12 h-12 text-blue-500" />
                            @else
                                @php
                                    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                    $iconClass = match($extension) {
                                        'pdf' => 'text-red-500',
                                        'doc', 'docx' => 'text-blue-600',
                                        'xls', 'xlsx' => 'text-green-600',
                                        'ppt', 'pptx' => 'text-orange-500',
                                        'jpg', 'jpeg', 'png', 'gif', 'svg' => 'text-purple-500',
                                        'mp4', 'avi', 'mov' => 'text-pink-500',
                                        'mp3', 'wav', 'flac' => 'text-yellow-500',
                                        'zip', 'rar', '7z' => 'text-gray-600',
                                        default => 'text-gray-400'
                                    };
                                @endphp
                                <x-heroicon-o-document class="w-12 h-12 {{ $iconClass }}" />
                            @endif
                        </div>

                        {{-- Name --}}
                        <div class="mb-2">
                            @if($file['type'] === 'dir')
                                <button
                                    wire:click="navigateToFolder({{ json_encode($file['path']) }})"
                                    class="w-full text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 line-clamp-2 break-words p-2 rounded hover:bg-primary-50 dark:hover:bg-primary-900/20"
                                    title="{{ $file['name'] }}"
                                >
                                    📁 {{ $file['name'] }}
                                </button>
                            @else
                                <span
                                    class="text-sm text-gray-900 dark:text-gray-100 line-clamp-2 break-words block"
                                    title="{{ $file['name'] }}"
                                >
                                    {{ $file['name'] }}
                                </span>
                            @endif
                        </div>

                        {{-- File Size --}}
                        @if($file['type'] === 'file' && isset($file['size']))
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                {{ $this->formatFileSize($file['size']) }}
                            </div>
                        @endif

                        {{-- Type Badge --}}
                        <div class="mb-3">
                            @if($file['type'] === 'dir')
                                <x-filament::badge color="info" size="xs">
                                    Folder
                                </x-filament::badge>
                            @else
                                <x-filament::badge color="gray" size="xs">
                                    {{ strtoupper(pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'File') }}
                                </x-filament::badge>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons (visible on hover) --}}
{{--                    <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">--}}
{{--                        <div class="flex space-x-1">--}}
{{--                            @if($file['type'] === 'file')--}}
{{--                                <x-filament::icon-button--}}
{{--                                    icon="heroicon-o-arrow-top-right-on-square"--}}
{{--                                    tooltip="Open in new tab"--}}
{{--                                    wire:click="openInNewTab({{ json_encode($file['path']) }})"--}}
{{--                                    size="xs"--}}
{{--                                    color="gray"--}}
{{--                                />--}}

{{--                                <x-filament::icon-button--}}
{{--                                    icon="heroicon-o-arrow-down-tray"--}}
{{--                                    tooltip="Download"--}}
{{--                                    wire:click="downloadFile({{ json_encode($file['path']) }})"--}}
{{--                                    size="xs"--}}
{{--                                    color="gray"--}}
{{--                                />--}}
{{--                            @endif--}}

{{--                            <x-filament::icon-button--}}
{{--                                icon="heroicon-o-trash"--}}
{{--                                tooltip="Delete"--}}
{{--                                color="danger"--}}
{{--                                wire:click="deleteFile({{ json_encode($file['path']) }})"--}}
{{--                                wire:confirm="Are you sure you want to delete this item?"--}}
{{--                                size="xs"--}}
{{--                            />--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <x-heroicon-o-folder-open class="w-16 h-16 text-gray-400 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No files or folders</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">This directory is empty.</p>
        </div>
    @endif
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
