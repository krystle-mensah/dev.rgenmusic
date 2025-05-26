<?php
// Start output buffering to prevent "headers already sent" errors caused by premature output
ob_start();

/* Template Name: Custom Registration */
// Include the site header
get_header();

// Initialize an empty array to store error messages
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register_user"])) {
  // Verify nonce to ensure request is legitimate and not a CSRF attack
  if (!isset($_POST['rgenmusic_register_nonce']) || !wp_verify_nonce($_POST['rgenmusic_register_nonce'], 'register_user_action')) {
    $errors[] = 'Security check failed!';
  }

  // Sanitize all input fields to prevent malicious input
  $username          = sanitize_user($_POST["register_username"]);
  $email             = sanitize_email($_POST["register_user_email"]);
  $first_name        = sanitize_text_field($_POST["register_first_name"]);
  $last_name         = sanitize_text_field($_POST["register_last_name"]);
  $password          = wp_strip_all_tags($_POST["register_password"]);
  $password_confirm  = wp_strip_all_tags($_POST["register_confirm_password"]);

  // Create a WP_Error instance to collect validation errors
  $validation_errors = new WP_Error();

  // Validate email format
  if (!is_email($email)) {
    $validation_errors->add('invalid_email', 'Please enter a valid email address.');
  }

  // Allow only certain characters in the username
  if (!preg_match('/^[a-zA-Z0-9_.]+$/', $username)) {
    $validation_errors->add('invalid_username', 'Username can only contain letters, numbers, underscores, and periods.');
  }

  // Check if the username is already taken
  if (username_exists($username)) {
    $validation_errors->add('username_exists', 'Username is already taken.');
  }

  // Check if the email is already in use
  if (email_exists($email)) {
    $validation_errors->add('email_exists', 'Email is already registered.');
  }

  // Ensure password strength: min 8 chars, one uppercase, one number, one special character
  if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
    $validation_errors->add('weak_password', 'Password must be at least 8 characters long and include an uppercase letter, a number, and a special character.');
  }

  // Check if password and confirmation match
  if ($password !== $password_confirm) {
    $validation_errors->add('password_mismatch', 'Passwords do not match.');
  }

  do_action('register_post', $username, $email, $validation_errors);
  $validation_errors = apply_filters('registration_errors', $validation_errors, $username, $email);

  // If there are validation errors, populate the main error array
  if (!empty($validation_errors->errors)) {
    $errors = $validation_errors->get_error_messages();
  }

  // Proceed if there are no errors
  if (empty($errors)) {
    // Prepare user data for account creation
    $userdata = [
      'user_login' => $username,
      'user_email' => $email,
      'user_pass'  => $password,
      'first_name' => $first_name,
      'last_name'  => $last_name,
      'role'       => 'pending_verification', // Custom role pending email verification
    ];

    // Apply filter to allow custom modifications to user data
    $userdata = apply_filters('rgenmusic_registration_userdata', $userdata);

    // Create the new user
    $user_id = wp_insert_user($userdata);

    if (!is_wp_error($user_id)) {
      // Trigger user_register action hook
      do_action('user_register', $user_id);

      // Send new user notification and admin
      wp_new_user_notification($user_id, null, 'both');

      // Redirect to success page
      $redirect_url = (isset($_POST['redirect_to']) && wp_validate_redirect($_POST['redirect_to']))
        ? $_POST['redirect_to']
        : site_url('/registration-success/');

      wp_safe_redirect($redirect_url);

      exit;
    }

    // if (!is_wp_error($user_id)) {
    //   // Send standard registration success email or just redirect
    //   wp_safe_redirect(site_url('/registration-success/'));
    //   exit;
    // }
  }
}
?>

<!-- Page Banner Section -->
<div class="page-banner">
  <div class="page-banner__bg-image my-banner-bg-image">
    <img src="<?php echo get_theme_file_uri('/images/pageBanner.jpg'); ?>" alt="Page banner background" loading="lazy">
  </div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title"><?php the_title(); ?></h1>
    <div class="page-banner__intro">
      <p>Please fill in the following fields to create your account:</p>
    </div>
  </div>
</div>

<!-- Registration Form Section -->
<div class="container container--narrow page-section">
  <div class="registration-container">
    <?php
    // Display error messages if any exist
    if (!empty($errors)) {
      foreach ($errors as $error) {
        echo "<p class='registration-error-message'>" . esc_html($error) . "</p>";
      }
    }
    ?>
    <!-- Registration Form -->
    <form method="post" enctype="multipart/form-data" class="custom-registration-form">
      <!-- Nonce field for security -->
      <?php wp_nonce_field('register_user_action', 'rgenmusic_register_nonce'); ?>

      <!-- Hidden fields to assist redirection and form action handling -->
      <input type="hidden" name="redirect_to" value="<?php echo esc_url(site_url('/registration-pending-verification/')); ?>">
      <input type="hidden" name="action" value="register">

      <!-- First Name Field -->
      <label for="register_first_name">First Name:</label>
      <input type="text" id="register_first_name" name="register_first_name" value="<?php echo isset($_POST['register_first_name']) ? esc_attr($_POST['register_first_name']) : ''; ?>" required>

      <!-- Last Name Field -->
      <label for="register_last_name">Last Name:</label>
      <input type="text" id="register_last_name" name="register_last_name" value="<?php echo isset($_POST['register_last_name']) ? esc_attr($_POST['register_last_name']) : ''; ?>" required>

      <!-- Username Field -->
      <label for="register_username">Username:</label>
      <p>Choose a unique username. It can contain letters, numbers, underscores, and periods.</p>
      <input type="text" id="register_username" name="register_username" value="<?php echo isset($_POST['register_username']) ? esc_attr($_POST['register_username']) : ''; ?>" required>

      <!-- Email Field -->
      <label for="register_user_email">Email:</label>
      <p>Enter a valid email address. You will need to verify it after registration.</p>
      <input type="email" id="register_user_email" name="register_user_email" value="<?php echo isset($_POST['register_user_email']) ? esc_attr($_POST['register_user_email']) : ''; ?>" required>

      <!-- Password Field -->
      <label for="register_password">Password:</label>
      <p>Your password must be at least 8 characters long and include an uppercase letter, a number, and a special character.</p>
      <input type="password" id="register_password" name="register_password" required>

      <!-- Confirm Password Field -->
      <label for="register_confirm_password">Confirm Password:</label>
      <input type="password" id="register_confirm_password" name="register_confirm_password" required>

      <!-- Submit Button -->
      <input type="submit" name="register_user" value="Create Account">
    </form>

    <!-- Terms and Conditions Notice -->
    <p class="agreement-message">
      I agree to the <a href="<?php echo esc_url(site_url('/terms-and-conditions')); ?>">Terms and Conditions</a> and
      <a href="<?php echo esc_url(site_url('/privacy-policy')); ?>">Privacy Policy</a>.
    </p>
  </div>
</div>

<?php
// Load the footer template
get_footer();

// End output buffering and flush output
ob_end_flush();
?>