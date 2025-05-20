<?php
/*
Template Name: Edit Profile Page
*/
get_header();

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Redirect to login if not logged in
if (!$user_id) {
  wp_redirect(wp_login_url());
  exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
  if (isset($_POST['update_profile_nonce_field']) && wp_verify_nonce($_POST['update_profile_nonce_field'], 'update_profile_nonce')) {

    // Update user fields
    wp_update_user([
      'ID' => $user_id,
      'first_name' => sanitize_text_field($_POST['first_name']),
      'last_name' => sanitize_text_field($_POST['last_name']),
      'nickname' => sanitize_text_field($_POST['nickname']),
    ]);

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
      require_once ABSPATH . 'wp-admin/includes/image.php';
      require_once ABSPATH . 'wp-admin/includes/file.php';
      require_once ABSPATH . 'wp-admin/includes/media.php';

      $file_type = wp_check_filetype($_FILES['profile_picture']['name']);
      $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
      $max_size = 5 * 1024 * 1024; // 5MB

      if (in_array($file_type['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= $max_size) {
        $uploaded = media_handle_upload('profile_picture', 0);
        if (!is_wp_error($uploaded)) {
          update_user_meta($user_id, 'profile_picture', $uploaded);
        }
      } else {
        echo '<p style="color:red;">Invalid file type or size. Upload JPEG/PNG/GIF under 5MB.</p>';
      }
    }

    // Redirect to profile view page
    wp_redirect(site_url('/profile/'));
    exit;
  } else {
    echo '<p style="color:red;">Nonce verification failed. Please try again.</p>';
  }
}

// Fetch user data
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$nickname = get_user_meta($user_id, 'nickname', true);
$profile_picture_id = get_user_meta($user_id, 'profile_picture', true);
$profile_picture_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : '';

?>

<div class="container container--narrow page-section">
  <h1>Edit Your Profile</h1>

  <form method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('update_profile_nonce', 'update_profile_nonce_field'); ?>

    <label for="first_name">First Name:</label>
    <input type="text" name="first_name" value="<?php echo esc_attr($first_name); ?>">

    <label for="last_name">Last Name:</label>
    <input type="text" name="last_name" value="<?php echo esc_attr($last_name); ?>">

    <label for="nickname">Nickname:</label>
    <input type="text" name="nickname" value="<?php echo esc_attr($nickname); ?>">

    <label for="profile_picture">Profile Picture:</label>
    <input type="file" name="profile_picture">
    <?php if ($profile_picture_url): ?>
      <p>Current Picture:</p>
      <img src="<?php echo esc_url($profile_picture_url); ?>" width="100" alt="Profile Picture">
    <?php endif; ?>

    <br><br>
    <input type="submit" name="update_profile" value="Update Profile">
  </form>

  <p><a href="<?php echo site_url('/profile/'); ?>">&larr; Back to Profile</a></p>
</div>

<?php get_footer(); ?>