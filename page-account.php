<?php
/* Template Name: Account Page */

get_header();

// Ensure user is logged in and has permission to view this page
if (!is_user_logged_in() || !current_user_can('read')) {
  echo '<p>' . esc_html__('You must be logged in to view this page.', 'devrgenmusic') . '</p>';
  get_footer();
  exit;
}

$current_user_id = get_current_user_id();

// Initialize message holders
$success = '';
$errors = [];

/**
 * Handle account form submission
 */
if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['account_form_submitted'])) {
  // Verify nonce for security
  if (isset($_POST['account_form_nonce']) && wp_verify_nonce($_POST['account_form_nonce'], 'update_account')) {

    // Sanitize user input
    $email = sanitize_email($_POST['user_email']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $password = $_POST['user_pass']; // Do not sanitize passwords
    $confirm_password = $_POST['register_confirm_password'];

    // Validate email format
    if (!is_email($email)) {
      $errors[] = esc_html__('Please enter a valid email address.', 'devrgenmusic');
    }

    // Check for email conflict with other users
    $existing_user = get_user_by('email', $email);
    if ($existing_user && $existing_user->ID != $current_user_id) {
      $errors[] = esc_html__('This email address is already in use.', 'devrgenmusic');
    }

    // Validate password (if provided)
    if (!empty($password)) {
      if (strlen($password) < 6) {
        $errors[] = esc_html__('Password must be at least 6 characters.', 'devrgenmusic');
      }
      if ($password !== $confirm_password) {
        $errors[] = esc_html__('Passwords do not match.', 'devrgenmusic');
      }
    }

    // Proceed if no errors
    if (empty($errors)) {
      // Update name fields
      update_user_meta($current_user_id, 'first_name', $first_name);
      update_user_meta($current_user_id, 'last_name', $last_name);

      // Get current email for comparison
      $user = wp_get_current_user();
      $current_email = $user->user_email;

      // Prepare user data for update
      $userdata = [
        'ID' => $current_user_id,
        'user_email' => $email,
      ];

      // Include password if provided
      if (!empty($password)) {
        $userdata['user_pass'] = $password;
      }

      // Update user
      $user_id = wp_update_user($userdata);

      if (is_wp_error($user_id)) {
        // Log error and notify user
        error_log(print_r($user_id->get_error_messages(), true));
        $errors[] = esc_html__('There was an error updating your account.', 'devrgenmusic');
      } else {
        $success = esc_html__('Account updated successfully.', 'devrgenmusic');

        // Send notifications if email changed
        if ($current_email !== $email) {
          $site_name = get_bloginfo('name');
          $headers = ['Content-Type: text/plain; charset=UTF-8'];

          // Email to new address
          $subject_new = __('Your email address was changed', 'devrgenmusic');
          $message_new = sprintf(
            __("Hello %s,\n\nYour email address on %s has been changed to: %s\n\nIf you did not make this change, please contact support.", 'devrgenmusic'),
            $user->display_name,
            $site_name,
            $email
          );
          wp_mail($email, $subject_new, $message_new, $headers);

          // Email to old address
          $subject_old = __('Your email address was changed', 'devrgenmusic');
          $message_old = sprintf(
            __("Hello %s,\n\nYour account email on %s was changed from this address to: %s\n\nIf you did not make this change, please contact support immediately.", 'devrgenmusic'),
            $user->display_name,
            $site_name,
            $email
          );
          wp_mail($current_email, $subject_old, $message_old, $headers);
        }

        // Redirect to avoid resubmission
        wp_safe_redirect(esc_url(get_permalink()));
        exit;
      }
    }
  } else {
    $errors[] = esc_html__('Security check failed.', 'devrgenmusic');
  }
}

// Load user data for form prefill
$user = wp_get_current_user();
$email = $user->user_email;
$first_name = get_user_meta($current_user_id, 'first_name', true);
$last_name = get_user_meta($current_user_id, 'last_name', true);
?>

<!-- Page Banner -->
<div class="page-banner">
  <div class="page-banner__bg-image my-banner-bg-image">
    <img src="<?php echo esc_url(get_theme_file_uri('/images/pageBanner.jpg')); ?>" alt="<?php echo esc_attr__('Page banner background', 'devrgenmusic'); ?>" loading="lazy">
  </div>

  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title"><?php echo esc_html(get_the_title()); ?></h1>
    <div class="page-banner__intro">
      <!-- Optional intro content -->
    </div>
  </div>
</div>

<!-- Account Form Section -->
<div class="container container--narrow page-section">
  <div class="account-page-content generic-content">

    <!-- Success or error message -->
    <?php if ($success): ?>
      <div class="notice success"><?php echo esc_html($success); ?></div>
    <?php elseif (!empty($errors)): ?>
      <div class="notice error">
        <?php foreach ($errors as $error) : ?>
          <p><?php echo esc_html($error); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Account Update Form -->
    <form method="post" class="acf-account-form" novalidate>
      <?php wp_nonce_field('update_account', 'account_form_nonce'); ?>
      <input type="hidden" name="account_form_submitted" value="1" />

      <div class="acf-form-fields">
        <p>
          <label for="user_email"><?php esc_html_e('Email', 'devrgenmusic'); ?></label><br>
          <input type="email" name="user_email" id="user_email" value="<?php echo esc_attr($email); ?>" required />
        </p>

        <p>
          <label for="first_name"><?php esc_html_e('First Name', 'devrgenmusic'); ?></label><br>
          <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($first_name); ?>" />
        </p>

        <p>
          <label for="last_name"><?php esc_html_e('Last Name', 'devrgenmusic'); ?></label><br>
          <input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($last_name); ?>" />
        </p>

        <p>
          <label for="user_pass"><?php esc_html_e('New Password', 'devrgenmusic'); ?></label><br>
          <input type="password" name="user_pass" id="user_pass" value="" autocomplete="new-password" />
        </p>

        <p>
          <label for="register_confirm_password"><?php esc_html_e('Confirm Password', 'devrgenmusic'); ?></label><br>
          <input type="password" name="register_confirm_password" id="register_confirm_password" value="" autocomplete="new-password" />
        </p>
      </div>

      <p>
        <button type="submit"><?php esc_html_e('Update Account', 'devrgenmusic'); ?></button>
      </p>
    </form>
  </div>
</div>

<?php get_footer(); ?>