<?php

namespace App;

use File;
use Closure;

class DownloadsManager
{
    private $downloadPath;

    public function __construct($downloadPath)
    {
        $this->downloadPath = $downloadPath;
        $this->archiver = app('zippy');
    }

    public function getDownloadsPath()
    {
        // Lazy create the downloads path if it doesn't exist
        if (!File::exists($this->downloadPath)) {
            File::makeDirectory($this->downloadPath, 0755, true);
        }

        return $this->downloadPath;
    }

    public function removeDownloadsPath()
    {
        File::deleteDirectory($this->downloadPath);
    }

    /**
     * Get all downloaded files
     *
     * @return \Illuminate\Support\Collection
     */
    public function allFiles()
    {
        $files = collect(File::allFiles($this->downloadPath));

        return $files->map(function ($file) {
            return new DownloadedFile($file->getRealPath());
        });
    }

    /**
     * Get all file matches via callback
     *
     * @see https://laravel.com/docs/5.4/collections#method-filter
     *
     * @param Closure $callback called for each file, return true for matches
     * @return \Illuminate\Support\Collection
     */
    public function allMatching(Closure $callback)
    {
        return $this->allFiles()->filter($callback)->values();
    }

    public function firstMatching(Closure $callback)
    {
        return $this->allMatching($callback)->first();
    }
}
