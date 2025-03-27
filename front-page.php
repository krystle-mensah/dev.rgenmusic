<?php get_header(); ?>
<div class="video-background">
  <video autoplay loop muted playsinline>
    <source src="<?php echo get_template_directory_uri(); ?>/videos/homepage-vid.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  <!-- <div class="overlay-text">
    <h1 class="main-title">
      <span class="word word1">create</span>
      <span class="word word2">collaborate</span>
      <span class="word word3">elevate</span>
    </h1>
    <a href="<?php echo get_permalink($post->ID); ?>" class="btn--large btn btn--orange home-cta-button">Explore</a>
  </div> -->
  <!-- Must recent release -->
  <div class="full-width-split group">
    <div class="full-width-split__one">
      <div class="full-width-split__inner">
        <h2 class="headline headline--small-plus t-center">Lastest Releases</h2>
        <?php
        //This line gets the current date formatted as YYYYMMDD (e.g., 20250105) and stores it in the $today variable. This format is used for comparing date values in the meta_query.
        $today = date('Ymd');
        //This creates a new WP_Query object and stores it in the $homepageMusicReleases variable. The array inside the WP_Query defines the parameters for the query, which retrieves specific posts.
        $homepageMusicReleases = new WP_Query(array(
          //This limits the number of posts returned to 2. It shows only the two most recent releases.
          'posts_per_page' => 2,
          //This specifies that the query should only return posts of the custom post type
          'post_type' => 'music_release',
          //This tells WordPress to sort the posts based on the value of the music_release_date custom field (a meta field attached to the post).
          'meta_key' => 'music_release_date',
          //This sets the sorting criteria to be based on the meta field music_release_date. This will order the posts by the numeric value of the music_release_date.
          'orderby' => 'meta_value',
          //The posts will be ordered in ascending order (ASC), meaning the earliest release date will appear first.
          'order' => 'ASC',
          //This part defines the conditions for the meta_key (music_release_date). The query will only return posts where the music_release_date is greater than or equal to today's date (>=).
          'meta_query' => array(
            array(
              //Specifies the custom field to query.
              'key' => 'music_release_date',
              //Ensures the post's release date is today or in the future.
              'compare' => '>=',
              //Compares the music_release_date to the value of $today (the current date).
              'value' => $today,
              //Treats the music_release_date as a numeric value for the comparison, which is crucial when comparing date strings like YYYYMMDD.
              'type' => 'numeric'
            )
          )
        ));
        while ($homepageMusicReleases->have_posts()) {
          $homepageMusicReleases->the_post(); ?>
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
        ?>
        <p class="t-center no-margin"><a href="<?php echo get_post_type_archive_link('music_release') ?>" class="btn btn--blue">View All Releases</a></p>
      </div>
    </div>
    <div class="full-width-split__two hide">
      <div class="full-width-split__inner">
        <h2 class="headline headline--small-plus t-center">From Our Blogs</h2>
        <?php
        $homepagePosts = new WP_Query(array(
          'posts_per_page' => 2
        ));
        while ($homepagePosts->have_posts()) {
          $homepagePosts->the_post(); ?>
          <div class="event-summary">
            <a class="event-summary__date event-summary__date--beige t-center" href="<?php the_permalink(); ?>">
              <span class="event-summary__month"><?php the_time('M'); ?></span>
              <span class="event-summary__day"><?php the_time('d'); ?></span>
            </a>
            <div class="event-summary__content">
              <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
              <p><?php if (has_excerpt()) {
                    echo get_the_excerpt();
                  } else {
                    echo wp_trim_words(get_the_content(), 18);
                  } ?> <a href="<?php the_permalink(); ?>" class="nu gray">Read more</a></p>
            </div>
          </div>
        <?php }
        wp_reset_postdata();
        ?>
        <p class="t-center no-margin"><a href="<?php echo site_url('index.php/blog'); ?>" class="btn btn--yellow">View All Blog Posts</a></p>
      </div>
    </div>
  </div>
</div> <!-- VIDEO -->
<?php get_footer(); ?>