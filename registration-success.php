<?php
/* Template Name: Registration Success */

get_header();
?>

<div class="registration-success-container">
  <h2>Welcome to RGenMusic!</h2>
  <p>Your registration was successful, and you are now logged in.</p>
  <p>Start exploring the platform and connect with other artists.</p>

  <a href="<?php echo esc_url(home_url('index.php/dashboard/')); ?>" class="button">Go to Dashboard</a>
  <a href="<?php echo esc_url(home_url('index.php/profile/')); ?>" class="button">View Your Profile</a>
</div>

<?php
get_footer();
?>