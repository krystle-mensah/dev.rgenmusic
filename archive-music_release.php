<?php

get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">All Music Releases</h1>
    <div class="page-banner__intro">
      <p>See what is going on in rgen music releases.</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">
  <?php
  $homepageMusicReleases = new WP_Query(array(
    'posts_per_page' => 2,
    'post_type' => 'music_release',
  ));

  while ($homepageMusicReleases->have_posts()) {
    $homepageMusicReleases->the_post(); ?>
    <div class="post-item">
      <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

      <div class="metabox">
        <p>Released by <?php the_author_posts_link(); ?> on <?php the_time('n.j.y'); ?> in <?php echo get_the_category_list(', '); ?></p>
      </div>

      <div class="generic-content">
        <p><?php echo wp_trim_words(get_the_content(), 18); ?></p>
        <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">View Single &raquo;</a></p>
      </div>

    </div>
  <?php }
  echo paginate_links();
  ?>
  <p>Looking for a recap of past music releases? <a href="<?php site_url('index.php/past-music-releases'); ?>">check out our past music releases archive</a>. </p>
</div>

<?php get_footer();

?>