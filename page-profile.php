<?php
/*
Template Name: Profile Page
*/
get_header();

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

if (!$user_id) {
  wp_redirect(wp_login_url());
  exit;
}

// Get user meta
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$nickname = get_user_meta($user_id, 'nickname', true);
$profile_picture_id = get_user_meta($user_id, 'profile_picture', true);
$profile_picture_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : '';

while (have_posts()) {
  the_post(); ?>

  <div class="page-banner">
    <?php
    $cover_photo_id = get_user_meta($user_id, 'cover_photo', true);
    $cover_photo_url = $cover_photo_id ? wp_get_attachment_url($cover_photo_id) : get_theme_file_uri('/images/pageBanner.jpg');
    ?>
    <div class="page-banner__bg-image cover-photo" style="background-image: url('<?php echo esc_url($cover_photo_url); ?>');"></div>

    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php the_title(); ?></h1>
      <div class="page-banner__intro">
        <p>THIS PAGE IS POWERED BY PAGE.PHP</p>
      </div>
    </div>
  </div>

  <div class="container container--narrow page-section">
    <div class="profile-container">

      <div class="profile-header">
        <div class="profile-picture">
          <?php if ($profile_picture_url): ?>
            <img src="<?php echo esc_url($profile_picture_url); ?>" alt="Profile Picture" class="profile-pic">
          <?php else: ?>
            <p>No profile picture uploaded.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- View Mode -->
      <p><a href="<?php echo site_url('/edit-profile/'); ?>">Edit Profile</a></p>
      <p><strong>First Name:</strong> <?php echo esc_html($first_name); ?></p>
      <p><strong>Last Name:</strong> <?php echo esc_html($last_name); ?></p>
      <p><strong>Nickname:</strong> <?php echo esc_html($nickname); ?></p>

    </div>

    <div class="generic-content">
      <?php the_content(); ?>
    </div>
  </div>

<?php }

get_footer();
?>