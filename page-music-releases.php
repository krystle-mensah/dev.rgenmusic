<?php

get_header();

while (have_posts()) {
  the_post(); ?>

  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php the_title(); ?></h1>
      <div class="page-banner__intro">
        <p>THIS PAGE SHOWED DISPLAY ALL RELEASES</p>
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
      <?php
      //most recent music release
      $homepageMusicReleases = new WP_Query(array(
        'posts_per_page' => 2,
        'post_type' => 'music_release'
      ));
      while ($homepageMusicReleases->have_posts()) {
        $homepageMusicReleases->the_post(); ?>
        <div class="event-summary">
          <a class="event-summary__date t-center" href="#">
            <span class="event-summary__month">Mar</span>
            <span class="event-summary__day">25</span>
          </a>
          <div class="event-summary__content">
            <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
            <p><?php echo wp_trim_words(get_the_content(), 18); ?> <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a></p>
          </div>
        </div>
      <?php }
      ?>
    </div>

  </div>

<?php }

get_footer();

?>