<footer class="site-footer">
  <div class="site-footer__inner container container--narrow">
    <div class="group">
      <div class="site-footer__col-one">
        <?php
        // Check if the 'the_custom_logo' function exists (to ensure theme supports custom logos)
        // and if a custom logo has been set in the WordPress customizer.

        if (function_exists('the_custom_logo') && has_custom_logo()) { ?>
          <!-- Display the custom logo inside a div with appropriate classes -->
          <div class="site-logo float-left">
            <?php the_custom_logo(); ?>
          </div>
        <?php } else { ?>
          <h1 class="school-logo-text float-left">
            <a href="<?php echo site_url(); ?>"><strong>Rgen</strong> Music</a>
          </h1>
        <?php } ?>
        <!-- <p><a class="site-footer__link" href="#">555.555.5555</a></p> -->
      </div>

      <!--
      <div class="site-footer__col-two-three-group">
        <div class="site-footer__col-two">
          <h3 class="headline headline--small">Explore</h3>
          <nav class="nav-list">
            <ul>
              <li><a href="<?php echo site_url('index.php/about-us') ?>">About Us</a></li>
              <li><a href="#">Programs</a></li>
              <li><a href="#">Events</a></li>
              <li><a href="#">Campuses</a></li>
            </ul>
          </nav>
        </div>

        <div class="site-footer__col-three">
          <h3 class="headline headline--small">Learn</h3>
          <nav class="nav-list">
            <ul>
              <li><a href="#">Legal</a></li>
              <li><a href="<?php echo site_url('index.php/privacy-policy') ?>">Privacy</a></li>
              <li><a href="#">Careers</a></li>
            </ul>
          </nav>
        </div>
      </div>
      -->

      <div class="site-footer__col-four">
        <!-- <h3 class="headline headline--small">Connect With Us</h3> -->
        <nav>
          <ul class="min-list social-icons-list group">
            <li>
              <!-- <a href="#" class="social-color-facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a> -->
            </li>
            <li>
              <!-- <a href="#" class="social-color-twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a> -->
            </li>
            <li>
              <!-- <a href="#" class="social-color-youtube"><i class="fa fa-youtube" aria-hidden="true"></i></a> -->
            </li>
            <li>
              <!-- <a href="#" class="social-color-linkedin"><i class="fa fa-linkedin" aria-hidden="true"></i></a> -->
            </li>
            <li>
              <a href="#" class="social-color-instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>