<nav class="navbar navbar-has-logo navbar-expand-md bg-white navbar-light p-0 fixed-top" id="topnavbar">

  <!-- Toggler/collapsibe Button -->
  <button class="navbar-toggler sidebar-toggler" type="button" id="sidebarToggle">
    <span class="navbar-toggle-icon">
      <span class="icon-bar top-bar"></span>
      <span class="icon-bar middle-bar"></span>
      <span class="icon-bar bottom-bar"></span>
    </span>
  </button>

  <!-- Brand -->
  <a class="navbar-brand sidebar-brand d-sm-flex d-none" href="<?php echo e_attr(url_for('dashboard')); ?>">
    <img src="<?php echo e_attr(site_uri('assets/img/dashboard-logo.png')); ?>" class="dashboard-logo">
  </a>

  <div class="navbar-title text-truncate d-flex h-100 align-items-center">
      <h4 class="p-0 m-0  font-weight-normal"><?php echo $t['page_heading']; ?></h4>
  </div>

  <!-- Toggler/collapsibe Button -->
  <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggle-icon navbar-icon-animate">
      <span class="icon-bar-round top-bar"></span>
      <span class="icon-bar-round middle-bar"></span>
      <span class="icon-bar-round bottom-bar"></span>
    </span>
  </button>

  <!-- Navbar links -->
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav ml-auto">
      <?php echo sp_render_navbar_menu(); ?>

      <li class="nav-item dropdown">
        <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
                    <span class="avatar" style="background-image: url('<?php echo e_attr(current_user_avatar_uri())?>')"></span>
                    <span class="ml-2">
                      <span><?php echo e(current_user_field('full_name')); ?></span>
                      <small class="d-block mt-1"><?php echo e(current_user_field('role_name')); ?></small>
                    </span>
                    <span class="caret"></span>
                  </a>
        <div class="dropdown-menu dropdown-menu-right mb-2 mb-md-0">
          <a class="dropdown-item" href="<?php echo e_attr(base_uri()); ?>" target="_blank"><?php echo __("Visit Site"); ?></a>
          <a class="dropdown-item" href="<?php echo e_attr(url_for('dashboard.account.settings')); ?>">
            <?php echo __("Account Settings"); ?>
        </a>
          <div class="dropdown-divider"></div>
          <form method="post" action="<?php echo e_attr(url_for('auth.logout')); ?>">
            <?php echo $t['csrf_html']; ?>
            <button class="dropdown-item" type="submit"><?php echo __('Log Out'); ?></button>
          </form>
        </div>
      </li>
    </ul>
  </div>
</nav>
