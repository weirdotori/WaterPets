<style>
  /* footer */
.footer-glass {
  padding: 30px 20px;
  background-color: #060417;
  color: white;
}

.footer-container {
  width: 100%; /* make it full width */
  max-width: 1450px; /* keep your intended limit */
  margin: auto;
  position: relative;
}


/* This stays the same — the glass box handles layout */
.glass-box {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  padding: 30px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  box-shadow: 0 8px 30px rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.2);
  font-size: 14px;
  gap: 150px 260px;
}

.footer-left-text {
  display: inline-block;
  margin-bottom: 4px;
}

.footer-left,
.footer-center,
.footer-right {
  flex: 1 1 250px;
  color: white;
  font-family: "Quicksand", sans-serif;
  font-size: 0.9rem;
  font-weight: 400;
}

.footer-title {
  display: inline-block;
  margin-bottom: 4px;
  font-weight: bold;
  font-size: 0.9rem;
  /* Optional: slightly larger */
  color: #fcd34d;
  /* Adjust to match theme */
}

.footer-link {
  display: inline-block;
  margin-bottom: 4px;
  color: white !important; /* Force white */
  text-decoration: none !important; /* No underline */
  transition: transform 0.3s ease, text-shadow 0.3s ease;
}

.footer-link:hover {
  color: white !important; /* Keep white on hover */
  transform: scale(1.05) !important;
  text-shadow: 2px 2px 16px rgb(255, 255, 255) !important;
}


.footer-divider {
  margin: 50px auto;
  border: none;
  height: 1px;
  background: rgb(255, 255, 255);
  /* light translucent line */
  width: 100%;
}

.footer-social {
  margin-top: 10px;
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.footer-social a img {
  width: 24px;
  height: 24px;
  filter: invert(1);
  /* makes black icons white */
  transition: transform 0.2s ease;
}

.footer-social a:hover img {
  transform: scale(1.2) !important;
}

.copyright-text {
  text-align: center;
  font-size: 0.8rem;
}

@media (max-width: 480px) {
  .glass-box {
    padding: 15px;
    gap: 30px;
  }

  .footer-left,
  .footer-center,
  .footer-right {
    font-size: 0.8rem;
  }

  .footer-social a img {
    width: 20px;
    height: 20px;
  }

  .footer-title {
    font-size: 0.95rem;
  }
}
</style>


<footer class="footer-glass">
  <div class="footer-container">

    <div class="glass-box">
      <div class="footer-left">
        <p class="footer-left-text">Address:<br>No.230, Rose Road, Yangon, MM</p> <br>
        <p class="footer-left-text">Opening Hours:<br>Mon–Fri: 10am–5pm<br>Sat–Sun: 10am–7pm</p>
      </div>

      <div class="footer-center">
        <p class="footer-title">Where to?</p> <br>
        <a href="fish.php" class="footer-link" data-sound="/sounds/notification_pop.mp3">Live Fish</a> <br>
        <a href="coralreefs.php" class="footer-link" data-sound="/sounds/notification_pop.mp3">Corals and Decorations</a> <br>
        <a href="#" class="footer-link" data-sound="/sounds/notification_pop.mp3">Care Guide</a> <br>
        <a href="contactus.php" class="footer-link" data-sound="/sounds/notification_pop.mp3">Contact Us</a> <br>
        <a href="#" class="footer-link" data-sound="/sounds/notification_pop.mp3">About Us</a> <br>
        <a href="faq.php" class="footer-link" data-sound="/sounds/notification_pop.mp3">FAQ</a> <br>
      </div>

      <div class="footer-right">
        <p>Contact</p>
        <p>Phone: +95 123 456 789</p>
        <p>Email: support@waterpets.com</p>

        <!-- Social Icons -->
        <div class="footer-social">
          <a href="#" data-sound="/sounds/notification_pop.mp3"><img src="/images/gmail.png" alt="Facebook"></a>
          <a href="#" data-sound="/sounds/notification_pop.mp3"><img src="/images/telegram.png" alt="Instagram"></a>
          <a href="#" data-sound="/sounds/notification_pop.mp3"><img src="/images/discord.png" alt="Twitter"></a>
          <a href="#" data-sound="/sounds/notification_pop.mp3"><img src="/images/facebook.png" alt="Twitter"></a>
          <a href="#" data-sound="/sounds/notification_pop.mp3"><img src="/images/instagram.png" alt="Twitter"></a>
          <a href="#" data-sound="/sounds/notification_pop.mp3"><img src="/images/twitterX.png" alt="Twitter"></a>
        </div>
      </div>
    </div>

    <!-- Horizontal Line -->
    <hr class="footer-divider">
    <div class="copyright-text">
      ©Copyright All rights reserved.
    </div>
</footer>

<!-- Toggle Sounds + Background Music -->
<script src="/js/soundControl.js"></script>