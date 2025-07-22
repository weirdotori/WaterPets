<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>WaterPets: Aquatic Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <link rel="stylesheet" href="css/style.css">
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
    <h1>Glowing Reefs</h1>
    <p>Colorful coral and marine ecosystems</p>
  </section>

<?php include 'footer.php'; ?>



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



</body>

</html>