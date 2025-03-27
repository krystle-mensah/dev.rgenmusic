<?php
// Start output buffering to prevent the "headers already sent" error
ob_start();
/* Template Name: Custom Registration */
get_header();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register_user"])) {
  if (!isset($_POST['rgenmusic_register_nonce']) || !wp_verify_nonce($_POST['rgenmusic_register_nonce'], 'register_user_action')) {
    wp_safe_redirect(add_query_arg('registration_errors', urlencode(json_encode(['Security check failed!'])), get_permalink()));
    exit;
  }

  if (!empty($_POST['website'])) {
    wp_safe_redirect(add_query_arg('registration_errors', urlencode(json_encode(['Spam detected.'])), get_permalink()));
    exit;
  }

  $username = sanitize_user($_POST["username"]);
  $email = sanitize_email($_POST["user_email"]);
  $password = $_POST["password"];
  $password_confirm = $_POST["password_confirm"];
  $artist_type = sanitize_text_field($_POST["artist_type"]);

  $allowed_artist_types = ['singer', 'producer', 'instrumentalist', 'dj'];
  if (!in_array($artist_type, $allowed_artist_types)) {
    $errors[] = "Invalid artist type selected.";
  }

  if (!preg_match('/^[a-zA-Z0-9_.]+$/', $username)) {
    $errors[] = "Username can only contain letters, numbers, underscores, and periods.";
  }
  if (username_exists($username)) {
    $errors[] = "Username is already taken.";
  }
  if (email_exists($email)) {
    $errors[] = "Email is already registered.";
  }
  if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
    $errors[] = "Password must be at least 8 characters long and include an uppercase letter, a number, and a special character.";
  }
  if ($password !== $password_confirm) {
    $errors[] = "Passwords do not match.";
  }

  if (!empty($errors)) {
    wp_safe_redirect(add_query_arg('registration_errors', urlencode(json_encode($errors)), get_permalink()));
    exit;
  }

  $userdata = [
    'user_login' => $username,
    'user_email' => $email,
    'user_pass'  => $password,
    'role'       => 'pending_verification',
  ];
  $user_id = wp_insert_user($userdata);

  if (!is_wp_error($user_id)) {
    update_user_meta($user_id, 'artist_type', $artist_type);
    $verification_code = wp_generate_password(20, false);
    update_user_meta($user_id, 'email_verification_code', $verification_code);

    // $verification_link = home_url("/registration-success/?code=$verification_code&user_id=$user_id");
    $verification_link = site_url("index.php/registration-success/?code=$verification_code&user_id=$user_id");

    $message = "<p>Thank you for registering. Please verify your email by clicking the link below:</p>";
    $message .= "<p><a href='$verification_link'>Verify Email</a></p>";
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    wp_mail($email, "Verify Your Email", $message, $headers);

    wp_safe_redirect(add_query_arg('registration_success', 'pending_verification', get_permalink()));
    exit;
  } else {
    wp_safe_redirect(add_query_arg('registration_errors', urlencode(json_encode([$user_id->get_error_message()])), get_permalink()));
    exit;
  }
}
?>

<div class="registration-container">
  <h2>Register</h2>
  <?php
  if (isset($_GET['registration_errors'])) {
    $errors = json_decode(urldecode($_GET['registration_errors']));
    if (!is_array($errors)) {
      $errors = [];
    }
    foreach ($errors as $error) {
      echo "<p style='color:red;'>" . esc_html($error) . "</p>";
    }
    exit;
  }
  if (isset($_GET['registration_success']) && $_GET['registration_success'] == 'pending_verification') {
    echo "<p style='color:green;'>Registration successful! Please check your email to verify your account.</p>";
    exit;
  }
  ?>
  <form method="post" enctype="multipart/form-data" class="custom-registration-form">
    <?php wp_nonce_field('register_user_action', 'rgenmusic_register_nonce'); ?>
    <label for="username">Username:</label>
    <input type="text" name="username" required>
    <label for="user_email">Email:</label>
    <input type="email" name="user_email" required>
    <label for="password">Password:</label>
    <input type="password" name="password" required>
    <label for="password_confirm">Confirm Password:</label>
    <input type="password" name="password_confirm" required>
    <label for="artist_type">Artist Type:</label>
    <select name="artist_type">
      <option value="singer">Singer</option>
      <option value="producer">Producer</option>
      <option value="instrumentalist">Instrumentalist</option>
      <option value="dj">DJ</option>
    </select>
    <input type="submit" name="register_user" value="Create Account">
  </form>
</div>
<?php
ob_end_flush();
?>