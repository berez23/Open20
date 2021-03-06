<?php

/*
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 */

namespace Symfony\Component\Finder\Iterator;

/**
 * CustomFilterIterator filters files by applying anonymous functions.
 *
 * The anonymous function receives a \SplFileInfo and must return false
 * to remove files.
 *
 */
class CustomFilterIterator extends FilterIterator
{
    private $filters = [];

    /**
     * @param \Iterator  $iterator The Iterator to filter
     * @param callable[] $filters  An array of PHP callbacks
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(\Iterator $iterator, array $filters)
    {
        foreach ($filters as $filter) {
            if (!\is_callable($filter)) {
                throw new \InvalidArgumentException('Invalid PHP callback.');
            }
        }
        $this->filters = $filters;

        parent::__construct($iterator);
    }

    /**
     * Filters the iterator values.
     *
     * @return bool true if the value should be kept, false otherwise
     */
    public function accept()
    {
        $fileinfo = $this->current();

        foreach ($this->filters as $filter) {
            if (false === \call_user_func($filter, $fileinfo)) {
                return false;
            }
        }

        return true;
    }
}
