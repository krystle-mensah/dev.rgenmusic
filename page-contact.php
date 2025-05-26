<?php

get_header();

while (have_posts()) {
  the_post(); ?>
  <!-- contact-page-banner -->
  <div class="page-banner">
    <div class="page-banner__bg-image my-banner-bg-image">
      <img src="<?php echo get_theme_file_uri('/images/pageBanner.jpg'); ?>" alt="Page banner background" loading="lazy">
    </div>

    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php the_title(); ?></h1>
      <div class="page-banner__intro">
        <p>Get in touch with RGENMUSIC</p>
      </div>
    </div>
  </div>

  <div class="container container--narrow page-section">

    <div class="generic-content contact-form-container">
      <?php echo do_shortcode('[wpforms id="81"]'); ?>
    </div>

  </div>

<?php }

get_footer();

?>