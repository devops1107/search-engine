<?php

namespace spark\drivers\Nav;

/**
* Sidebar Menu Renderer
*
* @package spark
*/
class SidebarGenerator
{
    const TYPE_LINK = 'link';

    const TYPE_HEADING = 'heading';

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
     * @return string
     */
    public function renderHtml($structure = null)
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

            // Easy with headings
            if ($menu['type'] === static::TYPE_HEADING) {
                $html .= $this->makeHeading($menu);
                continue;
            }

            $menu['active_var'] = $this->resolveActiveClass($menu);

            if ($menu['type'] === static::TYPE_LINK && !empty($menu['label'])) {
                $html .= $this->makeSingle($menu);
                continue;
            }

            if ($menu['type'] === static::TYPE_PARENT && is_array($menu['children'])) {
                $html .= $this->makeNested($menu, $id);
                continue;
            }
        }

        return $html;
    }

    public function renderTabs($parent, $classes = '')
    {
        $html = '';

        if (is_array($parent)) {
            $children = $parent;
        } else {
            if (!is_array($this->structure[$parent]['children'])) {
                return $html;
            }

            $children = $this->structure[$parent]['children'];
        }


        $html .= '<ul class="nav nav-tabs ' . e_attr($classes) . '">';

        foreach ($children as $id => $menu) {
            $defaults = static::getDefaultLayout();
            $menu = array_merge($defaults, $menu);

            // Not enough permissions
            if (is_string($menu['permission']) && !current_user_can($menu['permission'])) {
                continue;
            }


            // Easy with headings
            if ($menu['type'] === static::TYPE_HEADING) {
                continue;
            }


            $menu['active_var'] = $this->resolveActiveClass($menu);

            if ($menu['type'] === static::TYPE_LINK && !empty($menu['label'])) {
                $html .= $this->makeTab($menu);
                continue;
            }
        }

        $html .= '</ul>';
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

        $collapsed = 'collapsed';
        $aria_expanded = 'false';
        $show = '';


        if (!empty($menu['active_var'])) {
            $collapsed = '';
            $aria_expanded = 'true';
            $show = 'show';
        }

        $html =
        '<li class="sidebar-nav-item has-children children-expanded-'.$aria_expanded.'">
            <a class="sidebar-nav-link '.$menu['active_var'].' ' . $collapsed . '" href="#'. $target .'" data-toggle="collapse" data-target="#'. $target .'"><span class="sidebar-nav-item-icon">' . $menu['icon_html'] .'</span>
            <span class="sidebar-nav-item-label">' . html_escape($menu['label']) . '</span> <span class="sidebar-nav-item-caret">▾</span>
            </a>
        <ul class="sidebar-children list-unstyled flex-column collapse '.$show.'" id="'. $target .'" aria-expanded="'. $aria_expanded .'">
            ';
        $html .= $this->renderHtml($menu['children']);
        $html .= "\n</ul></li>\n";
        return $html;
    }

    /**
     * Handles single menu
     *
     * @param  array  $menu
     * @return string
     */
    protected function makeSingle(array $menu)
    {
        $count = '';

        if ($menu['count'] > 0) {
            $count = '<span class="sidebar-count">' . $menu['count'] .'</span>';
        }

        return
        '<li class="sidebar-nav-item">
            <a data-toggle="tooltip" data-placement="right" title="' . html_escape($menu['label']) . '" class="sidebar-nav-link  ' . html_escape($menu['active_var'], false) . '" href="' . html_escape($menu['url']) . '"><span class="sidebar-nav-item-icon">' . $menu['icon_html'] .'</span>
            <span class="sidebar-nav-item-label">
                ' . html_escape($menu['label']) . '
                </span>

                ' . $count . '
            </a>
        </li>' . "\n";
    }

    public function makeTab(array $menu)
    {
        return
        '<li class="nav-item">
            <a class="nav-link ' . html_escape($menu['active_var'], false) . '" href="' . html_escape($menu['url']) . '">
                ' . html_escape($menu['label']) . '
            </a>
        </li>';
    }

    /**
     * Handles heading menu
     *
     * @param  array  $menu
     * @return string
     */
    protected function makeHeading(array $menu)
    {
        return '<li class="nav-heading">' . html_escape($menu['label']) . '</li>'  . "\n";
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
            'count'      => 0,
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
