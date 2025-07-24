<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>WaterPets: Aquatic Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>

<body>

  <section class="section-bg">
    <!-- BUBBLES CONTAINER -->
    <div class="bubbles-container">
      <div class="bubble" style="left: 10%; width: 30; height: 25px; animation-delay: 0s;"></div>
      <div class="bubble" style="left: 25%; width: 25px; height: 25px; animation-delay: 2s;"></div>
      <div class="bubble" style="left: 40%; width: 10px; height: 10px; animation-delay: 4s;"></div>
      <div class="bubble" style="left: 30%; width: 15px; height: 15px; animation-delay: 1s;"></div>
      <div class="bubble" style="left: 60%; width: 35px; height: 35px; animation-delay: 3s;"></div>
      <div class="bubble" style="left: 90%; width: 25px; height: 25px; animation-delay: 5s;"></div>
    </div>

    <?php include 'header.php'; ?>


    <!-- Content SECTION -->
    <section class="relative h-[90vh] flex items-center justify-between px-10">

      <!-- Decorative Borders -->
      <div class="corner-deco top-left"></div>
      <div class="corner-deco bottom-right"></div>

      <!-- Left Text -->
      <div class="main-content">
        <p class="main-text">WaterPets</p>
        <p class="sub-text">Your One-Stop Aquatic Store!</p>
        <p class="sub-text">Shop variety of aquarium fish, plants and fish care products at one place.</p>

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



  <section class="section-gradient">
    <div class="feature-product">
      <p class="popular-pick-text">Popular Picks</p>

      <!-- <div class="carousel-container">
        <div class="carousel-slider" id="coverflowSlider">
          <div class="carousel-item">
            <img src="/images/fish1.jpg">
            <p>Neon Tetra</p>
          </div>

          <div class="carousel-item">
            <img src="/images/plant1.jpg">
            <p>Water Plant</p>
          </div>

          <div class="carousel-item">
            <img src="/images/coral1.jpg">
            <p>Live Coral</p>
          </div>

        </div>
        <div class="carousel-buttons">
          <button onclick="prevSlide()">❮</button>
          <button onclick="nextSlide()">❯</button>
        </div>
      </div> -->

      <!-- Slider main container -->
      <div class="swiper">
        <div class="swiper-wrapper">
          <!-- Slides -->
          <div class="swiper-slide">
            <img src="/images/clownfish.jpg" alt="">
            <div class="title">
              <span>Clown Fish</span>
            </div>
          </div>

          <div class="swiper-slide">
            <img src="/images/blue-betta.jpg" alt="">
            <div class="title">
              <span>Navy Blue Betta</span>
            </div>
          </div>

          <div class="swiper-slide">
            <img src="/images/betta-tank.jpg" alt="">
            <div class="title">
              <span>Square Fish Tank</span>
            </div>
          </div>

          <div class="swiper-slide">
            <img src="/images/seahorse.jpg" alt="">
            <div class="title">
              <span>Sea Horse</span>
            </div>
          </div>

          <div class="swiper-slide">
            <img src="/images/shellfishtank.jpg" alt="">
            <div class="title">
              <span>Fish Tank Decoration</span>
            </div>
          </div>
        
        </div>
        
        <!-- If we need pagination -->
        <div class="swiper-pagination"></div>
      </div>
    </div>


    <div class="latest-offer">

    </div>

    <div class="care-tips">

    </div>

    <div class="testimonial">

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
</body>

</html>