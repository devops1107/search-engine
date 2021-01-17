<?php

sp_add_sidebar_menu(
    'dashboard',
    [
        'type' => 'link',
        'label' => __('Dashboard'),
        'url' => url_for('dashboard'),
        'icon_html' => svg_icon('graph'),
        'active_var' => 'dashboard__active',
    ]
);


sp_add_sidebar_menu(
    'pages',
    [
        'type' => 'link',
        'label'      => __('Pages'),
        'icon_html'  => svg_icon('map'),
        'active_var' => 'pages__active',
        'permission' => 'manage_pages',
        'url' => url_for('dashboard.pages')
    ]
);


sp_add_sidebar_menu(
    'gallery',
    [
        'type' => 'link',
        'label'      => __('Gallery'),
        'icon_html'  => svg_icon('picture'),
        'active_var' => 'gallery__active',
        'url' => url_for('dashboard.gallery'),
        'permission' => 'access_gallery|manage_gallery'
    ]
);

sp_add_sidebar_menu(
    'users',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.users'),
        'label'      => __('Users'),
        'icon_html'  => svg_icon('people'),
        'active_var' => 'users__active',
        'permission' => 'add_user|edit_user|delete_user',
    ]
);


sp_add_sidebar_menu(
    'roles',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.roles'),
        'label'      => __('Roles'),
        'icon_html'  => svg_icon('lock'),
        'active_var' => 'roles__active',
        'permission' => 'add_role|edit_role|delete_role'
    ]
);

sp_add_sidebar_menu(
    'engines',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.engines'),
        'label'      => __('Engines'),
        'icon_html'  => svg_icon('layers'),
        'active_var' => 'engines__active',
        'permission' => 'manage_engines'
    ]
);

sp_add_sidebar_menu(
    'queries',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.queries'),
        'label'      => __('Queries'),
        'icon_html'  => svg_icon('chart'),
        'active_var' => 'queries__active',
        'permission' => 'manage_queries'
    ]
);


sp_add_sidebar_menu(
    '_customization_heading',
    [
        'type' => 'heading',
        'label' => __('Customization'),
        'permission' => 'manage_themes|manage_menus|change_settings',
    ]
);

sp_add_sidebar_menu(
    'themes',
    [
                'type'       => 'link',
                'icon_html' => svg_icon('equalizer'),
                'url'        => url_for('dashboard.themes'),
                'label'      => __('Themes'),
                'active_var' => 'themes__active',
                'permission' => 'manage_themes'
    ]
);

sp_add_sidebar_menu(
    'menus',
    [
                'type'       => 'link',
                'icon_html' => svg_icon('grid'),
                'url'        => url_for('dashboard.menus'),
                'label'      => __('Menus'),
                'active_var' => 'menus__active',
                'permission' => 'manage_menus'
    ]
);



sp_add_sidebar_menu(
    'settings',
    [
        'type' => 'link',
        'label' => __('Settings'),
        'url' => url_for('dashboard.settings', ['type' => 'general']),
        'icon_html' => svg_icon('settings'),
        'active_var' => 'settings__active',
        'permission' => 'change_settings',
    ]
);


sp_add_sidebar_menu(
    '_misc_heading',
    [
        'type' => 'heading',
        'label' => __('Misc.')
    ]
);

sp_add_sidebar_menu(
    'account',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.account.settings'),
        'label'      => __('Account Settings'),
        'icon_html'  => svg_icon('user'),
        'active_var' => 'account__active',
    ]
);
