<?php

namespace spark\drivers\Nav;

/**
* Navbar Menu Renderer
*
* @package spark
*/
class NavbarGenerator
{
    const TYPE_LINK = 'link';

    const TYPE_DIVIDER = 'divider';

    const TYPE_PARENT = 'parent';

    /**
     * Menu structure
     *
     * @var array
     */
    protected $structure;

    /**
     * Constructor
     *
     * @param array $structure
     */
    public function __construct(array $structure)
    {
        $this->structure = $structure;
    }

    /**
     * Render the html
     *
     * @param  array|null $structure
     * @param  boolean    $insideDropDown
     * @return string
     */
    public function renderHtml($structure = null, $insideDropDown = false)
    {
        if (!is_array($structure)) {
            $structure = $this->structure;
        }

        $html = '';

        foreach ($structure as $id => $menu) {
            $defaults = static::getDefaultLayout();
            $menu = array_merge($defaults, $menu);

            // Not enough permissions
            if (is_string($menu['permission']) && !current_user_can($menu['permission'])) {
                continue;
            }

            // Easy with dividers
            if ($menu['type'] === static::TYPE_DIVIDER) {
                $html .= $this->makeDivider($menu, $insideDropDown);
                continue;
            }

            $menu['active_var'] = $this->resolveActiveClass($menu);

            if ($menu['type'] === static::TYPE_LINK && !empty($menu['label'])) {
                $html .= $this->makeSingle($menu, $insideDropDown);
                continue;
            }

            if ($menu['type'] === static::TYPE_PARENT && is_array($menu['children'])) {
                $html .= $this->makeNested($menu, $id);
                continue;
            }
        }

        return $html;
    }

    /**
     * Handles nested menu
     *
     * @param  array  $menu
     * @return string
     */
    protected function makeNested(array $menu, $id)
    {
        $target =  html_escape($id, false) . '__parent';
        $selfActive = '';
        if (!empty($menu['active_var'])) {
            $selfActive = 'active';
        }

        $html =
        '<li class="nav-item dropdown '.$selfActive.'">
            <a class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" href="#">
            <span class="nav-item-icon">' . $menu['icon_html'] .'</span>
            <span class="nav-item-label">' . html_escape($menu['label']) . '</span>
            </a>
        <div class="dropdown-menu">
            ';
        $html .= $this->renderHtml($menu['children'], true);
        $html .= "\n</div></li>\n";
        return $html;
    }

    /**
     * Handles single menu
     *
     * @param  array  $menu
     * @return string
     */
    protected function makeSingle(array $menu, $insideDropDown = false)
    {
        if (!$insideDropDown) :
            return
            '<li class="nav-item  ' . html_escape($menu['active_var'], false) . '">
            <a class="nav-link" href="' . html_escape($menu['url']) . '"><span class="nav-item-icon">' . $menu['icon_html'] .'</span>
            <span class="nav-item-label">
                ' . html_escape($menu['label']) . '
                </span>
            </a>
        </li>' . "\n";
        endif;

        return '<a class="dropdown-item  ' . html_escape($menu['active_var'], false) . '" href="' . html_escape($menu['url']) . '"><span class="nav-item-icon dropdown-icon">' . $menu['icon_html'] .'</span>
            <span class="nav-item-label dropdown-label">
                ' . html_escape($menu['label']) . '
                </span></a>';
    }

    /**
     * Handles menu divider
     *
     * @param  array    $menu
     * @param  boolean  $insideDropDown
     * @return string
     */
    protected function makeDivider(array $menu, $insideDropDown = false)
    {
        if (!$insideDropDown) {
            return;
        }

        return '<div class="dropdown-divider"></div>'  . "\n";
    }

    /**
     * Handles active class
     *
     * @param  array  $menu
     * @return string
     */
    protected function resolveActiveClass(array $menu)
    {
        return app()->view()->get($menu['active_var']);
    }

    /**
     * Returns default layout
     *
     * @static
     * @return array
     */
    public static function getDefaultLayout()
    {
        return [
            'type'       => 'link',
            'label'      => '',
            'url'        => '',
            'icon_html'  => '',
            'active_var' => '',
            'target'     => '',
            'permission' => null,
            'children'   => [],
        ];
    }
}
