<?php

namespace spark\drivers\Nav;

use spark\models\MenuModel;
use spark\models\MenuRelModel;

/**
* Theme Menu Renderer
*
* @package spark
*/
class MenuGenerator
{
    /**
     * Menu arguments
     *
     * @var array
     */
    protected $args = [];

    /**
     * Menu identifier
     *
     * @var mixed
     */
    protected $identifier;

    /**
     * Current level of nested menu
     *
     * @var integer
     */
    protected $level = 0;

    /**
     * Information about the menu that is being rendered
     *
     * @var array
     */
    protected $menu;

    /**
     * Constructor
     *
     * @param mixed $identifier Menu location or ID
     * @param array $args
     */
    public function __construct($identifier, array $args = [])
    {
        $this->identifier = $identifier;

        $defaultArgs = [
            'no_container'                => false,
            'show_icons'                  => true,
            'icon_markup'                 => '<span class="menu-svg-icon %2$s">' . svg_icon('%1$s') . '</span>',
            'before_html'                 => '',
            'after_html'                  => '',
            'menu_class'                  => 'navbar-nav',
            'link_class'                  => 'nav-link',
            'li_class'                    => 'nav-item',
            'dropdown_link_class'         => 'sp-link dropdown-item',
            'dropdown_toggle_class'       => 'dropdown-toggle',
            'dropdown_child_toggle_class' => 'dropdown-item dropdown-submenu-toggle',
            'dropdown_li_class'           => 'dropdown',
            'dropdown_li_child_class'     => 'dropdown-submenu',
            'menu_id'                     => null,
            'fallback'                    => null,
            'fallback_text'               => null,
        ];

        $this->args = array_merge($defaultArgs, $args);
    }

    /**
     * Returns a formatted structure of the menu
     *
     * @return array|boolean
     */
    public function getStructure()
    {
        $location = $this->identifier;

        if (is_integer($location)) {
            $menuID = $location;
        } else {
            // Make sure the menu is registered
            if (!has_theme_menu($location)) {
                return false;
            }

            $menuID = (int) get_active_menu_id($location, 0);

            if (!$menuID) {
                return $menuID;
            }
        }

        $menuModel = new MenuModel;
        $this->menu = $menuModel->read($menuID, ['menu_id']);

        if (!$this->menu) {
            return false;
        }

        $app = app();
        $pool = $app->cache;

        $cache = $pool->getItem("parsedMenu/{$menuID}");
        $data = $cache->get();

        // If the cache exists serve from there
        if ($cache->isHit()) {
            return $data;
        }

        $menuRelModel = new MenuRelModel;

        $menuItems = $menuRelModel->select(['*'])
                                  ->where('menu_id', '=', $menuID)
                                  ->orderBy('sort')
                                  ->execute();

        $ref   = [];
        $items = [];

        while ($item = $menuItems->fetch()) {
            $thisRef = &$ref[$item['item_id']];

            $thisRef['parent_id'] = $item['parent_id'];
            $thisRef['item_label'] = __(str_locale_handle('menu-' . $item['item_label']), _T, ['defaultValue' => $item['item_label']]);
            $thisRef['item_url'] = $item['item_url'];
            $thisRef['item_id'] = $item['item_id'];
            $thisRef['item_class'] = $item['item_class'];
            $thisRef['item_icon'] = $item['item_icon'];

            if ((int) $item['parent_id'] === 0) {
                $items[$item['item_id']] = &$thisRef;
            } else {
                $ref[$item['parent_id']]['child'][$item['item_id']] = &$thisRef;
            }
        }


        $cache->set($items);
        $cache->expiresAfter(config('menu_cache_lifetime', 86400));
        $pool->save($cache);

        return $items;
    }

    public function renderMenu()
    {
        $items = $this->getStructure();

        if (!is_array($items)) {
            if (is_callable($this->args['fallback'])) {
                return call_user_func($this->args['fallback']);
            } elseif ($this->args['fallback_text']) {
                return $this->args['fallback_text'];
            }

            return '';
        }

        $id = $this->args['menu_id'] ? $this->args['menu_id'] : "{$this->identifier}-menu";

        $html = $this->args['before_html'];

        if (!$this->args['no_container']) {
            $html  .= '<ul class="' . e_attr($this->args['menu_class']) . '" id="' . e_attr($id) . '">';
        }

        $html .= $this->makeMenu($items);

        if (!$this->args['no_container']) {
            $html  .= '</ul>';
        }
        $html .= $this->args['after_html'];
        return $html;
    }

    protected function getIcon($icon, $parent = false, $insideDropDown = false)
    {
        if (!$this->args['show_icons'] || empty($icon)) {
            return;
        }

        $class = 'menu-icon';

        if ($parent) {
            $class = 'menu-icon-parent';
        }

        if ($insideDropDown) {
            $class = 'menu-icon-dropdown';
        }

        $icon = sprintf($this->args['icon_markup'], $icon, $class);

        return $icon;
    }

    protected function makeMenu($items, $isChild = false, $parent = [])
    {
        $html = '';

        if ($isChild) {
            $class = $this->args['dropdown_li_class'];
            $toggleClass = $this->args['dropdown_toggle_class'];
            $navClass = $this->args['link_class'];

            if ($this->level > 0) {
                $class = $this->args['dropdown_li_child_class'];
                $navClass = '';
                $toggleClass .= ' ' . $this->args['dropdown_child_toggle_class'];
            }

            $parent['item_url'] = $this->formatURL($parent['item_url']);

            $icon = $this->getIcon($parent['item_icon'], true);


            $html .= '<li class="' . $this->args['li_class'] . ' ' . e_attr($class) . '">
            <a class="' . $navClass . ' ' . e_attr($toggleClass) . '" role="button" data-toggle="dropdown" href="' . e_attr($parent['item_url']) . '">' . $icon . '<span class="menu-label">' . e($parent['item_label']) . '
            </span></a><ul class="dropdown-menu">';

            $this->level++;
        }

        foreach ($items as $key => $menu) {
            if (array_key_exists('child', $menu)) {
                $html .= $this->makeMenu($menu['child'], true, $menu);
            } else {
                $html .= $this->makeSingle($menu, $isChild);
            }
        }

        if ($isChild) {
            $html .= "\n</ul></li>\n";
        }

        return $html;
    }

    protected function formatURL($url)
    {
        return ensure_abs_url($url);
    }

    /**
     * Handles single menu
     *
     * @param  array  $menu
     * @return string
     */
    protected function makeSingle(array $menu, $insideDropDown = false)
    {
        $menu['item_url'] = $this->formatURL($menu['item_url']);
        $icon = $this->getIcon($menu['item_icon']);

        if (!$insideDropDown) :
            return
            '<li class="' . e_attr($this->args['li_class']) . '">
            <a class="sp-link ' . e_attr($this->args['link_class']) . ' ' . e_attr($menu['item_class']) . '" href="' . e_attr($menu['item_url']) . '">' . $icon . '
                <span class="menu-label">' . e($menu['item_label']) . '</span>
            </a></li>' . "\n";
        endif;

        $icon = $this->getIcon($menu['item_icon'], false, true);

        return '<a class="' . e_attr($this->args['dropdown_link_class']) . ' ' . e_attr($menu['item_class']) . '" href="' . e_attr($menu['item_url']) . '">' . $icon . '
                <span class="menu-label">' . e($menu['item_label']) . '</span>
                </a>';
    }
}
