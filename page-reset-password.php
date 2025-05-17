<?php

/**
 * Template Name: Reset Password
 * 
 * This is a custom page template for users to reset their password.
 */

get_header();

// Get the 'login' and 'key' values from the link in the user's email
$login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';
$key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';

// Set up empty messages to show later
$error_message = '';
$success_message = '';

// Check if the login or key is missing
if (empty($login) || empty($key)) {
  // If something is missing, show an error
  $error_message = 'Invalid password reset link. Please check your email for the correct link.';
}

// If someone sends the form (presses submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'])) {

  // Check if the hidden security code (nonce) is correct
  if (!isset($_POST['reset_password_nonce']) || !wp_verify_nonce($_POST['reset_password_nonce'], 'reset_password_action')) {
    $error_message = 'Security check failed. Please try again.';
  } else {
    // Check if the two entered passwords match
    if ($_POST['new_password'] !== $_POST['confirm_password']) {
      $error_message = 'Passwords do not match.';
    } else {
      // Make sure the reset link is still valid
      $user = check_password_reset_key($key, $login);

      // If the link is bad or expired
      if (is_wp_error($user)) {
        $error_message = 'Invalid or expired reset link.';
      } else {
        // Get the new password safely
        $new_pass = wp_unslash($_POST['new_password']);

        // Make sure the password is strong enough
        if (
          strlen($new_pass) < 8 ||                        // At least 8 characters
          !preg_match('/[A-Z]/', $new_pass) ||            // Has an uppercase letter
          !preg_match('/[0-9]/', $new_pass) ||            // Has a number
          !preg_match('/[\W_]/', $new_pass)               // Has a special character
        ) {
          $error_message = 'Your password must be at least 8 characters long and include an uppercase letter, a number, and a special character.';
        } else {
          // All checks passed — save the new password
          reset_password($user, $new_pass);
          // Tell the user it worked and give them a login link
          $success_message = 'Password reset successfully. <a href="' . esc_url(home_url('/login')) . '">Log in</a>.';
        }
      }
    }
  }
}

?>
<div class="page-banner">

  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title"><?php the_title(); ?></h1>
    <div class="page-banner__intro">
    </div>
  </div>
</div>

<div class="container container--narrow page-section">

  <!-- The HTML part of the form starts here -->
  <div class="generic-content">
    <div class="reset-password-form">

      <h2>Reset Your Password</h2>

      <!-- Show the error message, if there is one -->
      <?php if ($error_message): ?>
        <p class="error"><?php echo esc_html($error_message); ?></p>
      <?php endif; ?>

      <!-- Show the success message if the password was changed -->
      <?php if ($success_message): ?>
        <p class="success"><?php echo wp_kses_post($success_message); ?></p>

        <!-- If there's no success yet, show the form -->
      <?php else: ?>
        <?php if (empty($login) || empty($key)): ?>
          <!-- If the link is broken or incomplete, ask the user to check their email -->
          <p>Please use the password reset link sent to your email.</p>
        <?php else: ?>
          <!-- Show the password reset form -->
          <form method="post" novalidate>
            <!-- Hidden security field -->
            <?php wp_nonce_field('reset_password_action', 'reset_password_nonce'); ?>

            <!-- Ask for the new password -->
            <label for="new_password">New Password:</label><br>
            <input type="password" name="new_password" id="new_password" required autocomplete="new-password"><br><br>

            <!-- Ask to confirm the new password -->
            <label for="confirm_password">Confirm New Password:</label><br>
            <input type="password" name="confirm_password" id="confirm_password" required autocomplete="new-password"><br><br>

            <!-- Submit the form -->
            <button type="submit">Reset Password</button>
          </form>

        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php get_footer();
?>