<?php
//this is from archive.php
get_header(); ?>
<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Past Briefs</h1>
    <div class="page-banner__intro">
      <p>A recap of our past briefs.</p>
    </div>
  </div>
</div>
<div class="container container--narrow page-section">
  <?php
  // Get today's date formatted as "YearMonthDay" (e.g., "20250324")
  $today = date('Ymd');

  // Create a new WP_Query instance to fetch 'brief' posts
  // that have a 'brief_date' meta value older than today's date.
  $pastBriefs = new WP_Query(array(
    // Enable pagination and get the current page number, defaulting to page 1
    'paged' => get_query_var('paged', 1),
    // Set the custom post type to 'brief'
    'post_type' => 'brief',
    // Define the meta key to use for ordering
    'meta_key' => 'brief_date',
    // Order posts based on the meta value specified above
    'orderby' => 'meta_value',
    // Order the results in ascending order (oldest first)
    'order' => 'ASC',
    // Set up a meta query to filter posts based on 'brief_date'
    'meta_query' => array(
      array(
        // Specify the meta key to compare
        'key' => 'brief_date',
        // Use a comparison operator to only select dates before today
        'compare' => '<',
        // Compare against the formatted current date
        'value' => $today,
        // Ensure the comparison is numeric (since dates are stored as numbers in 'Ymd' format)
        'type' => 'numeric'
      )
    )
  ));
  while ($pastBriefs->have_posts()) {
    $pastBriefs->the_post(); ?>
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
            } ?> <a href="<?php the_permalink(); ?>" class="nu gray">Read More</a></p>
        <a class="event-summary__date t-center" href="#">
          <span class="event-summary__month"><?php $music_releaseDate = new DateTime(get_field('music_release_date'));
                                              echo $music_releaseDate->format('M') ?> </span>
          <span><?php echo $music_releaseDate->format('d') ?></span>
          <span class="event-summary__day"><?php echo $music_releaseDate->format('Y') ?></span>
        </a>
      </div>
    </div>
  <?php }
  echo paginate_links(array(
    'total' => $pastBriefs->max_num_pages
  ));
  ?>
</div>

<?php get_footer();

?>