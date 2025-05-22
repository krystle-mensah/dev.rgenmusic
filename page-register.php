<?php
// Start output buffering to prevent the "headers already sent" error
ob_start();
/* Template Name: Custom Registration */
get_header();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register_user"])) {
  if (!isset($_POST['rgenmusic_register_nonce']) || !wp_verify_nonce($_POST['rgenmusic_register_nonce'], 'register_user_action')) {
    $errors[] = 'Security check failed!';
  }

  // Sanitize all fields
  $username          = sanitize_user($_POST["register_username"]);
  $email             = sanitize_email($_POST["register_user_email"]);
  $first_name        = sanitize_text_field($_POST["register_first_name"]);
  $last_name         = sanitize_text_field($_POST["register_last_name"]);
  $password          = wp_strip_all_tags($_POST["register_password"]);
  $password_confirm  = wp_strip_all_tags($_POST["register_confirm_password"]);

  // Create WP_Error for collecting validation messages
  $validation_errors = new WP_Error();

  if (!is_email($email)) {
    $validation_errors->add('invalid_email', 'Please enter a valid email address.');
  }
  if (!preg_match('/^[a-zA-Z0-9_.]+$/', $username)) {
    $validation_errors->add('invalid_username', 'Username can only contain letters, numbers, underscores, and periods.');
  }
  if (username_exists($username)) {
    $validation_errors->add('username_exists', 'Username is already taken.');
  }
  if (email_exists($email)) {
    $validation_errors->add('email_exists', 'Email is already registered.');
  }
  if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
    $validation_errors->add('weak_password', 'Password must be at least 8 characters long and include an uppercase letter, a number, and a special character.');
  }
  if ($password !== $password_confirm) {
    $validation_errors->add('password_mismatch', 'Passwords do not match.');
  }

  if (!empty($validation_errors->errors)) {
    $errors = $validation_errors->get_error_messages();
  }

  if (empty($errors)) {
    $userdata = [
      'user_login' => $username,
      'user_email' => $email,
      'user_pass'  => $password,
      'first_name' => $first_name,
      'last_name'  => $last_name,
      'role'       => 'pending_verification',
    ];

    $user_id = wp_insert_user($userdata);

    if (!is_wp_error($user_id)) {
      $login_key = wp_generate_password(32, false, false);
      update_user_meta($user_id, 'rgen_login_key', $login_key);
      update_user_meta($user_id, 'rgen_login_key_expires', time() + 86400);

      $login_url = home_url('/login-key/' . $login_key);

      // Compose full name
      $full_name = trim($first_name . ' ' . $last_name);
      if (empty($full_name)) {
        $full_name = $username;
      }

      $subject = 'Your Access Link';
      $message = "Hi {$full_name},\n\n";
      $message .= "Welcome! You can access your account using the secure link below. This link will expire in 24 hours or after first use:\n\n";
      $message .= $login_url . "\n\n";
      $message .= "If you did not request this account, please ignore this message.";

      $mail_sent = wp_mail($email, $subject, $message);

      if ($mail_sent) {
        wp_safe_redirect(site_url('index.php/registration-pending-verification/'));
        exit;
      } else {
        // Email sending failed — inform the user or log the error
        $errors[] = 'There was a problem sending the verification email. Please contact support or try again later.';
      }
    }
  }
}
?>

<!-- HTML Form and Output -->
<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo esc_url(get_theme_file_uri('/images/pageBanner.jpg')); ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title"><?php the_title(); ?></h1>
    <div class="page-banner__intro">
      <p>Please fill in the following fields to create your account:</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">
  <div class="registration-container">
    <?php
    if (!empty($errors)) {
      foreach ($errors as $error) {
        echo "<p class='registration-error-message'>" . esc_html($error) . "</p>";
      }
    }
    ?>
    <form method="post" enctype="multipart/form-data" class="custom-registration-form">
      <?php wp_nonce_field('register_user_action', 'rgenmusic_register_nonce'); ?>
      <input type="hidden" name="redirect_to" value="<?php echo esc_url(site_url('/registration-pending-verification/')); ?>">
      <input type="hidden" name="action" value="register">

      <label for="register_first_name">First Name:</label>
      <input type="text" id="register_first_name" name="register_first_name" value="<?php echo isset($_POST['register_first_name']) ? esc_attr($_POST['register_first_name']) : ''; ?>" required>

      <label for="register_last_name">Last Name:</label>
      <input type="text" id="register_last_name" name="register_last_name" value="<?php echo isset($_POST['register_last_name']) ? esc_attr($_POST['register_last_name']) : ''; ?>" required>

      <label for="register_username">Username:</label>
      <p>Choose a unique username. It can contain letters, numbers, underscores, and periods.</p>
      <input type="text" id="register_username" name="register_username" value="<?php echo isset($_POST['register_username']) ? esc_attr($_POST['register_username']) : ''; ?>" required>

      <label for="register_user_email">Email:</label>
      <p>Enter a valid email address. You will need to verify it after registration.</p>
      <input type="email" id="register_user_email" name="register_user_email" value="<?php echo isset($_POST['register_user_email']) ? esc_attr($_POST['register_user_email']) : ''; ?>" required>

      <label for="register_password">Password:</label>
      <p>Your password must be at least 8 characters long and include an uppercase letter, a number, and a special character.</p>
      <input type="password" id="register_password" name="register_password" required>

      <label for="register_confirm_password">Confirm Password:</label>
      <input type="password" id="register_confirm_password" name="register_confirm_password" required>

      <input type="submit" name="register_user" value="Create Account">
    </form>
    <p class="agreement-message">
      I agree to the <a href="<?php echo esc_url(site_url('/terms-and-conditions')); ?>">Terms and Conditions</a> and
      <a href="<?php echo esc_url(site_url('/privacy-policy')); ?>">Privacy Policy</a>.
    </p>
  </div>
</div>

<?php get_footer();
ob_end_flush();
?>