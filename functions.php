<?php
/*
|--------------------------------------------------------------------------
| Theme Functions - rgenmusic
|--------------------------------------------------------------------------
| This file sets up theme features, scripts, styles, custom behavior,
| and functional enhancements for the rgenmusic theme.
*/

// -----------------------------------------------------------------------------
// Theme Setup: Register menus, logo, thumbnails, and HTML5 support
// -----------------------------------------------------------------------------
function rgenmusic_features()
{
  register_nav_menus(array(
    'headerMenuLoggedIn'  => 'Header Menu (Logged In)',
    'headerMenuLoggedOut' => 'Header Menu (Logged Out)',
  ));

  add_theme_support('title-tag');

  add_theme_support('custom-logo', array(
    'width'       => 200,
    'height'      => 100,
    'flex-width'  => true,
    'flex-height' => true,
  ));

  add_theme_support('post-thumbnails');

  add_theme_support('html5', array(
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
  ));
}
add_action('after_setup_theme', 'rgenmusic_features');

// -----------------------------------------------------------------------------
// Enqueue Scripts and Styles
// -----------------------------------------------------------------------------
function rgenmusic_scripts()
{
  wp_enqueue_script(
    'main-rgenmusic-js',
    get_theme_file_uri('/build/index.js'),
    array('jquery'),
    filemtime(get_theme_file_path('/build/index.js')),
    true
  );

  wp_enqueue_style(
    'custom-google-fonts',
    'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap'
  );

  wp_enqueue_style(
    'font-awesome',
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'
  );

  wp_enqueue_style(
    'rgenmusic-custom',
    get_theme_file_uri('/build/custom.css'),
    array(),
    filemtime(get_theme_file_path('/build/custom.css'))
  );

  wp_enqueue_style(
    'rgenmusic-style',
    get_theme_file_uri('/build/style-index.css'),
    array(),
    filemtime(get_theme_file_path('/build/style-index.css'))
  );

  wp_enqueue_style(
    'rgenmusic-extra-style',
    get_theme_file_uri('/build/index.css'),
    array(),
    filemtime(get_theme_file_path('/build/index.css'))
  );
}
add_action('wp_enqueue_scripts', 'rgenmusic_scripts');

// -----------------------------------------------------------------------------
// Adjust Archive Queries for Custom Post Type: 'music_release'
// -----------------------------------------------------------------------------
function rgenmusic_adjust_queries($query)
{
  $today = date('Ymd');

  if (!is_admin() and is_post_type_archive('music_release') and $query->is_main_query()) {
    $query->set('meta_key', 'music_release_date');
    $query->set('orderby', 'meta_value');
    $query->set('order', 'ASC');
    $query->set('meta_query', array(
      array(
        'key'     => 'music_release_date',
        'value'   => $today,
        'compare' => '>=',
        'type'    => 'DATE'
      )
    ));
  }
}
add_action('pre_get_posts', 'rgenmusic_adjust_queries');

//LOGIN

// -----------------------------------------------------------------------------
// Custom Password Reset Redirection to Branded Page
// -----------------------------------------------------------------------------
function rgen_redirect_password_reset()
{
  if (
    strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false &&
    isset($_GET['action'], $_GET['key'], $_GET['login']) &&
    $_GET['action'] === 'rp'
  ) {
    if (strpos($_SERVER['REQUEST_URI'], '/reset-password') === false) {
      $key   = sanitize_text_field(wp_unslash($_GET['key']));
      $login = sanitize_text_field(wp_unslash($_GET['login']));

      $reset_url = add_query_arg(
        array(
          'key'   => rawurlencode($key),
          'login' => rawurlencode($login),
        ),
        home_url('/reset-password')
      );

      wp_safe_redirect($reset_url);
      exit;
    }
  }
}
add_action('init', 'rgen_redirect_password_reset');

// -----------------------------------------------------------------------------
// Customize Password Reset Email Message
// -----------------------------------------------------------------------------
function rgen_custom_password_reset_email($message, $key, $user_login, $user_data)
{
  $reset_url = add_query_arg([
    'key'   => $key,
    'login' => rawurlencode($user_login),
  ], home_url('/reset-password'));

  $user_name = !empty($user_data->display_name) ? $user_data->display_name : $user_login;

  $message  = "Hello {$user_name},\n\n";
  $message .= "You requested to reset the password for your account.\n";
  $message .= "To reset your password, please click the link below or copy and paste it into your browser:\n\n";
  $message .= "{$reset_url}\n\n";
  $message .= "If you did not request this password reset, please ignore this email or contact support if you have concerns.\n\n";
  $message .= "Thank you,\n";
  $message .= get_bloginfo('name') . " Team";

  return $message;
}
add_filter('retrieve_password_message', 'rgen_custom_password_reset_email', 10, 4);


//Redirect wp-login.php Requests to Your Custom Login Page

function rgen_redirect_wp_login()
{
  $request_uri = $_SERVER['REQUEST_URI'];

  // Allow AJAX, cron, and REST API requests to go through
  if (
    defined('DOING_AJAX') && DOING_AJAX ||
    defined('DOING_CRON') && DOING_CRON ||
    strpos($request_uri, 'wp-json') !== false
  ) {
    return;
  }

  // Redirect wp-login.php access
  if (strpos($request_uri, 'wp-login.php') !== false) {
    wp_redirect(home_url('/login')); // Replace with your custom login page slug
    exit;
  }
}
add_action('init', 'rgen_redirect_wp_login');

//Redirect wp-admin Access if Not Logged In
function rgen_redirect_wp_admin_if_not_logged_in()
{
  if (
    is_admin() &&
    !is_user_logged_in() &&
    !(defined('DOING_AJAX') && DOING_AJAX)
  ) {
    wp_redirect(home_url('/login')); // Match your login page slug
    exit;
  }
}
add_action('init', 'rgen_redirect_wp_admin_if_not_logged_in');


//Hide the Admin Bar for Non-Admins
// this is for every user
function rgen_hide_admin_bar()
{
  if (!current_user_can('administrator') && !is_admin()) {
    show_admin_bar(false);
  }
}
add_action('after_setup_theme', 'rgen_hide_admin_bar');

// //Optional: Create a Slug-Based Login Access Key
// function rgen_secure_custom_login_access() {
//     $allowed_key = 'letmein'; // Change this
//     $requested_url = $_SERVER['REQUEST_URI'];

//     if (strpos($requested_url, '/login') !== false) {
//         if (!isset($_GET['key']) || $_GET['key'] !== $allowed_key) {
//             wp_redirect(home_url());
//             exit;
//         }
//     }
// }
// add_action('template_redirect', 'rgen_secure_custom_login_access');

// -----------------------------------------------------------------------------
// REGISTRATION 

//This tells WordPress how to interpret URLs like /login-key/some-token.
function rgen_register_login_key_rewrite()
{
  add_rewrite_rule(
    '^login-key/([^/]+)/?$',
    'index.php?login_key_token=$matches[1]',
    'top'
  );
}
add_action('init', 'rgen_register_login_key_rewrite');

//This lets WordPress recognize login_key_token as a valid variable.
function rgen_add_query_vars($vars)
{
  $vars[] = 'login_key_token';
  return $vars;
}
add_filter('query_vars', 'rgen_add_query_vars');

//Intercept the Request and Handle Login
function rgen_process_login_key_redirect()
{
  $token = get_query_var('login_key_token');

  if ($token) {
    global $wpdb;

    $user_id = $wpdb->get_var(
      $wpdb->prepare(
        "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'rgen_login_key' AND meta_value = %s",
        $token
      )
    );

    if ($user_id) {
      $expires = get_user_meta($user_id, 'rgen_login_key_expires', true);
      $current_time = time();

      // Remove token regardless of expiration
      delete_user_meta($user_id, 'rgen_login_key');
      delete_user_meta($user_id, 'rgen_login_key_expires');

      if ($expires && $current_time <= $expires) {
        wp_set_auth_cookie($user_id, true); // Log in the user
        wp_redirect(home_url('/profile'));
        exit;
      } else {
        wp_redirect(home_url('/login?error=expired'));
        exit;
      }
    } else {
      wp_redirect(home_url('/login?error=invalid'));
      exit;
    }
  }
}



// Brief

// -----------------------------------------------------------------------------
// Notify Author When Their 'brief' Post Is Published
// -----------------------------------------------------------------------------
function notify_user_brief_published($new_status, $old_status, $post)
{
  if ($post->post_type !== 'brief') {
    return;
  }

  if ($old_status !== 'publish' && $new_status === 'publish') {
    $post_id    = $post->ID;
    $author_id  = $post->post_author;
    $user_info  = get_userdata($author_id);

    if ($user_info) {
      $user_email   = $user_info->user_email;
      $user_name    = $user_info->display_name;
      $brief_title  = get_the_title($post_id);
      $brief_link   = get_permalink($post_id);

      $subject = 'Your brief has been published on rgenmusic!';
      $message = "Hi $user_name,\n\nGood news! Your brief titled \"$brief_title\" has been reviewed and published on rgenmusic.com.\n\nYou can view it here: $brief_link\n\nThanks for contributing!";
      $admin_email = get_option('admin_email');
      $headers     = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Bcc: ' . $admin_email
      );

      wp_mail($user_email, $subject, $message, $headers);
    }
  }
}
add_action('transition_post_status', 'notify_user_brief_published', 10, 3);

// -----------------------------------------------------------------------------
// OPTIONAL: Gzip Output Buffering (currently disabled)
// -----------------------------------------------------------------------------
// if (!ob_start("ob_gzhandler")) {
//   ob_start();
// }

// -----------------------------------------------------------------------------
// OPTIONAL: Customize Login Logo URL (currently disabled)
// -----------------------------------------------------------------------------
// function ourHeaderUrl()
// {
//   return esc_url(site_url('/'));
// }
// add_filter('login_headerurl', 'ourHeaderUrl');