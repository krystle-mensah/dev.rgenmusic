<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <header class="site-header">
    <div class="container">
      <?php
      if (function_exists('the_custom_logo') && has_custom_logo()) { ?>
        <div class="site_logo float-left">
          <?php the_custom_logo(); ?>
        </div>
      <?php } else { ?>
        <h1 class="school-logo-text float-left">
          <a href="<?php echo site_url(); ?>"><strong>Rgen</strong> Music</a>
        </h1>
      <?php } ?>

      <span class="js-search-trigger site-header__search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
      <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
      <div class="site-header__menu group">
        <nav class="main-navigation">
          <ul>
            <?php if (is_user_logged_in()) { ?>
              <!-- If the user is logged in, show the logged-in menu -->
              <?php wp_nav_menu(array(
                'theme_location' => 'headerMenuLoggedIn',  // Menu location registered for logged-in users
                'container' => false,
                //'menu_id'   => false,
                //'menu_class' => '', // Removes class from <ul>
                'items_wrap'     => '%3$s', // Outputs only the menu items without <ul>
              )); ?>
              <!-- Display the Logout button -->
              <a href="<?php echo wp_logout_url();  ?>" class="btn btn--small btn--orange float-left btn--with-photo">
                <span class="site-header__avatar"><?php echo get_avatar(get_current_user_id(), 60); ?></span>
                <span class="btn__text">Log Out</span>
              </a>
            <?php } else { ?>
              <!-- If the user is not logged in, show the logged-out menu -->
              <?php wp_nav_menu(array(
                'theme_location' => 'headerMenuLoggedOut',  // Menu location registered for logged-out users
                'container' => false,
                //'menu_id'   => false, // Set to an empty string to remove the <ul> ID
                //'menu_class' => false, // Removes class from <ul>
                'items_wrap'     => '%3$s', // Outputs only the menu items without <ul>
              )); ?>
              <!-- Display the Login and Sign Up buttons -->
              <a href="<?php echo site_url('index.php/login'); ?>" class="btn btn--small btn--orange float-left push-right">Login</a>
              <a href="<?php echo site_url('index.php/registration'); ?>" class="btn btn--small btn--dark-orange float-left">Sign Up</a>

            <?php } ?>
            <span class="search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
          </ul>
        </nav>
      </div>
    </div>
  </header>
</body>