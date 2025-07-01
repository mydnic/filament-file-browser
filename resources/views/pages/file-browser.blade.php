<x-filament::page>
    <div class="filament-file-browser">
        @if (count($this->getHeaderWidgets()))
            <x-filament-widgets::widgets
                :widgets="$this->getHeaderWidgets()"
                :columns="$this->getHeaderWidgetsColumns()"
                :data="['record' => null]"
            />
        @endif
    </div>
</x-filament::page>
