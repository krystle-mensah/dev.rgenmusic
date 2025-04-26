<?php

/**
 * Template Name: Brief Thank You
 * Template Post Type: page
 */

get_header(); ?>

<?php
while (have_posts()) {

  the_post(); ?>

  <div class="page-banner">

    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"></h1>
      <div class="page-banner__intro">
        <!-- <p>THIS IS A PAGE </p> -->
      </div>
    </div>
  </div>

  <div class="container container--narrow page-section">

    <div class="generic-content">
      <div class="thank-you-container">
        <h1>Thank you for submitting your brief!</h1>
        <p>We will review your submission shortly and get back to you.</p>
      </div>
    </div>

  </div>

<?php }

get_footer();

?>