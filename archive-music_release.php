<?php

get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/pageBanner.jpg') ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Releases</h1>
    <div class="page-banner__intro">
      <p>"Our Complete Catalogue, One Track at a Time."</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">
  <div class="releases-grid">
    <?php
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;

    $music_releases = new WP_Query(array(
      'post_type' => 'music_release',
      'posts_per_page' => 2,
      'orderby' => 'date',
      'order' => 'DESC',
      'paged' => $paged
    ));

    if ($music_releases->have_posts()) :
      while ($music_releases->have_posts()) : $music_releases->the_post();
        $cover_art_image = get_field('cover_art_image');
        $artist = get_field('collaborators');
        $release_date = get_field('music_release_date');
        $preview = get_field('audio_preview');
    ?>
        <div class="release-card">
          <?php if ($cover_art_image): ?>
            <img src="<?php echo esc_url($cover_art_image['url']); ?>" alt="<?php the_title(); ?>" class="release-cover">
          <?php endif; ?>
          <h2 class="track-heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <p class="release-artist"><?php echo esc_html($cover_art_image); ?></p>
          <div class="metabox">
            <?php
            echo '<p>Released: ' . get_the_author_posts_link() . ' on ' . esc_html((new DateTime($release_date))->format('n.j.y')) . get_the_category_list(', ') . '</p>';
            ?>
            <?php if ($preview): ?>
              <audio controls src="<?php echo esc_url($preview); ?>"></audio>
            <?php endif; ?>
          </div>

          <div class="generic-content"><?php get_the_post_thumbnail(); ?>
            <p><?php echo wp_trim_words(get_the_content(), 18); ?></p>
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
  echo paginate_links(array(
    'total' => $music_releases->max_num_pages
  ));
  ?>
  <hr class="section-break">
  <p>Looking for a recap of past music releases? <a href="<?php echo site_url('/past-music-releases'); ?>">check out our past music releases archive</a>. </p>
</div>

<?php get_footer(); ?>