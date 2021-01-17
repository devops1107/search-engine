<?php

namespace spark\drivers\Nav;

/**
 * Paginates array
 *
 * @package spark
 */
class ArrayPagination
{
    protected $page = 1;
    protected $pages;
    protected $perPage = 10;
    protected $start = 0;
    protected $length;

    /**
     * Get items based on pagination
     *
     * @param  array   $array
     * @param  integer $page
     * @param  integer $perPage
     * @return array
     */
    public function generate(array $array, $page = 1, $perPage = 10)
    {
        if (!empty($perPage)) {
            $this->perPage = $perPage;
        }
        $this->page = $page;

        $this->length = count($array);

        $this->pages = ceil($this->length / $this->perPage);

        if ($this->page > $this->pages) {
            $this->page = $this->pages;
        }

        $this->start  = ceil(($this->page - 1) * $this->perPage);

        return array_slice($array, $this->start, $this->perPage);
    }
}
