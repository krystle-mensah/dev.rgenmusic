<?php
// Start output buffering to prevent the "headers already sent" error
ob_start();
/* Template Name: Custom Registration */
get_header();
// Initialize an array to store error messages
$errors = [];
// Start by checking if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register_user"])) {
  // Verify nonce for security to prevent CSRF attacks
  if (!isset($_POST['rgenmusic_register_nonce']) || !wp_verify_nonce($_POST['rgenmusic_register_nonce'], 'register_user_action')) {
    wp_safe_redirect(add_query_arg('registration_errors', urlencode(json_encode(['Security check failed!'])), get_permalink()));
    exit;
  }
  // Honeypot field: if filled, block submission
  if (!empty($_POST['website'])) {
    wp_safe_redirect(add_query_arg('registration_errors', urlencode(json_encode(['Spam detected.'])), get_permalink()));
    exit;
  }
  // Sanitize user input to prevent XSS attacks and ensure clean data
  $username = sanitize_user($_POST["username"]);
  $email = sanitize_email($_POST["email"]);
  $password = $_POST["password"];
  $password_confirm = $_POST["password_confirm"];
  $artist_type = sanitize_text_field($_POST["artist_type"]);
  // Define allowed artist types
  $allowed_artist_types = ['singer', 'producer', 'instrumentalist', 'dj'];
  // Ensure artist type is valid
  if (!in_array($artist_type, $allowed_artist_types)) {
    $errors[] = "Invalid artist type selected.";
  }
  $first_name = sanitize_text_field($_POST["first_name"]);
  $last_name = sanitize_text_field($_POST["last_name"]);
  $nickname = sanitize_text_field($_POST["nickname"]);
  // Validate Username Format (Only allows letters, numbers, underscores, and periods)
  if (!preg_match('/^[a-zA-Z0-9_.]+$/', $username)) {
    $errors[] = "Username can only contain letters, numbers, underscores, and periods.";
  }
  // Check if the username or email already exists in WordPress
  if (username_exists($username)) {
    $errors[] = "Username is already taken.";
  }
  if (email_exists($email)) {
    $errors[] = "Email is already registered.";
  }
  // Validate password strength
  if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
    $errors[] = "Password must be at least 8 characters long and include an uppercase letter, a number, and a special character.";
  }
  // Ensure password confirmation matches
  if ($password !== $password_confirm) {
    $errors[] = "Passwords do not match.";
  }
  // If errors exist, redirect back to the registration page with error messages
  if (!empty($errors)) {
    wp_safe_redirect(add_query_arg('registration_errors', urlencode(json_encode($errors)), get_permalink()));
    exit;
  }
  // Create a new WordPress user with 'subscriber' role
  $userdata = [
    'user_login' => $username,
    'user_email' => $email,
    'user_pass'  => $password,
    'first_name' => $first_name,
    'last_name'  => $last_name,
    'nickname'   => $nickname,
    'role'       => 'subscriber'
  ];
  $user_id = wp_insert_user($userdata);
  // If the user is created successfully, proceed with additional settings
  if (!is_wp_error($user_id)) {
    // Store the artist type in user meta
    update_user_meta($user_id, 'artist_type', $artist_type);
    // Automatically log the new user in
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    do_action('wp_login', $username, get_user_by('id', $user_id));
    // Set email headers
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    $site_owner_email = get_option('admin_email');
    // Send an email notification to the site owner
    wp_mail($site_owner_email, "New User Registration: $username", "<p>A new user has registered.</p>", $headers);
    // Send a welcome email to the new user
    wp_mail($email, "Welcome to RGenMusic!", "<p>Welcome, $username! Enjoy your stay.</p>", $headers);
    // Redirect to show success message
    wp_safe_redirect(add_query_arg('registration_success', 'true', home_url('index.php/registration-success/')));
    exit;
  } else {
    // If an error occurs during user creation, redirect with an error message
    wp_safe_redirect(add_query_arg('registration_errors', urlencode(json_encode([$user_id->get_error_message()])), get_permalink()));
    exit;
  }
}
?>

<div class="registration-container">
  <h2>Register</h2>

  <?php
  // Display error messages if registration failed
  if (isset($_GET['registration_errors'])) {
    $errors = json_decode(urldecode($_GET['registration_errors']));
    if (!is_array($errors)) {
      $errors = [];
    }
    foreach ($errors as $error) {
      echo "<p style='color:red;'>" . esc_html($error) . "</p>";
    }
    exit; // Stop further execution
  }
  // Show success message after successful registration
  if (isset($_GET['registration_success']) && $_GET['registration_success'] == 'true') {
    echo "<p style='color:green;'>" . esc_html("Registration successful! You are now logged in.") . "</p>";
    exit; // Stop further execution
  }
  ?>
  <!-- Registration Form with added CSS class -->
  <form method="post" enctype="multipart/form-data" class="custom-registration-form">
    <?php wp_nonce_field('register_user_action', 'rgenmusic_register_nonce'); ?>

    <label for="username">Username:</label>
    <input type="text" name="username" required>

    <label for="email">Email:</label>
    <input type="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" name="password" required>
    <small>Password must be at least 8 characters long and include an uppercase letter, a number, and a special character.</small>

    <label for="password_confirm">Confirm Password:</label>
    <input type="password" name="password_confirm" required>

    <label for="first_name">First Name:</label>
    <input type="text" name="first_name" required>

    <label for="last_name">Last Name:</label>
    <input type="text" name="last_name" required>

    <label for="nickname">Nickname:</label>
    <input type="text" name="nickname">

    <label for="artist_type">Artist Type:</label>
    <select name="artist_type">
      <option value="singer">Singer</option>
      <option value="producer">Producer</option>
      <option value="instrumentalist">Instrumentalist</option>
      <option value="dj">DJ</option>
    </select>

    <!-- Honeypot Field (hidden from users) -->
    <div style="display:none;">
      <label for="website">Website:</label>
      <input type="text" name="website">
    </div>

    <input type="submit" name="register_user" value="Create Account">
  </form>
</div>

<?php
// Ensure no further code is executed after redirect
ob_end_flush();
