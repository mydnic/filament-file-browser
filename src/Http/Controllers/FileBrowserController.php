<?php

namespace Mydnic\FilamentFileBrowser\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class FileBrowserController extends Controller
{
    /**
     * Download a file from a specific disk
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadFile(Request $request)
    {
        $disk = $request->query('disk');
        $path = $request->query('path');

        if (! $disk || ! $path) {
            abort(404);
        }

        if (! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        return Storage::disk($disk)->download($path);
    }

    /**
     * Download a zip file from the temporary directory
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadZip(string $path)
    {
        $zipPath = storage_path('app/temp/' . $path);

        if (! file_exists($zipPath)) {
            abort(404);
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
