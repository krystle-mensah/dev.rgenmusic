<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" sizes="32x32" />

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <!-- This link helps people using screen readers or keyboard navigation to skip directly to the main content of the page, instead of going through menus or headers first. It's hidden visually but readable by screen readers. -->
  <a class="skip-link screen-reader-text" href="#main-content">Skip to content</a>

  <header class="site-header">
    <div class="container">
      <?php
      if (function_exists('the_custom_logo') && has_custom_logo()) { ?>
        <div class="site-logo float-left">
          <?php the_custom_logo(); ?>
        </div>
      <?php } else { ?>
        <h1 class="school-logo-text float-left">
          <!-- check the below code -->
          <a href="<?php echo esc_url(site_url()); ?>"><strong>Rgen</strong> Music</a>

        </h1>
      <?php } ?>

      <!-- The below code is for mobile search -->
      <!-- <button
        type="button"
        class="js-search-trigger site-header__search-trigger mobile-search-icon"
        aria-label="Open search">
        <i class="fa fa-search" aria-hidden="true"></i>
      </button> -->

      <!-- <span class="js-search-trigger site-header__search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span> -->
      <!-- <button
        type="button"
        class="site-header__menu-trigger"
        aria-label="Open menu">
        <i class="fa fa-bars" aria-hidden="true"></i>
      </button> -->

      <!-- Hamburger -->
      <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>

      <div class="site-header__menu group">
        <?php
        if (is_user_logged_in()) {
          ob_start();
          wp_nav_menu(array(
            'theme_location' => 'headerMenuLoggedIn',
            'container' => 'nav',
            'container_class' => 'main-navigation',
            'menu_class' => 'menu',
          ));
          $menu = ob_get_clean();
          $menu = str_replace('<nav', '<nav role="navigation" aria-label="Main Navigation"', $menu);
          echo $menu;
        ?>
          <a href="<?php echo esc_url(wp_logout_url(site_url('/'))); ?>" class="btn btn--small btn--orange float-left btn--with-photo">
            <span class="btn__text">Log Out</span>
          </a>
        <?php } else {
          ob_start();
          wp_nav_menu(array(
            'theme_location' => 'headerMenuLoggedOut',
            'container' => 'nav',
            'container_class' => 'main-navigation',
            'menu_class' => 'menu',
          ));
          $menu = ob_get_clean();
          $menu = str_replace('<nav', '<nav role="navigation" aria-label="Main Navigation"', $menu);
          echo $menu;
        ?>
          <?php
          /* <a href="<?php echo site_url('index.php/login'); ?>" class="btn btn--small btn--orange float-left push-right">Login</a> */
          ?>

          <a href="<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>" class="btn btn--small btn--orange float-left push-right">Login</a>
          <a href="<?php echo esc_url(get_permalink(get_page_by_path('registration'))); ?>" class="btn btn--small btn--dark-orange float-left">Sign Up</a>

          <?php
          /*   <a href="<?php echo esc_url(site_url('index.php/login')); ?>" class="btn btn--small btn--orange float-left push-right">Login</a>
          <a href="<?php echo esc_url(site_url('index.php/registration')); ?>" class="btn btn--small btn--dark-orange float-left">Sign Up</a>*/
          ?>

        <?php } ?>
        <!-- <span class="search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span> -->

        <!-- desktop search -->
        <!-- <button
          type="button"
          class="search-trigger js-search-trigger desktop-search-icon"
          aria-label="Open search">
          <i class="fa fa-search" aria-hidden="true"></i>
        </button> -->
      </div>
    </div>
  </header>