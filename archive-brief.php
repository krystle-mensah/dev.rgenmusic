<?php
//this is from archive.php
get_header(); ?>
<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">All Briefs</h1>
    <div class="page-banner__intro">
      <p>See whats going on in our world.</p>
    </div>
  </div>
</div>
<div class="container container--narrow page-section">
  <?php
  while (have_posts()) {
    the_post(); ?>
    <div class="event-summary">
      <!-- <a class="event-summary__date t-center" href="#"> -->
      <!-- <span class="event-summary__month"><?php the_field('music_release_date'); ?></span> -->
      <!-- <span class="event-summary__day">25</span> -->
      <!-- </a> -->
      <div class="event-summary__content">
        <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
        <p><?php if (has_excerpt()) {
              echo get_the_excerpt();
            } else {
              echo wp_trim_words(get_the_content(), 18);
            } ?> <a href="<?php the_permalink(); ?>" class="nu gray">Stream Now</a></p>
        <a class="event-summary__date t-center" href="#">
          <span class="event-summary__month"><?php $music_releaseDate = new DateTime(get_field('music_release_date'));
                                              echo $music_releaseDate->format('M') ?> </span>
          <span><?php echo $music_releaseDate->format('d') ?></span>
          <span class="event-summary__day"><?php echo $music_releaseDate->format('Y') ?></span>
        </a>
      </div>
    </div>
  <?php }
  echo paginate_links();
  ?>
  <hr class="section-break">
  <p>Looking for a recap of past briefs? <a href="<?php echo site_url('index.php/past-briefs') ?>">Check out our past briefs archive</a>.</p>
</div>

<?php get_footer();

?>