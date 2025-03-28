<?php
/*
Template Name: Email Verification
*/
get_header();

if (isset($_GET['code']) && isset($_GET['user_id'])) {
  $code = sanitize_text_field($_GET['code']);
  $user_id = intval($_GET['user_id']);

  $user_meta_code = get_user_meta($user_id, 'email_verification_code', true);

  // Check if the verification code matches
  if ($code === $user_meta_code) {
    // Activate the user
    $userdata = [
      'ID' => $user_id,
      'role' => 'subscriber',  // Change the role if needed
    ];
    wp_update_user($userdata);

    // Optional: Delete verification code after success
    delete_user_meta($user_id, 'email_verification_code');

    // Notify the site owner about the new user
    $site_owner_email = get_option('admin_email');
    $user_info = get_userdata($user_id);
    $subject = "New User Registration - Email Verified";
    $message = "A new user has registered and their email has been verified.\n\n";
    $message .= "Username: " . $user_info->user_login . "\n";
    $message .= "Email: " . $user_info->user_email . "\n";
    $message .= "Role: " . $userdata['role'] . "\n";

    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    wp_mail($site_owner_email, $subject, $message, $headers);

    echo "<p>Your email has been verified successfully! You can now log in to your account.</p>";
  } else {
    echo "<p>Invalid verification code. Please check your email for the correct link.</p>";
  }
} else {
  echo "<p>Missing verification code or user ID.</p>";
}

get_footer();
