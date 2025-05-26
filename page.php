<?php

get_header(); // Include the header.php template

// Start the WordPress Loop to process posts/pages
while (have_posts()) {
  the_post(); // Set up the global post data for the current post
?>

  <!-- Page Banner Section -->
  <div class="page-banner">

    <div class="page-banner__bg-image my-banner-bg-image">
      <img src="<?php echo get_theme_file_uri('/images/pageBanner.jpg'); ?>" alt="Page banner background" loading="lazy">
    </div>

    <!-- Banner content container -->
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php the_title(); // Display the page title 
                                      ?></h1>

      <div class="page-banner__intro">
        <!-- Optional intro content area -->
        <!-- <p>THIS IS A PAGE </p> -->
      </div>
    </div>
  </div>

  <!-- Main content section -->
  <div class="container container--narrow page-section">

    <!-- 
      Optional metabox and page links section (currently commented out)
      
      <div class="metabox metabox--position-up metabox--with-home-link">
        <p>
          <a class="metabox__blog-home-link" href="#">
            <i class="fa fa-home" aria-hidden="true"></i> Back to About Us
          </a> 
          <span class="metabox__main">Our History</span>
        </p>
      </div>

      <div class="page-links">
        <h2 class="page-links__title"><a href="#">About Us</a></h2>
        <ul class="min-list">
          <li class="current_page_item"><a href="#">Our History</a></li>
          <li><a href="#">Our Goals</a></li>
        </ul>
      </div>
    -->

    <!-- Generic page content -->
    <div class="generic-content">
      <?php the_content(); // Display the main page content 
      ?>
    </div>

  </div>

<?php }

get_footer(); // Include the footer.php template

?>