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

function rgenmusic_scripts()
{
  //File versioning: Added filemtime to all CSS and JS enqueues to make sure the browser fetches the latest version when the files change.
  wp_enqueue_script('main-rgenmusic-js', get_theme_file_uri('/build/index.js'), array('jquery'), filemtime(get_theme_file_path('/build/index.js')), true);
  wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('rgenmusic-custom', get_theme_file_uri('/build/custom.css'), array(), filemtime(get_theme_file_path('/build/custom.css')));
  wp_enqueue_style('rgenmusic-style', get_theme_file_uri('/build/style-index.css'), array(), filemtime(get_theme_file_path('/build/style-index.css')));
  wp_enqueue_style('rgenmusic-extra-style', get_theme_file_uri('/build/index.css'), array(), filemtime(get_theme_file_path('/build/index.css')));
}
add_action('wp_enqueue_scripts', 'rgenmusic_scripts');

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

/**
 * Customising Login
 */

// This filter changes the URL of the login page logo link.
// Function that will be called to modify the login header URL.
//function ourHeaderUrl()
//{
// Returns the site's home URL (using site_url()) and ensures it is a safe URL using esc_url().
//return esc_url(site_url('/'));
//}
//add_filter('login_headerurl', 'ourHeaderUrl');

//LOGIN PASSWORD

// Custom function to redirect WordPress password reset links to a custom reset page
function rgen_redirect_password_reset()
{
  // Check if the current request is for the default WordPress password reset handler
  if (
    strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false && // URL contains 'wp-login.php'
    isset($_GET['action']) && $_GET['action'] === 'rp' &&        // Action is 'rp' (reset password)
    isset($_GET['key']) &&                                       // Reset key is present
    isset($_GET['login'])                                        // Login/username is present
  ) {
    // Build the custom reset password URL on the frontend (e.g., /reset-password)
    $reset_url = home_url('/reset-password');

    // Append 'key' and 'login' as query parameters, sanitized for security
    $reset_url = add_query_arg([
      'key' => sanitize_text_field($_GET['key']),
      'login' => sanitize_text_field($_GET['login']),
    ], $reset_url);

    // Redirect the user to the custom reset password page
    wp_redirect($reset_url);
    exit; // Stop further execution to ensure redirect works immediately
  }
}
// Hook this function to 'init' so it runs early during WordPress execution
add_action('init', 'rgen_redirect_password_reset');

// Custom reset password email message
function rgen_custom_password_reset_email($message, $key, $user_login, $user_data)
{
  $reset_url = home_url('/reset-password');
  $reset_url = add_query_arg([
    'key' => $key,
    'login' => rawurlencode($user_login),
  ], $reset_url);

  $message  = "Hi,\n\n";
  $message .= "You requested to reset your password for your account.\n";
  $message .= "Click the link below to reset it:\n\n";
  $message .= $reset_url . "\n\n";
  $message .= "If you didn’t request this, you can ignore this email.";

  return $message;
}
add_filter('retrieve_password_message', 'rgen_custom_password_reset_email', 10, 4);

/**
 * Briefs functions
 */

// Function to notify the post author when their 'brief' post is published
function notify_user_brief_published($new_status, $old_status, $post)
{
  // Exit the function if the post type is not 'brief'
  if ($post->post_type !== 'brief') {
    return;
  }

  // Only proceed if the post is being published (from a non-published state)
  if ($old_status !== 'publish' && $new_status === 'publish') {
    $post_id = $post->ID;

    // Get the ID of the post author
    $author_id = $post->post_author;

    // Retrieve the author's user data
    $user_info = get_userdata($author_id);

    if ($user_info) {
      // Extract the author's email and display name
      $user_email = $user_info->user_email;
      $user_name = $user_info->display_name;

      // Get the post title and permalink
      $brief_title = get_the_title($post_id);
      $brief_link = get_permalink($post_id);

      // Prepare the email content
      $subject = 'Your brief has been published on rgenmusic!';
      $message = "Hi $user_name,\n\nGood news! Your brief titled \"$brief_title\" has been reviewed and published on rgenmusic.com.\n\nYou can view it here: $brief_link\n\nThanks for contributing!";

      // Get the admin email address to BCC in the notification
      $admin_email = get_option('admin_email');

      // Set the email headers (plain text + BCC to admin)
      $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Bcc: ' . $admin_email
      );

      // Send the email notification to the user
      wp_mail($user_email, $subject, $message, $headers);
    }
  }
}

// Hook the function into the post status transition process
add_action('transition_post_status', 'notify_user_brief_published', 10, 3);
