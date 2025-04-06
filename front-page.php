<?php get_header(); ?>
<div class="video-background">
  <video autoplay loop muted playsinline>
    <source src="<?php echo get_template_directory_uri(); ?>/videos/homepage-vid.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
</div>
<!-- Overlay Text on Top of Video -->
<div class="overlay-text">
  <h1 class="main-title word-rotator">
    <span class="word word1">create</span>
    <span class="word word2">collaborate</span>
    <span class="word word3">elevate</span>
  </h1>
  <p class="t-center no-margin"><a href="<?php echo get_post_type_archive_link('music_release') ?>" class="btn btn--blue">View All Releases</a></p>
</div>



<?php get_footer(); ?>