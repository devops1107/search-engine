<?php

namespace spark\drivers\Nav;

/**
 * Pagination Driver
 *
 *
 * @package spark
 */

class Pagination
{
    /**
     * Type for first page
     */
    const TYPE_FIRST = 'first';

    /**
     * Type for Last page
     */
    const TYPE_LAST = 'last';

    /**
     * Type for next page
     */
    const TYPE_NEXT = 'next';

    /**
     * Type for previous page
     */
    const TYPE_PREV = 'prev';

    /**
     * Type for current page
     */
    const TYPE_CURRENT = 'current';

    /**
     * Type for numeric
     */
    const TYPE_NUMERIC = 'numeric';

    /**
     * Base integer of first page
     */
    const BASE_PAGE = 1;

    /**
     * Number of total items
     *
     * @var integer
     */
    protected $total_items;

    /**
     * Current page number
     *
     * @var integer
     */
    protected $current_page;

    /**
     * Number of items per page
     *
     * @var integer
     */
    protected $items_per_page;

    /**
     * Depth of numeric links
     *
     * @var integer
     */
    protected $numeric_links_depth = 3;

    protected $line_format = [];

    protected $parsed_pages = 0;

    /**
     * Toggle numeric links
     *
     * @var boolean
     */
    protected $numeric_links = true;

    /**
     * Toggle first..last links
     *
     * @var boolean
     */
    protected $first_last_links = true;

    /**
     * The offset number
     *
     * @var integer
     */
    protected $offset = 0;

    /**
     * URL
     *
     * @var string
     */
    protected $url = '?page=@id@';

    /**
     * Hide if there's only one page
     *
     * @var boolean
     */
    protected $hide_if_one = false;

    /**
     * i18n values for the types
     *
     * @var array
     */
    protected static $i18n = [
        'first' => 'First',
        'last'  => 'Last',
        'next'  => 'Next',
        'prev'  => 'Prev.',

        'numeric'  => [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
            '9' => '9',
            '0' => '0',
        ]
    ];

    /**
     * Create a new pagination instance
     *
     * @param integer  $total_items    Number of total items
     * @param integer  $current_page   Current page number
     * @param integer $items_per_page  Number of items per page
     */
    public function __construct($total_items, $current_page, $items_per_page = 10)
    {
        $this->totalItems($total_items)
        ->currentPage($current_page)
        ->itemsPerPage($items_per_page)
        ->setLineFormat('<li class="@class@">', '</li>', 'active', 'page-item', 'page-link');
    }

    /**
     * Set translation strings
     *
     * @param  array  $i18n
     * @return boolean
     */
    public static function setTranslations(array $i18n)
    {
        static::$i18n = array_merge(static::$i18n, $i18n);
        return true;
    }

    /**
     * Set number of total items
     *
     * @param  integer $items_count The number of items
     * @return object
     */
    public function totalItems($items_count)
    {
        $this->total_items = (int)$items_count;
        return $this;
    }

    /**
     * Set current page
     *
     * @param  integer $page Set current page
     * @return object
     */
    public function currentPage($page)
    {
        $page = (int)$page;

        if ($page <= 0) {
            $page = 1;
        }

        $this->current_page = $page;
        return $this;
    }

    /**
     * Set number of items per page
     *
     * @param  integer $items_count The number of items per page
     * @return object
     */
    public function itemsPerPage($items_count)
    {
        $this->items_per_page = (int)$items_count;

        if ($this->items_per_page <= 0) {
            throw new \LogicException('Items per page must be at least 1');
        }

        return $this;
    }

    /**
     * Set numeric links depth
     *
     * @param  integer $count The numeric links depth
     * @return object
     */
    public function numericDepth($count)
    {
        $count = (int)$count;

        if ($count <= 0) {
            throw new \LogicException('Numeric Links count must be at least 1');
        }

        $this->numeric_links_depth = $count;
    }

    /**
     * Toggle numeric links
     *
     * @param  boolean $state True or false
     * @return object
     */
    public function numericLinks($state = true)
    {
        $this->numeric_links = (bool)$state;
        return $this;
    }

    /**
     * Toggle hide if one
     *
     * @param  boolean $state True or false
     * @return object
     */
    public function hideIfOnlyOne($state = true)
    {
        $this->hide_if_one = (bool)$state;
        return $this;
    }

    /**
     * Toggle first..last links
     *
     * @param  boolean $state True or false
     * @return object
     */
    public function firstLastLinks($state)
    {
        $this->first_last_links = (bool)$state;
        return $this;
    }

    /**
     * Parse the data and generate pagination links object
     *
     * @return object
     */
    public function parse()
    {
        $pagination_data = [];

        if ($this->total_items === 0) {
            return $pagination_data;
        }

        $total_pages = ceil($this->total_items/$this->items_per_page);

        $this->offset = ($this->current_page - 1) * $this->items_per_page;

        // First
        if ($this->first_last_links && $this->current_page - $this->numeric_links_depth > self::BASE_PAGE) {
            $pagination_data[] = ['type' => self::TYPE_FIRST, 'id' => self::BASE_PAGE, 'label' => static::$i18n['first']];
        }

        // Previous
        if ($this->current_page > self::BASE_PAGE) {
            $pagination_data[] = ['type' => self::TYPE_PREV, 'id' => $this->current_page - 1, 'label' => static::$i18n['prev']];
        } else {
            $pagination_data[] = ['type' => self::TYPE_PREV, 'id' => $this->current_page - 1, 'disabled' => true, 'label' => static::$i18n['prev']];
        }

        // Numeric
        if ($this->numeric_links) {
            for ($i = max(self::BASE_PAGE, $this->current_page - $this->numeric_links_depth);
                $i <= min($this->current_page + $this->numeric_links_depth, $total_pages); $i++) {
                // Replace the numbers
                $label = str_ireplace(
                    array_keys(static::$i18n['numeric']),
                    array_values(static::$i18n['numeric']),
                    $i
                );

                $line = ['type' => self::TYPE_NUMERIC, 'id' => $i, 'label' => $label];

                if ($i === $this->current_page) {
                    $line['type'] = self::TYPE_CURRENT;
                }

                $pagination_data[] = $line;
            }
        }
        // Next
        if ($this->current_page < $total_pages) {
            $pagination_data[] = ['type' => self::TYPE_NEXT, 'id' => $this->current_page + 1,  'label' => static::$i18n['next']];
        } else {
            $pagination_data[] = ['type' => self::TYPE_NEXT, 'id' => $this->current_page + 1, 'disabled' => true, 'label' => static::$i18n['next']];
        }

        // Last
        if ($this->first_last_links && $this->current_page < $total_pages - $this->numeric_links_depth) {
            $pagination_data[] = ['type' => self::TYPE_LAST, 'id' => $total_pages, 'label' => static::$i18n['last']];
        }

        $this->parsed_pages = count($pagination_data);

        return $pagination_data;
    }

    public function renderHtml($before = '<ul class="pagination">', $after = '</ul>')
    {
        $pages = $this->parse();
        $html = null;
        if (empty($pages)) {
            return $html;
        }

        if (count($pages) === 1 && $this->hide_if_one) {
            return $html;
        }

        $html .= $before;

        foreach ($pages as $page) {
            $type = $page['type'];
            $class = $this->line_format['idle_class'];
            $label = $page['label'];

            if ($page['type'] === self::TYPE_CURRENT) {
                $class .= ' ' . $this->line_format['active_class'];
            }

            $url = str_ireplace('@id@', $page['id'], $this->url);

            if (isset($page['disabled'])) {
                $class .= ' disabled';
                $url = 'javascript:void(0);';
            }

            $parser = ['@id@' => $page['id'], '@class@' => $class, '@type@' => $type];
            $lineBefore = str_ireplace(array_keys($parser), array_values($parser), $this->line_format['before']);
            $lineAfter = str_ireplace(array_keys($parser), array_values($parser), $this->line_format['after']);

            $html .= $lineBefore . '<a href="' . $url . '" class="' . $this->line_format['hyperlink_class'] . '">'. $label .'</a>'. $lineAfter;
        }

        $html .= $after;
        return $html;
    }

    public function setUrl($url)
    {
        $this->url = e_attr($url);
        return $this;
    }

    public function setLineFormat(
        $before = '<li class="@class@">',
        $after = '</li>',
        $active_class = 'active',
        $idle_class = 'page-item',
        $hyperlink_class = 'page-link'
    ) {
        $this->line_format = [
            'before' => $before,
            'after' => $after,
            'active_class' => e_attr($active_class),
            'idle_class' => e_attr($idle_class),
            'hyperlink_class' => e_attr($hyperlink_class)
        ];
        return $this;
    }

    /**
     * Returns the offset value, self::parse() must be called before calling this
     *
     * @return integer
     */
    public function offset()
    {
        return $this->offset;
    }

    public function getLinksCount()
    {
        return $this->parsed_pages;
    }
}
