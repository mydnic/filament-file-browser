<?php

namespace Mydnic\FilamentFileBrowser\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mydnic\FilamentFileBrowser\FilamentFileBrowser
 */
class FilamentFileBrowser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Mydnic\FilamentFileBrowser\FilamentFileBrowser::class;
    }
}
