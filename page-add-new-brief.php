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
  $genre             = sanitize_text_field($_POST['genre']);
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
?>

<?php while (have_posts()) {
  the_post(); ?>
  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php the_title(); ?></h1>
    </div>
  </div>

  <div class="container container--narrow page-section">
    <div class="generic-content">
      <form method="post" action="">

        <?php wp_nonce_field('submit_brief_action', 'brief_nonce'); ?>

        <label for="brief_title">Title</label>
        <input type="text" name="brief_title" id="brief_title" required>

        <label for="musician_type">What type of musician are you looking for?</label>
        <input type="text" name="musician_type" id="musician_type" required>

        <label for="brief_description">Brief description</label>
        <textarea name="brief_description" id="brief_description" required></textarea>

        <?php
        $genre_field = get_field_object('genre'); // Assuming you're using ACF
        if ($genre_field && isset($genre_field['choices'])): ?>
          <label for="genre">Genre</label>
          <div>
            <?php foreach ($genre_field['genre'] as $value => $label): ?>
              <label>
                <input type="checkbox" name="genre[]" value="<?php echo esc_attr($value); ?>"> <?php echo esc_html($label); ?>
              </label><br>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <label for="brief_deadline">Deadline</label>
        <input type="date" name="brief_deadline" id="brief_deadline" required>

        <label for="brief_budget">Budget</label>
        <input type="number" name="brief_budget" id="brief_budget" required>

        <input type="submit" name="submit_brief" value="Submit Brief">
      </form>

    </div>
  </div>
<?php }
get_footer();
?>