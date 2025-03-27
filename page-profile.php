<?php
/*
Template Name: Profile Page
*/
get_header();
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Save profile updates and redirect to view mode after update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
  // Verify nonce
  if (isset($_POST['update_profile_nonce_field']) && wp_verify_nonce($_POST['update_profile_nonce_field'], 'update_profile_nonce')) {

    update_user_meta($user_id, 'artist_bio', sanitize_textarea_field($_POST['artist_bio']));
    update_user_meta($user_id, 'artist_type', sanitize_text_field($_POST['artist_type']));

    // Update first name, last name, and nickname
    wp_update_user([
      'ID' => $user_id,
      'first_name' => sanitize_text_field($_POST['first_name']),
      'last_name' => sanitize_text_field($_POST['last_name']),
      'nickname' => sanitize_text_field($_POST['nickname'])
    ]);

    // Handle profile picture upload with validation
    if (!empty($_FILES['profile_picture']['name'])) {
      require_once ABSPATH . 'wp-admin/includes/file.php';

      // Check file type and size
      $file_type = wp_check_filetype($_FILES['profile_picture']['name']);
      $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
      $max_size = 5 * 1024 * 1024; // 5MB max size

      if (in_array($file_type['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= $max_size) {
        $uploaded = media_handle_upload('profile_picture', 0);
        if (!is_wp_error($uploaded)) {
          update_user_meta($user_id, 'profile_picture', $uploaded);
        }
      } else {
        echo 'Invalid file type or file size too large. Please upload a valid image (JPEG, PNG, or GIF) under 5MB.';
      }
    }

    // Redirect back to profile view mode after profile update
    wp_redirect(get_permalink());
    exit;
  } else {
    echo 'Nonce verification failed. Please try again.';
  }
}

// Get existing values
$artist_bio = get_user_meta($user_id, 'artist_bio', true);
$artist_type = get_user_meta($user_id, 'artist_type', true);
$profile_picture_id = get_user_meta($user_id, 'profile_picture', true);
$profile_picture_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : '';

// Get user details
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$nickname = get_user_meta($user_id, 'nickname', true);
?>
<div class="container container--narrow page-section">
  <div class="profile-container">
    <?php if ($user_id) : ?>
      <div class="profile-header">
        <div class="profile-picture">
          <?php if ($profile_picture_url) : ?>
            <img src="<?php echo esc_url($profile_picture_url); ?>" alt="Profile Picture" class="profile-pic">
          <?php endif; ?>
        </div>
      </div>

      <!-- Edit Button -->
      <?php if (isset($_GET['edit']) && $_GET['edit'] == 'true') : ?>
        <!-- Edit Form -->
        <form method="post" enctype="multipart/form-data">
          <!-- Add Nonce Field -->
          <?php wp_nonce_field('update_profile_nonce', 'update_profile_nonce_field'); ?>

          <!-- Name Fields -->
          <h3>Personal Information</h3>
          <label for="first_name">First Name:</label>
          <input type="text" name="first_name" value="<?php echo esc_attr($first_name); ?>">

          <label for="last_name">Last Name:</label>
          <input type="text" name="last_name" value="<?php echo esc_attr($last_name); ?>">

          <label for="nickname">Nickname:</label>
          <input type="text" name="nickname" value="<?php echo esc_attr($nickname); ?>">

          <!-- Bio Section -->
          <h3>About Me</h3>
          <label for="artist_bio">Bio:</label>
          <textarea name="artist_bio"><?php echo esc_textarea($artist_bio); ?></textarea>

          <!-- Artist Type Section -->
          <label for="artist_type">Artist Type:</label>
          <select name="artist_type">
            <option value="singer" <?php selected($artist_type, 'singer'); ?>>Singer</option>
            <option value="producer" <?php selected($artist_type, 'producer'); ?>>Producer</option>
            <option value="instrumentalist" <?php selected($artist_type, 'instrumentalist'); ?>>Instrumentalist</option>
            <option value="dj" <?php selected($artist_type, 'dj'); ?>>DJ</option>
          </select>

          <!-- Profile Picture Section -->
          <label for="profile_picture">Profile Picture:</label>
          <input type="file" name="profile_picture">
          <?php if ($profile_picture_url) : ?>
            <img src="<?php echo esc_url($profile_picture_url); ?>" alt="Profile Picture" width="100">
          <?php endif; ?>

          <input type="submit" name="update_profile" value="Update Profile">
        </form>
      <?php else : ?>
        <!-- Profile View Mode -->
        <p><a href="?edit=true">Edit Profile</a></p>
        <h3>Personal Information</h3>
        <p><strong>First Name:</strong> <?php echo esc_html($first_name); ?></p>
        <p><strong>Last Name:</strong> <?php echo esc_html($last_name); ?></p>
        <p><strong>Nickname:</strong> <?php echo esc_html($nickname); ?></p>

        <h3>About Me</h3>
        <p><?php echo esc_textarea($artist_bio); ?></p>
        <h3>Artist Type</h3>
        <p><?php echo esc_html($artist_type); ?></p>
        <?php if ($profile_picture_url) : ?>
          <h3>Profile Picture</h3>
          <img src="<?php echo esc_url($profile_picture_url); ?>" alt="Profile Picture" width="100">
        <?php endif; ?>
      <?php endif; ?>

    <?php else : ?>
      <p>Please <a href="<?php echo wp_login_url(); ?>">log in</a> to access your profile.</p>
    <?php endif; ?>
  </div>
</div>