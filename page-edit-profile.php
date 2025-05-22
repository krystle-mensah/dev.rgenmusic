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

    // Include media handling functions
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
      $file_type = wp_check_filetype($_FILES['profile_picture']['name']);
      $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
      $max_size = 5 * 1024 * 1024; // 5MB

      if (in_array($file_type['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= $max_size) {
        $uploaded = media_handle_upload('profile_picture', 0);
        if (!is_wp_error($uploaded)) {
          update_user_meta($user_id, 'profile_picture', $uploaded);
        }
      } else {
        echo '<p style="color:red;">Invalid profile picture file type or size. Upload JPEG/PNG/GIF under 5MB.</p>';
      }
    }

    // Handle cover photo upload
    if (!empty($_FILES['cover_photo']['name'])) {
      $file_type = wp_check_filetype($_FILES['cover_photo']['name']);
      $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
      $max_size = 5 * 1024 * 1024; // 5MB

      if (in_array($file_type['type'], $allowed_types) && $_FILES['cover_photo']['size'] <= $max_size) {
        $uploaded = media_handle_upload('cover_photo', 0);
        if (!is_wp_error($uploaded)) {
          update_user_meta($user_id, 'cover_photo', $uploaded);
        }
      } else {
        echo '<p style="color:red;">Invalid cover photo file type or size. Upload JPEG/PNG/GIF under 5MB.</p>';
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

$cover_photo_id = get_user_meta($user_id, 'cover_photo', true);
$cover_photo_url = $cover_photo_id ? wp_get_attachment_url($cover_photo_id) : '';
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
      <p>Current Profile Picture:</p>
      <div><img src="<?php echo esc_url($profile_picture_url); ?>" width="100" alt="Profile Picture" title="<?php echo esc_attr($user_name); ?>'s Profile Picture"></div>     
    <?php endif; ?>

    <label for="cover_photo">Cover Photo:</label>
    <input type="file" name="cover_photo">
    <?php if ($cover_photo_url): ?>
      <p>Current Cover Photo:</p>
      <img src="<?php echo esc_url($cover_photo_url); ?>" width="300" alt="Cover Photo">
    <?php endif; ?>

    <br><br>
    <input type="submit" name="update_profile" value="Update Profile">
  </form>

  <p><a href="<?php echo site_url('/profile/'); ?>">&larr; Back to Profile</a></p>
</div>

<?php get_footer(); ?>
