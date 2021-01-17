<?php

namespace spark\drivers\Nav;

/**
* MenuStorage
*
* Used as a container for dashboard menus
*
* @package spark
*/
class MenuStorage
{
    /**
     * Array of sidebar menus
     *
     * @var array
     */
    protected static $sidebarMenus = [];

    /**
     * Array of navbar menus
     *
     * @var array
     */
    protected static $navBarMenus = [];

    /**
     * Add a menu to the navbar
     *
     * @param string $id
     * @param array  $args
     * @param string $insertAfter
     */
    public static function addNavbarMenu($id, array $args, $parent = false, $insertAfter = null)
    {
        if (empty($args['label'])) {
            throw new \InvalidArgumentException("$\args['label'] is required!");
        }

        if (empty($args['active_var'])) {
            $args['active_var'] = $id . '__active';
        }

        // Handle insert within a parent element
        if ($parent && isset(static::$navBarMenus[$parent]['children'])) {
            if ($insertAfter) {
                array_insert_after(static::$navBarMenus[$parent]['children'], $insertAfter, [$id => $args]);
                return true;
            }

            static::$navBarMenus[$parent]['children'][$id] = $args;
            return true;
        }

        // Insert after a specific key
        if ($insertAfter) {
            array_insert_after(static::$navBarMenus, $insertAfter, [$id => $args]);
            return true;
        }

        static::$navBarMenus[$id] = $args;
        return true;
    }

    /**
     * Check if a navbar menu exists by providing a id
     *
     * @param  string  $id
     * @return boolean
     */
    public static function hasNavbarMenu($id)
    {
        return isset(static::$navBarMenus[$id]);
    }

    /**
     * Get all the registered navbar menus
     *
     * @return array
     */
    public static function getNavbarMenus()
    {
        return static::$navBarMenus;
    }

    /**
     * Add a menu to the sidebar
     *
     * @param string $id
     * @param array  $args
     * @param string $insertAfter
     */
    public static function addSidebarMenu($id, array $args, $parent = false, $insertAfter = null)
    {
        if (empty($args['label'])) {
            throw new \InvalidArgumentException("$\args['label'] is required!");
        }

        if (empty($args['active_var'])) {
            $args['active_var'] = $id . '__active';
        }

        // Handle insert within a parent element
        if ($parent && isset(static::$sidebarMenus[$parent]['children'])) {
            if ($insertAfter) {
                array_insert_after(static::$sidebarMenus[$parent]['children'], $insertAfter, [$id => $args]);
                return true;
            }

            static::$sidebarMenus[$parent]['children'][$id] = $args;
            return true;
        }

        // Insert after a specific key
        if ($insertAfter) {
            array_insert_after(static::$sidebarMenus, $insertAfter, [$id => $args]);
            return true;
        }

        static::$sidebarMenus[$id] = $args;
        return true;
    }

    /**
     * Check if a sidebar menu exists by providing a id
     *
     * @param  string  $id
     * @return boolean
     */
    public static function hasSidebarMenu($id)
    {
        return isset(static::$sidebarMenus[$id]);
    }

    /**
     * Get all the registered sidebar menus
     *
     * @return array
     */
    public static function getSidebarMenus()
    {
        return static::$sidebarMenus;
    }
}
