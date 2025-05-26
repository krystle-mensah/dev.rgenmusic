<?php

get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image my-banner-bg-image">
    <img src="<?php echo get_theme_file_uri('/images/pageBanner.jpg'); ?>" alt="Page banner background" loading="lazy">
  </div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Past Music Releases</h1>
    <div class="page-banner__intro">
      <p>Recap of our past music releases.</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">
  <?php
  $today = date('Ymd');
  $pastMusicReleases = new WP_Query(array(
    'paged' => get_query_var('paged', 1),
    'post_type' => 'music_release',
    'meta_key' => 'music_release_date',
    'orderby' => 'meta_value',
    'order' => 'DESC',
    'meta_query' => array(
      array(
        'key' => 'music_release_date',
        'compare' => '<',
        'value' => $today,
        'type' => 'numeric'
      )
    )
  ));

  while ($pastMusicReleases->have_posts()) {
    $pastMusicReleases->the_post(); ?>
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
  echo paginate_links(array(
    'total' => $pastMusicReleases->max_num_pages
  ));
  //test
  //echo 'Total Pages: ' . $pastMusicReleases->max_num_pages;
  ?>
</div>
<?php get_footer(); ?>