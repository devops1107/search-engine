<?php
/**
 * Dashboard Route Groups
 *
 */
$app->group('/dashboard', function () use ($app) {

    $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardController:dashboard')->name('dashboard');
    $app->get('/credits', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardController:credits')->name('dashboard.credits');

    /**
     * Ajax
     */
    $app->group('/ajax', function () use ($app) {

        $app->get('/menuSuggestions', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAjaxController:suggestMenuItems')
            ->name('dashboard.ajax.suggest_menu_items');
    });

    /**
     * Pages
     */
    $app->group('/pages', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:index')
            ->name('dashboard.pages');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:create')
            ->name('dashboard.pages.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:createPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.pages.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:update')
            ->name('dashboard.pages.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:updatePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.pages.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:delete')
            ->name('dashboard.pages.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:deletePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.pages.delete_post');
    });

    /**
     * Gallery
     */
    $app->group('/gallery', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardGalleryController:index')
            ->name('dashboard.gallery');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardGalleryController:createPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.gallery.create_post');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardGalleryController:deletePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.gallery.delete_post');
    });


    /**
     * Roles
     */
    $app->group('/roles', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:index')
            ->name('dashboard.roles');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:create')
            ->name('dashboard.roles.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:createPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.roles.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:update')
            ->name('dashboard.roles.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:updatePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.roles.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:delete')
            ->name('dashboard.roles.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:deletePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.roles.delete_post');
    });
    /**
     * Users
     */
    $app->group('/users', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:index')
            ->name('dashboard.users');

        // List POST
        $app->post('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:indexPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.users_post');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:create')
            ->name('dashboard.users.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:createPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.users.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:update')
            ->name('dashboard.users.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:updatePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.users.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:delete')
            ->name('dashboard.users.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:deletePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.users.delete_post');
    });

    /**
     * Themes
     */
    $app->group('/themes', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:index')
            ->name('dashboard.themes');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:create')
            ->name('dashboard.themes.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:createPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.themes.create_post');

        // Apply
        $app->post('/apply/:theme', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:applyTheme')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.themes.apply');

        // Delete
        $app->get('/delete/:theme', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:delete')
            ->name('dashboard.themes.delete');

        // Delete POST
        $app->post('/delete/:theme', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:deletePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.themes.delete_post');
    });



    /**
     * Settings
     */
    $app->group('/settings', function () use ($app) {
        $app->get('/:type', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardSettingsController:index')
            ->name('dashboard.settings');

        $app->post('/:type', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardSettingsController:indexPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.settings_post');

        $app->get('/theme/:theme', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardSettingsController:themeOptions')
            ->name('dashboard.settings.theme');

        $app->post('/theme/:theme', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardSettingsController:themeOptionsPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.settings.theme_post');
    });

    /**
     * Menus
     */
    $app->group('/menus', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:index')
            ->name('dashboard.menus');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:create')
            ->name('dashboard.menus.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:createPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.menus.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:update')
            ->name('dashboard.menus.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:updatePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.menus.update_post');

        // Add menu item POST
        $app->post('/addMenu/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:addMenuPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.menus.add_menu_post');

        // Add menu item POST
        $app->post('/editMenu/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:editMenuPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.menus.edit_menu_post');

        // Order menu items POST
        $app->post('/orderMenu/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:orderMenuPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.menus.order_menu_post');

        // Delete menu item POST
        $app->post('/deleteMenu/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:deleteMenuPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.menus.delete_menu_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:delete')
            ->name('dashboard.menus.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardMenusController:deletePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.menus.delete_post');
    });

    /**
     * Account
     */
    $app->group('/account', function () use ($app) {
        $app->get('/settings', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:accountSettings')
            ->name('dashboard.account.settings');

        $app->post('/settings', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:accountSettingsPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.account.settings_post');

        // Sign Out
            $app->post('/logout', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:logOut')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.account.logout');
    });



    /**
     * Engines
     */
    $app->group('/engines', function () use ($app) {
        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:index')
            ->name('dashboard.engines');

        // Reorder
        $app->get('/reorder', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:reorder')
            ->name('dashboard.engines.reorder');

        $app->post('/reorder', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:reorderPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.engines.reorder_post');

        $app->post('/setDefaultEngine/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:setDefaultEngine')->setMiddleware('csrf_guard')
            ->name('dashboard.engines.default_engine_post');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:create')
            ->name('dashboard.engines.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:createPOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.engines.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:update')
            ->name('dashboard.engines.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:updatePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.engines.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:delete')
            ->name('dashboard.engines.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:deletePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.engines.delete_post');
    });

    /**
     * Queries
     */
    $app->group('/queries', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardQueriesController:index')
            ->name('dashboard.queries');

        // List POST
        $app->post('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardQueriesController:indexPOST')
            ->name('dashboard.queries_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardQueriesController:delete')
            ->name('dashboard.queries.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardQueriesController:deletePOST')
            ->setMiddleware('csrf_guard')
            ->name('dashboard.queries.delete_post');
    });
});
