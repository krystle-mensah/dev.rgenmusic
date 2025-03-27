<?php

get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Past Music Releases</h1>
    <div class="page-banner__intro">
      <p>A recap of our past Music Releases.</p>
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
    'order' => 'ASC',
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
    <div class="event-summary">
      <a class="event-summary__date t-center" href="#">
        <span class="event-summary__month"><?php $music_releaseDate = new DateTime(get_field('music_release_date'));
                                            echo $music_releaseDate->format('M') ?> </span>
        <span><?php echo $music_releaseDate->format('d') ?></span>
        <span class="event-summary__day"><?php echo $music_releaseDate->format('Y') ?></span>
      </a>
      <div class="event-summary__content">
        <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
        <p><?php echo wp_trim_words(get_the_content(), 18); ?> <a href="<?php the_permalink(); ?>" class="nu gray">Listen Now</a></p>
      </div>
    </div>
  <?php }
  echo paginate_links(array(
    'total' => $pastMusicReleases->max_num_pages
  ));
  ?>
</div>

<?php get_footer();

?>