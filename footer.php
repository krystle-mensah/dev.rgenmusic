<footer class="site-footer">
  <div class="site-footer__inner container container--narrow">
    <div class="group">
      <div class="site-footer__col-one">
        <?php
        if (function_exists('the_custom_logo') && has_custom_logo()) { ?>
          <div class="site-logo float-left">
            <?php the_custom_logo(); ?>
          </div>
        <?php } else { ?>
          <h1 class="school-logo-text float-left">
            <a href="<?php echo esc_url(site_url()); ?>"><strong>Rgen</strong> Music</a>
          </h1>
        <?php } ?>
      </div>

      <!--
      <div class="site-footer__col-two-three-group">
        <div class="site-footer__col-two">
          <h3 class="headline headline--small">Explore</h3>
          <nav class="nav-list">
            <ul>
              <li><a href="<?php echo esc_url(home_url('/about-us/')); ?>">About Us</a></li>
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
              <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy</a></li>
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
              <!-- <a href="#" class="social-color-facebook" aria-label="Facebook">
                <i class="fa fa-facebook" aria-hidden="true"></i>
              </a> -->
            </li>
            <li>
              <!-- <a href="#" class="social-color-twitter" aria-label="Twitter">
                <i class="fa fa-twitter" aria-hidden="true"></i>
              </a> -->
            </li>
            <li>
              <!-- <a href="#" class="social-color-youtube" aria-label="YouTube">
                <i class="fa fa-youtube" aria-hidden="true"></i>
              </a> -->
            </li>
            <li>
              <!-- <a href="#" class="social-color-linkedin" aria-label="LinkedIn">
                <i class="fa fa-linkedin" aria-hidden="true"></i>
              </a> -->
            </li>
            <li>
              <a href="#" class="social-color-instagram" aria-label="Instagram">
                <i class="fa fa-instagram" aria-hidden="true"></i>
              </a>
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
