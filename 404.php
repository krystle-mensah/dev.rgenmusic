<?php get_header(); ?>

<main id="primary" class="site-main">
  <section class="error-404 not-found">
    <header class="page-header">
      <h1 class="page-title">Page Not Found</h1>
    </header>

    <div class="page-content">
      <p>Sorry, the page you are looking for doesn’t exist or has been moved.</p>
      <a href="<?php echo esc_url(home_url('/')); ?>" class="button">Back to Homepage</a>
    </div>
  </section>
</main>

<?php get_footer(); ?>