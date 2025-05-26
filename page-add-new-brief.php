<?php

/**
 * Template Name: Add new brief
 * Template Post Type: page
 */

get_header();

// Handle form submission
if (isset($_POST['submit_brief']) && isset($_POST['brief_nonce']) && wp_verify_nonce($_POST['brief_nonce'], 'submit_brief_action')) {
  // Optional: Require user to be logged in
  if (!is_user_logged_in()) {
    wp_die('You must be logged in to submit a brief.');
  }

  // Sanitize inputs
  $brief_title       = sanitize_text_field($_POST['brief_title']);
  $musician_type     = sanitize_text_field($_POST['musician_type']);
  $brief_description = sanitize_textarea_field($_POST['brief_description']);
  $genre = isset($_POST['genre']) ? sanitize_text_field($_POST['genre']) : '';
  $brief_deadline    = sanitize_text_field($_POST['brief_deadline']);
  $brief_budget      = sanitize_text_field($_POST['brief_budget']);

  $new_brief = array(
    'post_title'   => $brief_title,
    'post_content' => $brief_description,
    'post_status'  => 'pending', // Or 'publish'
    'post_type'    => 'brief',
  );

  $post_id = wp_insert_post($new_brief);

  if ($post_id) {
    // Save custom fields
    update_post_meta($post_id, 'musician_type', $musician_type);
    update_post_meta($post_id, 'genre', $genre);
    update_post_meta($post_id, 'brief_deadline', $brief_deadline);
    update_post_meta($post_id, 'brief_budget', $brief_budget);

    // Optional: Show a success message
    echo '<div class="notice success">Brief submitted successfully!</div>';
  } else {
    echo '<div class="notice error">Something went wrong. Please try again.</div>';
  }

  // Get current user info
  $current_user = wp_get_current_user();
  $user_email   = $current_user->user_email;
  $user_name    = $current_user->display_name;

  // Send email to user
  $user_subject = 'Your brief was submitted!';
  $user_message = "Hi $user_name,\n\nThanks for submitting your brief titled '$brief_title'. We'll review it shortly.\n\nrgenmusic.com";
  wp_mail($user_email, $user_subject, $user_message);

  // Send email to admin
  $admin_email   = get_option('admin_email');
  $admin_subject = 'New Brief Submission on rgenmusic.com';
  $admin_headers = array('Content-Type: text/html; charset=UTF-8');

  $admin_message  = "<p>A new brief has been submitted on <strong>rgenmusic.com</strong>.</p>";
  $admin_message .= "<p><strong>Title:</strong> " . esc_html($brief_title) . "<br>";
  $admin_message .= "<strong>Submitted by:</strong> " . esc_html($user_name) . " (" . esc_html($user_email) . ")</p>";
  $admin_message .= "<p><a href='" . admin_url('edit.php?post_type=brief') . "'>Click here to review it</a></p>";

  wp_mail($admin_email, $admin_subject, $admin_message, $admin_headers);

  wp_redirect(home_url('/brief-thank-you/'));
  exit;
}

// Set default values to avoid undefined variable warnings
$brief_title       = '';
$musician_type     = '';
$brief_description = '';
$genre             = '';
$brief_deadline    = '';
$brief_budget      = '';

?>
<?php
$homepage_briefs = new WP_Query(array(
  // 'posts_per_page' => 2,
  'post_type' => 'brief'

));
?>
<?php while (have_posts()) {
  the_post(); ?>
  <div class="page-banner">
    <div class="page-banner__bg-image my-banner-bg-image">
      <img src="<?php echo get_theme_file_uri('/images/pageBanner.jpg'); ?>" alt="Page banner background" loading="lazy">
    </div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php the_title(); ?></h1>
    </div>
  </div>

  <div class="container container--narrow page-section">
    <div class="briefs-form">
      <form method="post" action="">

        <!-- Brief title -->
        <label for="brief_title"><strong>Title:</strong></label>
        <input type="text" name="brief_title" id="brief_title" required value="<?php echo esc_attr($brief_title); ?>">

        <!-- musician type -->
        <label for="musician_type"><strong>What type of musician are you looking for:</strong></label>
        <input type="text" name="musician_type" id="musician_type" required value="<?php echo esc_html($musician_type); ?>">

        <!-- Genre -->
        <label for="genre"><strong>Genre:</strong></label>
        <input type="text" name="genre" id="genre" required value="<?php echo esc_attr($genre); ?>">

        <!-- Deadline -->
        <div>
          <label for="brief_deadline"><strong>Brief Deadline:</strong></label>
          <input type="date" name="brief_deadline" id="brief_deadline" value="<?php echo esc_attr($brief_deadline); ?>" required>
        </div>

        <!-- Budget -->
        <div>
          <p>How much can you pay?:</p>
          <label for="brief_budget"><strong>Budget:</strong></label>
          <input type="number" name="brief_budget" id="brief_budget" value="<?php echo esc_attr($brief_budget); ?>" required>
        </div>

        <!-- Description -->
        <div>
          <label for="brief_description"><strong>Brief Description:</strong></label>
          <textarea name="brief_description" id="brief_description" required><?php echo esc_textarea($brief_description); ?></textarea>
        </div>

        <?php wp_nonce_field('submit_brief_action', 'brief_nonce'); ?>

        <input type="submit" name="submit_brief" value="Submit Brief">
      </form>
    </div>
  </div>

<?php }
get_footer();
?>