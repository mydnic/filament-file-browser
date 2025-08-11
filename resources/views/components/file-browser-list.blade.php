<div>
    @if(count($files) > 0)
        <div class="space-y-1">
            @foreach($files as $file)
                <div
                    class="flex py-1 px-2 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm space-x-2 items-center relative"
                    wire:key="file-list-{{ $loop->index }}"
                >
                    @php $pathB64 = base64_encode($file['path']); @endphp
{{--                    <div class="flex-shrink-0">--}}
{{--                        <x-filament::input.checkbox--}}
{{--                            wire:click="toggleSelect({{ json_encode($file['path']) }})"--}}
{{--                            :valir="in_array($file['path'], $selectedItems)"--}}
{{--                        />--}}
{{--                    </div>--}}

                    <div class="flex-shrink-0 w-8">
                        @if($file['type'] === 'dir')
                            <x-heroicon-o-folder class="size-8 text-blue-500" />
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
                            <x-heroicon-o-document class="size-8 {{ $iconClass }}" />
                        @endif
                    </div>

                    <div class="grow">
                        @if($file['type'] === 'dir')
                            <button
                                wire:click="navigateToFolderBase64('{{ $pathB64 }}')"
                            >
                                {{ $file['name'] }}
                            </button>
                        @else
                            {{ $file['name'] }}
                        @endif
                    </div>

                    @if($file['type'] === 'file' && isset($file['size']))
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $this->formatFileSize($file['size']) }}
                        </div>
                    @endif

                    <div class="flex space-x-1 w-[90px] shrink-0 items-center justify-end">
                        @if($file['type'] === 'file')
                            <x-filament::icon-button
                                icon="heroicon-o-arrow-top-right-on-square"
                                tooltip="Open in new tab"
                                href="{{ $file['full_url'] }}"
                                tag="a"
                                target="_blank"
                                size="xs"
                                color="gray"
                            />
                        @endif

                        <x-filament::icon-button
                            icon="heroicon-o-trash"
                            tooltip="Delete"
                            color="danger"
                            wire:click="deleteFileBase64('{{ $pathB64 }}')"
                            wire:confirm="Are you sure you want to delete this item?"
                            size="xs"
                        />
                    </div>

{{--                    <div class="p-4 text-center">--}}

{{--                         File Size--}}


{{--                         Type Badge--}}
{{--                        <div class="mb-3">--}}
{{--                            @if($file['type'] === 'dir')--}}
{{--                                <x-filament::badge color="info" size="xs">--}}
{{--                                    Folder--}}
{{--                                </x-filament::badge>--}}
{{--                            @else--}}
{{--                                <x-filament::badge color="gray" size="xs">--}}
{{--                                    {{ strtoupper(pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'File') }}--}}
{{--                                </x-filament::badge>--}}
{{--                            @endif--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                     Action Buttons (visible on hover)--}}
{{--                    <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">--}}

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
