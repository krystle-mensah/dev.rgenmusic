<?php
/*
Template Name: Email Verification
*/

get_header(); // Include the site header

// Check if the required parameters are present in the URL
if (isset($_GET['code']) && isset($_GET['user_id'])) {
  // Sanitize and retrieve the verification code and user ID from the URL
  $code = sanitize_text_field($_GET['code']);
  $user_id = intval($_GET['user_id']);

  // Retrieve the stored verification code from the user's metadata
  $user_meta_code = get_user_meta($user_id, 'email_verification_code', true);

  // Compare the provided code with the stored code
  if ($code === $user_meta_code) {
    // Prepare user data for updating (this currently does not update any field)
    $userdata = [
      'ID' => $user_id,
    ];
    wp_update_user($userdata); // Update user data (but no actual change is made)

    // Remove the email verification code from user metadata to mark verification as complete
    delete_user_meta($user_id, 'email_verification_code');

    // Send an email notification to the site admin
    $site_owner_email = get_option('admin_email'); // Get site admin email
    $user_info = get_userdata($user_id); // Retrieve user data
    $subject = "New User Registration - Email Verified"; // Email subject
    $message = "A new user has registered and their email has been verified.\n\n";
    $message .= "Username: " . $user_info->user_login . "\n";
    $message .= "Email: " . $user_info->user_email . "\n";
    //$message .= "Role: " . $userdata['role'] . "\n"; // This will cause an issue since 'role' is not set in $userdata

    $headers = ['Content-Type: text/plain; charset=UTF-8']; // Email headers
    wp_mail($site_owner_email, $subject, $message, $headers); // Send the email

    // Display a success message to the user
    echo "<p>Your email has been verified successfully! You can now log in to your account.</p>";
  } else {
    // Display an error message if the code is invalid
    echo "<p>Invalid verification code. Please check your email for the correct link.</p>";
  }
} else {
  // Display an error message if parameters are missing
  echo "<p>Missing verification code or user ID.</p>";
}

get_footer(); // Include the site footer
