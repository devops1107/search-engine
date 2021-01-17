<?php

// Register global dashboard assets
sp_register_style(
    'dashboard-core-style',
    site_uri('assets/css/bundle.css'),
    [
        'abspath' => sitepath('assets/css/bundle.css')
    ]
);

// Google font
sp_register_style('google-font-dashboard', 'https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;500&display=swap');

// heavyweight ugly ass bootstrap js ugh </3
sp_register_script('bootstrap-bundle-js', site_uri('assets/js/bootstrap.bundle.min.js'));

sp_register_script('sortable-js', site_uri('assets/js/sortable.min.js'));

// Register dashboard's core JS
sp_register_script(
    'dashboard-core-js',
    site_uri('assets/js/spark.js'),
    [
        'abspath' => sitepath('assets/js/spark.js')
    ]
);


// Enqueue Google font css
sp_enqueue_style('google-font-dashboard');

// Core style
sp_enqueue_style('dashboard-core-style');

// jQuery
sp_enqueue_script('jquery', 2);

// Ios popup dialogs
sp_enqueue_script('ios-dialog', 2);

// Bootstrap bundle js
sp_enqueue_script('bootstrap-bundle-js', 2);

// Core dashboard JS
sp_enqueue_script('dashboard-core-js', 2, ['jquery', 'bootstrap-bundle-js']);
sp_enqueue_script('polyfill-io', 2);
