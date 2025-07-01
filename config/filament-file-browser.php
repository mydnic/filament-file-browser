<?php

// config for Mydnic/FilamentFileBrowser
return [
    // The default disk to use when the file browser is first loaded
    'default_disk' => 'public',

    // Navigation settings
    'navigation_group' => 'Files',
    'navigation_sort' => 0,

    // Temporary directory for zip downloads
    'temp_directory' => storage_path('app/temp'),

    // Maximum upload file size in MB
    'max_upload_size' => 10,

    // Allowed file extensions for upload (empty array means all extensions are allowed)
    'allowed_extensions' => [],

    // Disks to show in the file browser (empty array means all disks are shown)
    'disks' => [],
];
