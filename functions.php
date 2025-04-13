<?php
// Attempt to start output buffering with Gzip compression.
// If "ob_gzhandler" is available, it will compress output to reduce bandwidth usage.
if (!ob_start("ob_gzhandler")) {
  // If "ob_gzhandler" is not available or fails, start regular output buffering instead.
  ob_start();
}
// tell wordpress to automatically generate these theme support features
function rgenmusic_features()
{
  register_nav_menus(array(
    'headerMenuLoggedIn' => 'Header Menu (Logged In)', // Menu for logged-in users
    'headerMenuLoggedOut' => 'Header Menu (Logged Out)', // Menu for logged-out users
  ));
  add_theme_support('title-tag');
  // Add theme support for custom logo
  add_theme_support('custom-logo', array(
    'width' => 200, // Customize this to your preferred logo width
    'height' => 100, // Customize this to your preferred logo height
    'flex-width' => true,
    'flex-height' => true,
  ));
  add_theme_support('menus');
  add_theme_support('post-thumbnails');

  // Flush rewrite rules on theme switch
  flush_rewrite_rules();
}
// Hook into the 'after_setup_theme' action to initialize theme features
add_action('after_setup_theme', 'rgenmusic_features');

// Hook the script enqueue function into 'wp_enqueue_scripts' to ensure proper loading.
add_action('wp_enqueue_scripts', 'rgenmusic_scripts');
function rgenmusic_scripts()
{
  wp_enqueue_script('main-rgenmusic-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
  // Enqueues a Google Fonts stylesheet for Bebas Neue and Open Sans fonts.
  wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet');

  // Enqueues Font Awesome CSS for adding icons to the login page.
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

  // Enqueues a custom CSS file specific to the Rgenmusic theme (build/custom.css).
  wp_enqueue_style('rgenmusic-custom', get_theme_file_uri('/build/custom.css'));

  // Enqueues the main style file for the Rgenmusic theme (build/style-index.css).
  wp_enqueue_style('rgenmusic-style', get_theme_file_uri('/build/style-index.css'));

  // Enqueues an additional custom stylesheet for extra styles (build/index.css).
  wp_enqueue_style('rgenmusic-extra-style', get_theme_file_uri('/build/index.css'));
}

//Verify that thumbnails are enabled in your WordPress theme.
//add_theme_support('post-thumbnails');
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

// CUSTOMISE LOGIN SCREEN
// This filter changes the URL of the login page logo link.
add_filter('login_headerurl', 'ourHeaderUrl');
// Function that will be called to modify the login header URL.
function ourHeaderUrl()
{
  // Returns the site's home URL (using site_url()) and ensures it is a safe URL using esc_url().
  return esc_url(site_url('/'));
}
// END

// This filter modifies the title attribute of the WordPress login page logo.
add_filter('login_headertitle', 'ourLoginTitle');

// Function that changes the login page logo's title attribute.
function ourLoginTitle()
{
  // Returns the site's name (from WordPress settings) as the title for the login logo.
  return get_bloginfo('name');
}

// Function to add custom rewrite rules
// function custom_rewrite_rules()
// {
//   // Adds a rewrite rule to make "music-releases/" point to the 'music_release' post type archive page
//   add_rewrite_rule('^music-releases/?$', 'index.php?post_type=music_release', 'top');

//   // Add other rewrite rules as needed
// }

// // Hook the function into WordPress initialization
// add_action('init', 'custom_rewrite_rules');
//This ensures that when you activate your theme, the rewrite rules are refreshed.
// function rgenmusic_flush_rewrite_rules()
// {
//   flush_rewrite_rules();
// }
// register_activation_hook(__FILE__, 'rgenmusic_flush_rewrite_rules');
