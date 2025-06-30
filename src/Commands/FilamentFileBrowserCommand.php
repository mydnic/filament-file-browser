<?php

namespace Mydnic\FilamentFileBrowser\Commands;

use Illuminate\Console\Command;

class FilamentFileBrowserCommand extends Command
{
    public $signature = 'filament-file-browser';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
