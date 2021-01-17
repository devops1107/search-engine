<?php

use spark\controllers\Site\SiteController;

/**
 * Route for the homepage
 */
$app->any('/', CONTROLLER_NAMESPACE . 'Site\\SiteController:home')
    ->name('site.home');

/**
 * Route for the rss feeds
 */
$app->get('/feed/rss', CONTROLLER_NAMESPACE . 'Site\\RssController:feed')
    ->name('site.rss');

/**
 * Route for site pages
 */
$app->any('/pages/:identifier', CONTROLLER_NAMESPACE . 'Site\\SiteController:page')
    ->name('site.page');

/**
 * Route for preferences
 */
$app->get('/preferences', CONTROLLER_NAMESPACE . 'Site\\SiteController:preferences')
    ->name('site.preferences');

/**
 * Route for preferences
 */
$app->post('/preferencesPOST', CONTROLLER_NAMESPACE . 'Site\\SiteController:preferencesPOST')
    ->setMiddleware('csrf_guard')
    ->name('site.preferences_post');

/**
 * Route for the search page
 */
$app->get('/search', CONTROLLER_NAMESPACE . 'Site\\SiteController:search')->name('site.search');
$app->get('/suggestQueries', CONTROLLER_NAMESPACE . 'Site\\SiteController:suggestQueries')->name('site.suggest_queries');


/**
 * Route to handle contact form
 */
$app->post('/pageAction/handleContactForm', CONTROLLER_NAMESPACE . 'Site\\SiteController:handleContactForm')
    ->name('site.contact_form_action')
    ->setMiddleware(['csrf_guard', 'honeypot']);


$app->group('/system', function () use ($app) {
    /**
    * Cron Job Tasks
    */
    $app->get('/runtasks', CONTROLLER_NAMESPACE . 'Site\\SiteController:runTasks')
    ->name('tasks');
});


$app->get('/sitemap.xml', CONTROLLER_NAMESPACE . 'Site\\SitemapController:sitemapIndex')
    ->name('sitemap.index');
$app->get('/sitemap-:id.xml', CONTROLLER_NAMESPACE . 'Site\\SitemapController:sitemap')
    ->name('sitemap.list');


/**
 * Authentication Management
 */
$app->group('/auth', function () use ($app) {
    $app->get('/connect/:provider', CONTROLLER_NAMESPACE . 'Site\\AuthController:socialConnect')
        ->name('auth.social_connect');

    $app->get('/callback/:provider', CONTROLLER_NAMESPACE . 'Site\\AuthController:socialConnectCallback')
        ->name('auth.social_callback');

    // Register
    $app->get('/register', CONTROLLER_NAMESPACE . 'Site\\AuthController:register')
        ->name('auth.register');

    // Register POST
    $app->post('/register', CONTROLLER_NAMESPACE . 'Site\\AuthController:registerPOST')
        ->setMiddleware(['csrf_guard', 'honeypot'])
        ->name('auth.register_post');

    // Sign In
    $app->get('/signin', CONTROLLER_NAMESPACE . 'Site\\AuthController:signIn')
        ->name('auth.signin');

    // Sign In POST
    $app->post('/signin', CONTROLLER_NAMESPACE . 'Site\\AuthController:signInPOST')
        ->setMiddleware(['csrf_guard', 'honeypot'])
        ->name('auth.signin_post');

    // Forgot password
    $app->get('/forgotpass', CONTROLLER_NAMESPACE . 'Site\\AuthController:forgotPass')
        ->name('auth.forgotpass');

    // Forgot password POST
    $app->post('/forgotpass', CONTROLLER_NAMESPACE . 'Site\\AuthController:forgotPassPOST')
        ->setMiddleware(['csrf_guard', 'honeypot'])
        ->name('auth.forgotpass_post');

    // Reset password
    $app->get('/resetpass/:token', CONTROLLER_NAMESPACE . 'Site\\AuthController:resetPass')
        ->name('auth.resetpass');

    // Forgot password POST
    $app->post('/resetpass/:token', CONTROLLER_NAMESPACE . 'Site\\AuthController:resetpassPOST')
        ->setMiddleware('csrf_guard')
        ->name('auth.resetpass_post');

    // Email verification
    $app->get('/activation', CONTROLLER_NAMESPACE . 'Site\\AuthController:emailActivation')
        ->name('auth.activation');

    // Email verification request
    $app->post('/activation', CONTROLLER_NAMESPACE . 'Site\\AuthController:emailActivationPOST')
        ->setMiddleware('csrf_guard')
        ->name('auth.activation_post');

    // Email verification action
    $app->get('/verify/:token', CONTROLLER_NAMESPACE . 'Site\\AuthController:emailVerifyAction')
        ->name('auth.verify_action');


    // Sign Out
    $app->post('/logout', CONTROLLER_NAMESPACE . 'Site\\AuthController:logOut')
        ->setMiddleware('csrf_guard')
        ->name('auth.logout');
});


/**
 * Routes for basic ajax calls
 */
$app->group('/ajax', function () use ($app) {
        $app->get('/emailCheck', CONTROLLER_NAMESPACE . 'Site\\AjaxController:emailCheck')
            ->name('ajax.email_check');

        $app->get('/usernameCheck', CONTROLLER_NAMESPACE . 'Site\\AjaxController:usernameCheck')
            ->name('ajax.username_check');
});

/**
 * Route for 404 page
 */
$app->notFound(function () use ($app) {
    return (new SiteController)->notFound();
});
