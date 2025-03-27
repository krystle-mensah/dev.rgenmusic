<?php
// Attempt to start output buffering with Gzip compression.
// If "ob_gzhandler" is available, it will compress output to reduce bandwidth usage.
if (!ob_start("ob_gzhandler")) {
  // If "ob_gzhandler" is not available or fails, start regular output buffering instead.
  ob_start();
}
//clean the <ul> element in the WordPress navigation menu
// function clean_wp_nav_menu($ul_class)
// {
//   // Remove the ID attribute from the <ul> element using a regular expression
//   $ul_class = preg_replace('/ id="[^"]*"/', '', $ul_class); // Remove ID

//   // Remove the Class attribute from the <ul> element using a regular expression
//   $ul_class = preg_replace('/ class="[^"]*"/', '', $ul_class); // Remove Class

//   // Return the cleaned <ul> element without the ID and Class attributes
//   return $ul_class;
// }
// // Hook the function into the 'wp_nav_menu' filter to modify the menu output
// add_filter('wp_nav_menu', 'clean_wp_nav_menu', 10, 2);
// tell wordpress to automatically generate these theme support features
function rgenmusic_features()
{
  register_nav_menus(array(
    'headerMenuLoggedIn' => 'Header Menu (Logged In)', // Menu for logged-in users
    'headerMenuLoggedOut' => 'Header Menu (Logged Out)', // Menu for logged-out users
  ));
  // Enables support for the title tag, allowing WordPress to manage the document's <title> element dynamically.
  // This is essential for proper SEO and avoids the need to manually set the <title> tag in the theme's header.
  add_theme_support('title-tag');
  // Add theme support for custom logo
  add_theme_support('custom-logo', array(
    'width' => 200, // Customize this to your preferred logo width
    'height' => 100, // Customize this to your preferred logo height
    'flex-width' => true,
    'flex-height' => true,
  ));
}
// Hook into the 'after_setup_theme' action to initialize theme features
add_action('after_setup_theme', 'rgenmusic_features');
// This function tells wordpress to load stylesheets through WordPress' wp_enqueue_style function.
function rgenmusic_scripts()
{
  wp_enqueue_script('main-rgenmusic-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
  // Google Fonts - Bebas Neue and Open Sans, for typography customization.
  wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet');
  // Font Awesome - A library of icons for use throughout the site.
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  // Enqueue custom CSS file from the css folder
  wp_enqueue_style('rgenmusic-custom', get_theme_file_uri('/build/custom.css'));
  // RgenMusic main styles - The primary CSS file generated for the theme.
  wp_enqueue_style('rgenmusic-style', get_theme_file_uri('/build/style-index.css'));
  // normalize.css styles - An additional CSS file for custom or additional styling.
  wp_enqueue_style('rgenmusic-extra-style', get_theme_file_uri('/build/index.css'));
}
// Hook the script enqueue function into 'wp_enqueue_scripts' to ensure proper loading.
add_action('wp_enqueue_scripts', 'rgenmusic_scripts');
//Verify that thumbnails are enabled in your WordPress theme.
add_theme_support('post-thumbnails');
// add_action('pre_get_posts', 'rgenmusic_adjust_queries');
// Define a function named rgenmusic_adjust_queries that modifies WordPress queries
function rgenmusic_adjust_queries($query)
{
  // Get today's date in the format 'Ymd' (e.g., '20250105' for January 5, 2025)
  $today = date('Ymd');
  // Check if:
  // 1. The request is not for the admin area (to avoid modifying backend queries)
  // 2. The request is for the archive of the 'music_release' custom post type
  // 3. This is the main query (to avoid interfering with secondary or custom queries)
  if (!is_admin() and is_post_type_archive('music_release') and $query->is_main_query()) {
    // Set the query to sort posts by the 'music_release_date' meta key
    $query->set('meta_key', 'music_release_date');
    // Order the results based on the value of the 'music_release_date' meta key
    $query->set('orderby', 'meta_value');
    // Set the order of the results to ascending (earliest to latest dates)
    $query->set('order', 'ASC');
    // Add a meta_query parameter to filter the results
    $query->set('meta_query', array(
      array(
        // Specify the meta key to filter by
        'key' => 'music_release_date',
        // Compare the value of the meta key to today's date
        'value' => $today, // Today's date
        // Include only posts where 'music_release_date' is greater than or equal to today's date
        'compare' => '>=',
        // Specify that the meta value should be treated as a date
        'type' => 'DATE'
      )
    ));
  }
}
// Hook the rgenmusic_adjust_queries function to the 'pre_get_posts' action
// This ensures the function is called before WordPress retrieves posts
add_action('pre_get_posts', 'rgenmusic_adjust_queries');
