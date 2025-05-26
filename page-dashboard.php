<?php
/*
Template Name: Dashboard
*/
get_header();

while (have_posts()) {

  the_post(); ?>

  <div class="page-banner">

    <div class="page-banner__bg-image my-banner-bg-image">
      <img src="<?php echo get_theme_file_uri('/images/pageBanner.jpg'); ?>" alt="Page banner background" loading="lazy">
    </div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php the_title(); ?></h1>
      <div class="page-banner__intro">
        <p>Welcome to Your Dashboard</p>
      </div>
    </div>
  </div>

  <div class="container container--narrow page-section">

    <div class="generic-content">
      <p>Manage your profile, music releases, and more.</p>
      <?php the_content(); ?>
    </div>
  </div>

  </div>

<?php }

get_footer();

?>