<?php
//This is index.php. Blog screen
get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image my-banner-bg-image">
    <img src="<?php echo get_theme_file_uri('/images/pageBanner.jpg'); ?>" alt="Page banner background" loading="lazy">
  </div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">RGENMUSIC BLOG</h1>
    <div class="page-banner__intro">
      <p>Your Gateway to Music Culture.</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">
  <?php
  // Start the loop: checks if there are any posts available
  while (have_posts()) {
    // Set up the current post data. This prepares template tags (e.g., the_title(), the_permalink()) to output information about the post.
    the_post(); ?>
    <div class="post-item">
      <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
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