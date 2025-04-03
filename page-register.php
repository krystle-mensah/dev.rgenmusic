<?php
// Start output buffering to prevent the "headers already sent" error
ob_start();
/* Template Name: Custom Registration */
get_header();

$errors = [];
// Initialize error variables
$username_error = '';
$email_error = '';
$password_error = '';
$confirm_password_error = '';

// Check if the form was submitted via POST and if the "register_user" field is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register_user"])) {
  // Verify the nonce to ensure the request is legitimate and not a CSRF attack
  if (!isset($_POST['rgenmusic_register_nonce']) || !wp_verify_nonce($_POST['rgenmusic_register_nonce'], 'register_user_action')) {
    $errors[] = 'Security check failed!';
  }
  // Check if the "website" field is filled (commonly used as a honeypot field to detect bots)
  if (!empty($_POST['website'])) {
    $errors[] = 'Spam detected.';
  }

  $username = sanitize_user($_POST["register_username"]);
  $email = sanitize_email($_POST["register_user_email"]);
  $first_name = sanitize_text_field($_POST["register_first_name"]);
  $last_name = sanitize_text_field($_POST["register_last_name"]);
  $password = $_POST["register_password"];
  $password_confirm = $_POST["register_confirm_password"];

  // Field validations and setting error messages
  if (!preg_match('/^[a-zA-Z0-9_.]+$/', $username)) {
    $username_error = 'Username can only contain letters, numbers, underscores, and periods.';
  }
  if (username_exists($username)) {
    $username_error = 'Username is already taken.';
  }
  if (email_exists($email)) {
    $email_error = 'Email is already registered.';
  }
  if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
    $password_error = 'Password must be at least 8 characters long and include an uppercase letter, a number, and a special character.';
  }
  if ($password !== $password_confirm) {
    $confirm_password_error = 'Passwords do not match.';
  }

  // If there are errors, prevent registration and display errors
  if (!empty($username_error) || !empty($email_error) || !empty($password_error) || !empty($confirm_password_error)) {
    // Errors are stored in an array for redirection or inline display
    $errors = array_filter([$username_error, $email_error, $password_error, $confirm_password_error]);
  }
  // Check if there are no validation errors
  if (empty($errors)) {
    // Proceed with registration if no errors
    $userdata = [
      'user_login' => $username,
      'user_email' => $email,
      'user_pass'  => $password,
      'first_name' => $first_name,
      'last_name'  => $last_name,
      'role'       => 'pending_verification', // Assign a custom role for unverified users
    ];
    // Insert the new user into WordPress
    $user_id = wp_insert_user($userdata);
    // Check if the user was created successfully (no errors)
    if (!is_wp_error($user_id)) {
      // Generate a random verification code (20 characters, no special characters)
      $verification_code = wp_generate_password(20, false);
      // Store the verification code as user meta data
      update_user_meta($user_id, 'email_verification_code', $verification_code);
      // Generate a verification link for the user
      $verification_link = site_url("index.php/email-verification/?code=$verification_code&user_id=$user_id");

      $message = "<p>Thank you for registering. Please verify your email by clicking the link below:</p>";
      //this message goes to the users email
      $message .= "<p><a href='$verification_link'>Verify Email</a></p>";
      $headers = ['Content-Type: text/html; charset=UTF-8'];
      wp_mail($email, "Verify Your Email", $message, $headers);

      wp_safe_redirect(site_url('index.php/registration-pending-verification/'));
      exit;
    } else {
      $errors[] = $user_id->get_error_message();
    }
  }
}


?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
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
    // Check if the $errors array is not empty
    if (!empty($errors)) {
      // Loop through each error message in the $errors array
      foreach ($errors as $error) {
        // Output each error message inside a paragraph tag with a class for styling
        // esc_html() is used to escape special characters and prevent XSS attacks
        echo "<p class='registration-error-message';'>" . esc_html($error) . "</p>";
      }
    }
    ?>

    <!-- <div class="generic-content"></div> -->
    <form method="post" enctype="multipart/form-data" class="custom-registration-form">
      <?php wp_nonce_field('register_user_action', 'rgenmusic_register_nonce'); ?>

      <label for="register_first_name">First Name: </label>
      <input type="text" id="register_first_name" name="register_first_name" value="<?php echo isset($_POST['register_first_name']) ? esc_attr($_POST['register_first_name']) : ''; ?>" required>

      <label for="register_last_name">Last Name:</label>
      <input type="text" id="register_last_name" name="register_last_name" value="<?php echo isset($_POST['register_last_name']) ? esc_attr($_POST['register_last_name']) : ''; ?>" required>

      <label for="register_username">Username:</label>
      <p>Choose a unique username. It can contain letters, numbers, underscores, and periods.</p>
      <input type="text" id="register_username" name="register_username" value="<?php echo isset($_POST['register_username']) ? esc_attr($_POST['register_username']) : ''; ?>" required>
      <?php
      /* 
if ($username_error) : ?>
  <p class="registration-error-message"><?php echo esc_html($username_error); ?></p>
<?php endif; ?> 
*/
      ?>
      <label for="register_user_email">Email:</label>
      <p>Enter a valid email address. You will need to verify your email address after registration.</p>
      <input type="email" id="register_user_email" name="register_user_email" value="<?php echo isset($_POST['register_user_email']) ? esc_attr($_POST['register_user_email']) : ''; ?>" required>
      <?php
      /*
      if ($email_error) : ?>
        <p class="registration-error-message"><?php echo esc_html($email_error); ?></p>
      <?php endif; 
      */
      ?>
      <label for="register_password">Password:</label>
      <p>Your password must be at least 8 characters long and include an uppercase letter, a number, and a special character.</p>
      <input type="password" id="register_password" name="register_password" required>
      <?php
      /*
      if ($password_error) : ?>
        <p class="registration-error-message"><?php echo esc_html($password_error); ?></p>
      <?php endif; 
      */
      ?>
      <label for="register_confirm_password">Confirm Password:</label>
      <p>Re-enter your password to confirm it matches.</p>
      <input type="password" id="register_confirm_password" name="register_confirm_password" required>
      <?php
      /*
      if ($confirm_password_error) : ?>
        <p class="registration-error-message"><?php echo esc_html($confirm_password_error); ?></p>
      <?php endif; 
      */
      ?>
      <input type="submit" name="register_user" value="Create Account">
    </form>
    <p>I agree to the (terms and conditions link) and <a href="<?php echo site_url('/privacy-policy'); ?>">Privacy policy</a>.</p>
  </div>
</div>
<?php
get_footer();
ob_end_flush();
?>