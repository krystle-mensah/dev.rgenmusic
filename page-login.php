<?php
/* Template Name: Custom Login */

// Get the header of the website (includes site header, scripts, styles)
get_header();

// Handle login form submission when the form is posted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login_user"])) {

  // Verify nonce for security to prevent CSRF (Cross-Site Request Forgery)
  if (!isset($_POST['rgenmusic_login_nonce']) || !wp_verify_nonce($_POST['rgenmusic_login_nonce'], 'login_user_action')) {
    wp_die('Security check failed!'); // Stop execution if nonce is invalid
  }

  // Sanitize username and get the password and remember option from the form
  $username = sanitize_user($_POST["username"]);
  $password = $_POST["password"];
  $remember = isset($_POST["remember_me"]); // Check if the user wants to be remembered

  // Validate that both username and password are provided
  if (empty($username) || empty($password)) {
    // Redirect with error if either field is empty
    wp_safe_redirect(add_query_arg('login_error', urlencode('Username and password are required.'), get_permalink()));
    exit;
  }

  // Prepare credentials for login
  $credentials = [
    'user_login'    => $username,
    'user_password' => $password,
    'remember'      => $remember
  ];

  // Attempt to log the user in
  $user = wp_signon($credentials, false);

  // Check if login was successful
  if (!is_wp_error($user)) {
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, $remember);
    // if the user is admin
    if (user_can($user, 'administrator')) {
      //then direct them to the admin area
      wp_redirect(admin_url());
    } else {
      // then direct them there profile page.
      $redirect_url = home_url('index.php/profile/');
    }
    exit;

    if (isset($_GET['redirect_to'])) {
      $redirect_url = esc_url($_GET['redirect_to']);
    }
    wp_safe_redirect($redirect_url); // Safe redirect to the URL
    exit;
  } else {
    // Display error if login fails
    $error_message = 'Invalid username or password.';
    wp_safe_redirect(add_query_arg('login_error', urlencode($error_message), get_permalink())); // Redirect back with error message
    exit;
  }
}
?>

<!-- Start of login form container -->

<?php

// Check if the 'the_custom_logo' function exists and a custom logo is set for the site
if (function_exists('the_custom_logo') && has_custom_logo()) { ?>
  <!-- If a custom logo exists, display it inside a div with a class 'site_logo' -->
  <div class="site-logo hide">
    <?php the_custom_logo(); // Output the custom logo 
    ?>
  </div>
<?php } else { ?>
  <!-- If no custom logo is set, display the site name as a heading with a link to the homepage -->
  <h1 class="school-logo-text">
    <a href="<?php echo site_url(); ?>"><strong>Rgen</strong> Music</a>
  </h1>
<?php } ?>

<!-- Check if there is a login error and display the error message -->
<?php
/*
if (isset($_GET['login_error'])) {
    echo "<p class='error-message' style='color:red;'>" . esc_html(urldecode($_GET['login_error'])) . "</p>";
}
*/
// Check if there is a login error and display the error message 

if (isset($_GET['login_error'])) {
  echo "<p class='login-error-message'>" . esc_html(urldecode($_GET['login_error'])) . "</p>";
}

?>

<div class="page-banner">

  <div class="page-banner__bg-image my-banner-bg-image">
    <img src="<?php echo get_theme_file_uri('/images/pageBanner.jpg'); ?>" alt="Page banner background" loading="lazy">
  </div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title"><?php the_title(); ?></h1>
    <div class="page-banner__intro">
      <!-- <p>THIS IS A PAGE </p> -->
    </div>
  </div>
</div>

<div class="container container--narrow page-section">

  <div class="generic-content">
    <!-- Custom login form -->
    <form method="post" class="custom-login-form">
      <!-- Nonce field to verify the form submission (security) -->
      <?php wp_nonce_field('login_user_action', 'rgenmusic_login_nonce'); ?>

      <!-- Username input field -->
      <label for="username">Username or Email</label>
      <input type="text" name="username" id="username" required aria-label="Username">

      <!-- Password input field -->
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required aria-label="Password">
      <!-- Button to toggle password visibility -->
      <button type="button" onclick="togglePassword()" aria-label="Show/Hide password">Show/Hide</button>

      <!-- Remember me checkbox -->
      <label>
        <input type="checkbox" name="remember_me" aria-label="Remember me"> Remember Me
      </label>

      <!-- Submit button for the form -->
      <input type="submit" name="login_user" value="Login">
    </form>

    <!-- Forgot password link -->
    <p><a href="<?php echo wp_lostpassword_url(); ?>">Forgot your password?</a></p>
    <!-- Register link for users without an account -->
    <p>Don't have an account? <a href="<?php echo esc_url(home_url('index.php/registration/')); ?>">Register here</a></p>
  </div>
</div>

<!-- This JavaScript function toggles the visibility of the password -->
<script>
  function togglePassword() {
    var passwordField = document.getElementById("password");
    if (passwordField.type === "password") {
      passwordField.type = "text"; // Show password if it is currently hidden
    } else {
      passwordField.type = "password"; // Hide password if it is currently visible
    }
  }
</script>

<?php get_footer(); ?>