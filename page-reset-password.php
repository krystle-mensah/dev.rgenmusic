<?php

/**
 * Template Name: Reset Password 
 */

get_header();

// Retrieve the 'login' parameter from the URL, sanitize it, or default to an empty string if not set.
$login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';

// Retrieve the 'key' parameter from the URL, sanitize it, or default to an empty string if not set.
$key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';

// Check if the form was submitted via POST method and if a new password was provided.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {

  // Verify the nonce for security
  if (!isset($_POST['reset_password_nonce']) || !wp_verify_nonce($_POST['reset_password_nonce'], 'reset_password_action')) {
    echo '<p class="error">Security check failed.</p>';
    return;
  }

  // Use WordPress's built-in function to verify the password reset key and login.
  $user = check_password_reset_key($key, $login);

  // If the key is invalid or expired, display an error message.
  if (is_wp_error($user)) {
    echo '<p class="error">Invalid or expired reset link.</p>';
  } else {
    //This is too aggressive for passwords, as it might remove @, %, #, etc.
    //which out for password problems
    $new_pass = wp_unslash($_POST['new_password']);

    // Validate the new password for security (at least 8 characters, an uppercase letter, a number, and a special character).
    if (
      strlen($new_pass) < 8 ||              // Password must be at least 8 characters.
      !preg_match('/[A-Z]/', $new_pass) ||   // At least one uppercase letter.
      !preg_match('/[0-9]/', $new_pass) ||   // At least one number.
      !preg_match('/[\W_]/', $new_pass)      // At least one special character.
    ) {
      echo '<p class="error">Your password must be at least 8 characters long and include an uppercase letter, a number, and a special character.</p>';
      return; // Stop further processing if the password doesn't meet the requirements.
    }

    // If the password is valid, reset the user's password with the new one.
    reset_password($user, $new_pass);

    // Display a success message and provide a link to the login page.
    echo '<p class="success">Password reset successfully. <a href="' . home_url('/login') . '">Log in</a></p>';

    // Prevent further execution of the code to avoid showing the form again.
    return;
  }
}
?>

<!-- HTML structure for the Password Reset Form -->
<div class="reset-password-form">
  <h2>Reset Your Password</h2>
  <!-- Form for entering the new password -->
  <form method="post">
    <?php wp_nonce_field('reset_password_action', 'reset_password_nonce'); ?>
    <label for="new_password">New Password:</label><br>
    <input type="password" name="new_password" required><br><br>
    <button type="submit">Reset Password</button>
  </form>
</div>

<?php
// Get the footer of the WordPress site.
get_footer();
?>