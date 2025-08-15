<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>WaterPets: Aquatic Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tailwind css -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Alpine js -->
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <link rel="stylesheet" href="css/home_style.css">

  <!-- AOS Animate On Scroll -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <!-- Swiper JS library -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

</head>

<body>

  <section class="section-bg">
    <?php include 'home_header.php'; ?>
    <!-- BUBBLES CONTAINER -->
    <div class="bubbles-container">
      <div class="bubble" style="left: 10%; width: 30; height: 25px; animation-delay: 0s;"></div>
      <div class="bubble" style="left: 25%; width: 25px; height: 25px; animation-delay: 2s;"></div>
      <div class="bubble" style="left: 40%; width: 10px; height: 10px; animation-delay: 4s;"></div>
      <div class="bubble" style="left: 30%; width: 15px; height: 15px; animation-delay: 1s;"></div>
      <div class="bubble" style="left: 60%; width: 35px; height: 35px; animation-delay: 3s;"></div>
      <div class="bubble" style="left: 90%; width: 25px; height: 25px; animation-delay: 5s;"></div>
    </div>

    


    <!-- Content SECTION -->
    <section class="relative h-[90vh] flex items-center justify-between px-10">

      <!-- Decorative Borders -->
      <div class="corner-deco top-left"></div>
      <div class="corner-deco bottom-right"></div>

      <!-- Left Text -->
      <div class="main-content">
        <p class="main-text">WaterPets</p>
        <p class="sub-text">Your One-Stop Aquatic Store!</p>
        <p class="sub-text">Shop variety of aquarium fish, coral reefs and fish care products at one place.</p>

        <!-- Floating Action Button -->
        <div class="floating-button-group">
          <button class="main-circle" onclick="toggleOptions()" data-sound="/sounds/click_pop.mp3">Build Your Dream Aquarium Now</button>

          <a href="/fish.html" class="option-button fish" data-sound="/sounds/click_pop.mp3">Fish</a>
          <a href="/plants.html" class="option-button plants" data-sound="/sounds/click_pop.mp3">Coral Reefs</a>
          <a href="/supplies.html" class="option-button supplies" data-sound="/sounds/click_pop.mp3">Supplies</a>
          <a href="/equipment.html" class="option-button equipment" data-sound="/sounds/click_pop.mp3">Equipment</a>
        </div>

      </div>

      <!-- Right Jellyfish -->
      <img src="/images/jellyfishglow.png" alt="Jellyfish"
        class="h-[400px] w-auto animate-float absolute opacity-90"
        style="right: 100px; bottom: 100px;" />

    </section>

  </section>

  <!-- Latest Offer Section -->
  <section class="latest-offers">
    <div class="latest-offer-container">
      <!-- left side -->
      <div class="offer-left" data-aos="fade-up">
        <div class="latest-offer-text">
          <h2>Special Summer Offer</h2>
          <p>Enjoy up to 15% off on any Betta Fish species with different color patterns. <br> Limited time only!</p>
          <a href="fish.php">Shop Now</a>
        </div>

        <div class="offer-thumbnails" data-aos="fade-up">
          <a href="fish.php?type=betta-red">
            <img src="/images/whitebetta.png" alt="Red Betta">
          </a>
          <a href="fish.php?type=betta-blue">
            <img src="/images/whiteredbetta.png" alt="Blue Betta">
          </a>
          <a href="fish.php?type=betta-white">
            <img src="/images/redmoonbetta.png" alt="White Betta">
          </a>
        </div>
      </div>

      <!-- right side -->
      <div class="latest-offer-box" data-aos="zoom-in-up">
        <video autoplay muted loop playsinline class="rounded-lg shadow-xl w-[450px] h-[600px] object-cover">
          <source src="/videos/bettavideo.mp4" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>

    </div>
  </section>

  <!-- Popular Pick Section -->
  <section class="section-gradient">
    <video autoplay muted loop playsinline class="bg-video">
      <source src="/videos/underwater.mp4">
    </video>

    <div class="feature-product" data-aos="fade-up" data-aos-delay="100">
      <p class="popular-pick-text" data-aos="fade-up">Popular Picks</p>

      <!-- Slider main container -->
      <div class="swiper" data-aos="zoom-in-up" data-aos-duration="1000" data-aos-delay="300">
        <div class="swiper-wrapper">
          <!-- Slides -->
          <div class="swiper-slide">
            <img src="/images/clownfish.jpg" alt="">
            <div class="title">
              <a href="/fish.php#clownfish" data-sound="/sounds/notification_pop.mp3">Clown Fish</a>
            </div>
          </div>

          <div class="swiper-slide">
            <img src="/images/blue-betta.jpg" alt="">
            <div class="title">
              <a href="/fish.php#clownfish" data-sound="/sounds/notification_pop.mp3">Navy Blue Betta</a>
            </div>
          </div>

          <div class="swiper-slide">
            <img src="/images/betta-tank.jpg" alt="">
            <div class="title">
              <a href="/fish.php#clownfish" data-sound="/sounds/notification_pop.mp3">Square Fish Tank</a>
            </div>
          </div>

          <div class="swiper-slide">
            <img src="/images/seahorse.jpg" alt="">
            <div class="title">
              <a href="/fish.php#clownfish" data-sound="/sounds/notification_pop.mp3">Sea Horse</a>
            </div>
          </div>

          <div class="swiper-slide">
            <img src="/images/shellfishtank.jpg" alt="">
            <div class="title">
              <a href="/fish.php#clownfish" data-sound="/sounds/notification_pop.mp3">Fish Tank Decoration</a>
            </div>
          </div>

        </div>

        <!-- pagination -->
        <div class="swiper-pagination"></div>
      </div>
      <div class="swiper-caption-container" data-aos="fade-up" data-aos-delay="500">
        <p class="swiper-caption">Top-rated picks — only the best for your tank.</p>
        <a href="shop.php" class="swiper-caption-btn">Shop Now</a>
      </div>
    </div>

  </section>


  <!-- Care tips section -->
  <section class="care-tips">
    <div class="tips-container">

      <!-- Tip 1 -->
      <div class="tip-box" data-aos="fade-up">
        <img src="/images/goldfishacc.jpg" alt="Tip 1">
        <div class="tip-text">
          <p class="tip-subtitle">Tank Setup</p>
          <h3>Setting Up Your Aquarium</h3>
          <p>Learn how to prepare your tank environment to keep your fish safe, healthy, and happy.</p>
          <a href="#">read more →</a>
        </div>
      </div>

      <!-- Tip 2 -->
      <div class="tip-box reverse" data-aos="fade-up">
        <img src="/images/goldfishforcare.jpg" alt="Tip 2">
        <div class="tip-text">
          <p class="tip-subtitle">Feeding</p>
          <h3>What & When to Feed</h3>
          <p>Tips on feeding schedules, portion sizes, and nutrition for your aquatic friends.</p>
          <a href="#">read more →</a>
        </div>
      </div>

    </div>
  </section>


  <!-- Testimonials section -->
  <section class="testimonials">
    <div class="flex items-center justify-between my-4 px-10 py-6">
      <hr class="flex-grow border-t-2 border-white" />
    </div>
    <div class="testimonial-text">
      <h2>Read reviews,</h2>
      <p><strong>ride with confidence.</strong></p>
    </div>

    <!-- testimonial main container -->
    <div class="testimonial-box">
      <div class="testimonial-wrapper">
        <!-- quotes -->
        <div class="quotes" data-aos="fade-up">
          <img src="/images/quote.png" class="quote-symbol h-10 w-10" />
          <div class="text-white pt-4 text-xl">
            What our customers are saying
            <div class="flex items-center justify-between my-4">
              <hr class="flex-grow border-t-2 border-white" />
            </div>
          </div>
        </div>

        <div class="quotes" data-aos="fade-up">
          <p>"Amazing customer service! They explain so well and patiently to a beginner fish hobbist like me."</p>
          <div class="quote-author">
            <img src="/images/old-woman.png" alt="Reviewer" />
            <div class="quote-author-name">
              <strong>Tori</strong><br />
              3 days ago
            </div>
          </div>
        </div>

        <div class="quotes" data-aos="fade-up">
          <p>"Super fast delivery and the care guide really helped me maintain a healthy tank. Highly recommended!"</p>
          <div class="quote-author">
            <img src="/images/detective.png" alt="Reviewer" />
            <div class="quote-author-name">
              <strong>Orlando</strong><br />
              10 days ago
            </div>
          </div>
        </div>

        <div class="quotes" data-aos="fade-up">
          <p>"Quick process and my dream aquarium fish is available so it was worth it."</p>
          <div class="quote-author">
            <img src="/images/grandmother.png" alt="Reviewer" />
            <div class="quote-author-name">
              <strong>Sydeny</strong><br />
              1 month ago
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>



  <?php include 'footer.php'; ?>

  <!-- SwiperJs -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <!-- Page Active Indicator -->
  <script src="/js/pageActive.js"></script>

  <!-- Scroll-based -->
  <script>
    window.addEventListener('scroll', () => {
      const scrollY = window.scrollY;
      const topLeft = document.querySelector('.top-left');
      const bottomRight = document.querySelector('.bottom-right');

      // Move based on scroll amount
      topLeft.style.transform = `translateY(${scrollY * 0.2}px)`;
      bottomRight.style.transform = `translateY(-${scrollY * 0.2}px)`;
    });
  </script>


  <!-- Toggle Sounds + Background Music -->
  <script src="/js/soundControl.js"></script>

  <!-- Float Circle Button -->
  <script>
    function toggleOptions() {
      const group = document.querySelector('.floating-button-group');
      group.classList.toggle('active');
    }
  </script>

  <!-- featured products carousel -->
  <script src="/js/featuredProductSlider.js"></script>

  <!-- animation aos on scroll -->
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out',
      once: false,
      mirror: true
    });
  </script>

  
  <?php include 'backToTop.php'; ?>

</body>

</html>