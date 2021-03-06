<?php

namespace ChrisWhite\XkcdSlack\Search\Sources;

class LocalSource implements SourceInterface
{
    /**
     * The path to the directory that holds local comics.
     *
     * @var string
     */
    protected $comicsDirectory;

    /**
     * Instantiates a new LocalSource.
     *
     * @param $comicsDirectory
     */
    public function __construct($comicsDirectory)
    {
        $this->comicsDirectory = $comicsDirectory;
    }

    /**
     * Performs a comic search with the provided search terms.
     *
     * @param $terms
     * @return array|null
     */
    public function search($terms)
    {
        foreach ($this->comics() as $comic) {
            if ($this->isMatch($comic, $terms)) {
                return $comic['num'];
            }
        }

        return null;
    }

    /**
     * Returns true if the comic matches the search terms, false otherwise.
     *
     * @param $comic
     * @param $terms
     * @return bool
     */
    protected function isMatch($comic, $terms)
    {
        $terms = strtolower($terms);

        // Exact comic matches.
        if (strtolower($comic['safe_title']) === $terms) {
            return $comic['num'];
        }

        // Comic ID matches.
        if (ctype_digit($terms) && $comic['num'] == $terms) {
            return $comic['num'];
        }

        return false;
    }

    /**
     * Returns a generator that iterates over all locally stored comics.
     *
     * @return \Generator
     */
    protected function comics()
    {
        $dir = new \DirectoryIterator($this->comicsDirectory);

        foreach ($dir as $file) {
            if ($file->isDir() || $file->isDot()) continue;

            yield json_decode(file_get_contents($file->getPathname()), true);
        }
    }
}