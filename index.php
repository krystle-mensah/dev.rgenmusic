<?php

get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Rgen music Blog</h1>
    <div class="page-banner__intro">
      <p>Your Gateway to Music Culture. This is index.php. Blog screen</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">
  <?php
  // Start the loop: checks if there are any posts available
  while (have_posts()) {
    // Set up the current post data. This prepares template tags (e.g., the_title(), the_permalink()) to output information about the post.
    the_post(); ?>
    <!-- Container for an individual post item -->
    <div class="post-item">
      <!-- Post title: displayed as a clickable headline linking to the single post page -->
      <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
      <!-- Meta information about the post -->
      <div class="metabox">
        <!-- Shows the author with a link to their archive page, Lists all categories the post belongs to, separated by commas -->
        <p>Posted by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?></p>
      </div>
      <!-- Excerpt of the post and a link to continue reading -->
      <div class="generic-content">
        <?php the_excerpt(); ?>
        <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">Continue reading &raquo;</a></p>
      </div>

    </div>
  <?php }
  // End the loop
  echo paginate_links();
  ?>
</div>

<?php get_footer();



?>