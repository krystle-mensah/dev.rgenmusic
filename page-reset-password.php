<?php

/**
 * Template Name: Reset Password 
 */

get_header();

// Get the 'login' parameter from the URL, sanitize it, or default to an empty string if not set.
$login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';

// Get the 'key' parameter from the URL, sanitize it, or default to an empty string if not set.
$key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';

// Check if the form was submitted via POST and if a new password was provided.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {

  // Verify the password reset key and login using WordPress's built-in function.
  $user = check_password_reset_key($key, $login);

  // If the key is invalid or expired, display an error message.
  if (is_wp_error($user)) {
    echo '<p class="error">Invalid or expired reset link.</p>';
  } else {
    // Sanitize the new password input
    $new_pass = sanitize_text_field($_POST['new_password']);

    // Validate password strength
    if (
      strlen($new_pass) < 8 ||
      !preg_match('/[A-Z]/', $new_pass) ||        // At least one uppercase letter
      !preg_match('/[0-9]/', $new_pass) ||        // At least one number
      !preg_match('/[\W_]/', $new_pass)           // At least one special character
    ) {
      echo '<p class="error">Your password must be at least 8 characters long and include an uppercase letter, a number, and a special character.</p>';
      return;
    }

    // Reset the user's password using the new one.
    reset_password($user, $new_pass);

    // Display a success message and link to the login page.
    echo '<p class="success">Password reset successfully. <a href="' . home_url('/login') . '">Log in</a></p>';

    // Stop further script execution to prevent the form from showing again.
    return;
  }
}
?>

<!-- Password Reset Form Markup -->
<div class="reset-password-form">
  <h2>Reset Your Password</h2>
  <form method="post">
    <label for="new_password">New Password:</label><br>
    <input type="password" name="new_password" required><br><br>
    <button type="submit">Reset Password</button>
  </form>
</div>

<?php get_footer(); ?>