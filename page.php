<?php

get_header();

// Start the WordPress loop to process posts/pages
while (have_posts()) {
  // Set up the global post data for the current post
  the_post(); ?>

  <!-- Page Banner Section -->
  <div class="page-banner">

    <!-- 
  This <div> serves as the background image container for the page banner.

  - The class "page-banner__bg-image" is likely used for styling the banner background.
  - The `style` attribute sets the background image dynamically using inline CSS.
  - `get_theme_file_uri('/images/pageBanner.jpg')` retrieves the full URL of the "pageBanner.jpg" image located inside the theme's "images" folder.
  - `echo` outputs the correct URL for the image so that it appears as a background image.
  -->

    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php the_title(); ?></h1>
      <div class="page-banner__intro">
        <p>THIS IS A PAGE </p>
      </div>
    </div>
  </div>

  <div class="container container--narrow page-section">
    <!--
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p><a class="metabox__blog-home-link" href="#"><i class="fa fa-home" aria-hidden="true"></i> Back to About Us</a> <span class="metabox__main">Our History</span></p>
    </div>

    <div class="page-links">
      <h2 class="page-links__title"><a href="#">About Us</a></h2>
      <ul class="min-list">
        <li class="current_page_item"><a href="#">Our History</a></li>
        <li><a href="#">Our Goals</a></li>
      </ul>
    </div>
    -->

    <div class="generic-content">
      <?php the_content(); ?>
    </div>

  </div>

<?php }

get_footer();

?>