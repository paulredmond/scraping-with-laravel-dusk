<?php

namespace App;

use Closure;

/**
 * DownloadedFile represents a single downloaded file
 * @see \App\DownloadsManager
 */
class DownloadedFile extends \SplFileInfo
{
    /**
     * @var \Alchemy\Zippy\Zippy
     */
    private $archiver;

    /**
     * @param string $file The full file path
     * {@inheritdoc}
     */
    public function __construct($file)
    {
        parent::__construct($file);
        $this->archiver = app('zippy');
    }

    /**
     * Extract first file matching a given pattern in an archive
     *
     * Client code passes a closure that will accept an instance of \Alchemy\Zippy\Archive\Member
     * that is used to conditionally find a file. The first file that matches the condition is extracted
     * and returned
     *
     * @param Closure $callback The callback to find the first file that matches a user-defined condition
     * @throws \Exception
     * @return self
     */
    public function extractFirstFileMatching(Closure $callback)
    {
        if (!$this->isArchive()) {
            throw new \Exception(sprintf('The file %s is not a valid archive', $this->getBasename()));
        }

        $archive = $this->archiver->open($this->getRealPath());

        $expectedFile = collect($archive->getMembers())->first($callback);

        if (!$expectedFile) {
            throw new \Exception('The extracted file was not found in the archive');
        }

        return $expectedFile->extract($this->getPath(), true);
    }

    /**
     * Determine if this file is an archive based on the available archiver strategies
     *
     * @return bool
     */
    public function isArchive()
    {
        return collect($this->archiver->getStrategies())->has($this->getExtension());
    }
}
