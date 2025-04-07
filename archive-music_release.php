<?php

get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Releases</h1>
    <div class="page-banner__intro">
      <p>See what is going on in rgen music releases.</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">
  <div class="releases-grid">
    <?php
    $music_releases = new WP_Query(array(
      'post_type' => 'music_release',
      'posts_per_page' => 2,
      'orderby' => 'date',
      'order' => 'DESC'
    ));

    $music_releases = new WP_Query($music_releases);
    if ($music_releases->have_posts()) :
      while ($music_releases->have_posts()) : $music_releases->the_post();
        $music_release_cover = get_field('cover_art_image');
        $artist = get_field('collaborators');
        $release_date = get_field('music_release_date');
        $preview = get_field('audio_preview');
    ?>
        <!-- need class below for styling -->
        <div class="post-item">
          <?php if ($music_release_cover): ?>
            <img src="<?php echo esc_url($music_release_cover['url']); ?>" alt="<?php the_title(); ?>" class="release-cover">
          <?php endif; ?>
          <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <p class="release-artist"><?php echo esc_html($music_release_cover); ?></p>
          <div class="metabox">
            <?php
            // echo '<p>Released by ' . get_the_author_posts_link() . ' on ' . get_the_time('n.j.y') . ' in ' . get_the_category_list(', ') . '</p>';
            ?>
            <p class="release-date">Released: <?php echo esc_html($release_date); ?></p>
            <?php if ($preview): ?>
              <audio controls src="<?php echo esc_url($preview); ?>"></audio>
            <?php endif; ?>
          </div>

          <div class="generic-content">
            <p><?php echo wp_trim_words(get_the_content(), 18); ?></p>

            <?php // <p><a class="btn btn--blue" href="<?php the_permalink();">View Single &raquo;</a></p> 
            ?>
            <a href="<?php the_permalink(); ?>" class="release-link">View Track</a>
          </div>
        </div>
    <?php
      endwhile;
      wp_reset_postdata();
    else :
      echo '<p>No new releases found.</p>';
    endif;
    ?>
  </div>


  <?php
  echo paginate_links();
  ?>
  <hr class="section-break">
  <p>Looking for a recap of past music releases? <a href="<?php echo site_url('/past-music-releases'); ?>">check out our past music releases archive</a>. </p>
</div>

<?php get_footer();

?>