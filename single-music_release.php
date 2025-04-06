<?php

get_header();

while (have_posts()) {
  the_post(); ?>
  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php the_title(); ?></h1>
      <div class="page-banner__intro">
        <p>THIS IS A SINGLE POST</p>
      </div>
    </div>
  </div>
  <!-- need to sort out the date -->
  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('music_release'); ?>"><i class="fa fa-music" aria-hidden="true"></i> Music Releases Home</a> <span
          class="metabox__main">Released by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?></span></p>
    </div>

    <div class="generic-content"><?php the_content(); ?></div>

  </div>

<?php }

get_footer();

?>