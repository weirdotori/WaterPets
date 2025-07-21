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

    <!-- NAVBAR -->
    <nav class="navbar">
      <div class="flex items-center space-x-2 animate-logo">
        <img src="/images/oystergif.gif" alt="Fish Icon" class="h-11 w-11" />
        <span class="h-8 border-l-2 border-blue-500 mx-3"></span>
        <a href="#" class="logo-font animate-slide-in-left" data-sound="/sounds/notification_pop.mp3">WaterPets</a>
      </div>


      <div class="hidden md:flex items-center space-x-6" x-data="{ open: false }">
        <a href="home.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">Home</a>

        <!-- Dropdown Menu for Shop -->
        <div class="relative" @mouseenter="open = true" @mouseleave="open = false">
          <button class="nav-link">Shop</button>

          <!-- Dropdown Items -->
          <div x-show="open" x-transition class="dropdown-menu">
            <a href="fish.php" class="dropdown-item" data-sound="/sounds/notification_pop.mp3">Fish</a>
            <a href="plants.php" class="dropdown-item" data-sound="/sounds/notification_pop.mp3">Plants</a>
            <a href="supplies.php" class="dropdown-item" data-sound="/sounds/notification_pop.mp3">Supplies</a>
            <a href="equipment.php" class="dropdown-item" data-sound="/sounds/notification_pop.mp3">Equipment</a>
          </div>

        </div>

        <a href="about.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">About</a>
        <a href="contact.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">Contact</a>
      </div>


      <div class="hidden md:flex">
        <a href="#" class="nav-link" data-sound="/sounds/notification_pop.mp3">Login</a>
        <a href="#" class="nav-link" data-sound="/sounds/notification_pop.mp3">Register</a>

        <!-- Sound Toggle Button -->
        <button id="sound-toggle" class="nav-link" title="Toggle Sound">
          <img id="sound-icon" src="/images/volume.png" alt="Sound Icon" class="h-7 w-7" />
        </button>

      </div>
    </nav>

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
          <button class="main-circle" onclick="toggleOptions()">Build Your Dream Aquarium Now</button>

          <a href="/fish.html" class="option-button fish">Fish</a>
          <a href="/plants.html" class="option-button plants">Plants</a>
          <a href="/supplies.html" class="option-button supplies">Supplies</a>
          <a href="/equipment.html" class="option-button equipment">Equipment</a>
        </div>

      </div>

      <!-- Right Jellyfish -->
      <img src="/images/jellyfishglow.png" alt="Jellyfish"
        class="h-[400px] w-auto animate-float absolute opacity-90"
        style="right: 100px; bottom: 100px;" />

    </section>

    

  </section>

  <section class="">
    <!-- Glass Box -->
    <div class="absolute bottom-0 left-10 bg-white/10 backdrop-blur-md rounded-xl p-4 text-sm w-[280px] shadow-xl">
      <p><strong>Address:</strong><br>Mandalay Fish Market, MM</p>
      <p class="mt-2"><strong>Hours:</strong><br>Mon–Fri: 10am–7pm<br>Sat–Sun: 10am–9pm</p>
    </div>

  </section>

  <section class="hero-animated">
    <h1>Glowing Reefs</h1>
    <p>Colorful coral and marine ecosystems</p>
  </section>


  <!-- Page Active Indicator -->
  <script>
    const links = document.querySelectorAll('.nav-link');
    const current = window.location.pathname.split("/").pop();

    links.forEach(link => {
      if (link.getAttribute("href") === current) {
        link.classList.add("border-b-2", "border-red-500");
      }
    });
  </script>

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


  <!-- Toggle Sounds -->
  <script>
    const clickSound = new Audio('/sounds/glassyclick_pop.mp3'); // sound toggle
    clickSound.volume = 0.5;

    let soundEnabled = true;

    const soundToggle = document.getElementById('sound-toggle');
    const soundIcon = document.getElementById('sound-icon');

    // Toggle sound ON/OFF
    soundToggle.addEventListener('click', (e) => {
      e.preventDefault();
      soundEnabled = !soundEnabled;
      soundIcon.src = soundEnabled ? '/images/volume.png' : '/images/volume-mute.png';

      // Optional: play toggle sound
      if (soundEnabled) {
        clickSound.currentTime = 0;
        clickSound.play();
      }
    });

    // Play specific sound for each link
    document.querySelectorAll('[data-sound]').forEach(el => {
      el.addEventListener('click', e => {
        const soundPath = el.getAttribute('data-sound');
        if (soundEnabled && soundPath) {
          const customSound = new Audio(soundPath);
          customSound.volume = 0.5;
          customSound.play();
        }
      });
    });
  </script>

  <!-- Float Circle Button -->
  <script>
    function toggleOptions() {
      const group = document.querySelector('.floating-button-group');
      group.classList.toggle('active');
    }
  </script>



</body>

</html>