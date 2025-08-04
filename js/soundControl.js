// sound-control.js

const clickSound = new Audio('/sounds/glassyclick_pop.mp3');
clickSound.volume = 0.5;

let soundEnabled = true;

const soundToggle = document.getElementById('sound-toggle');
const soundIcon = document.getElementById('sound-icon');

// For mobile menu
const soundToggleMobile = document.getElementById('sound-toggle-mobile');
const soundIconMobile = document.getElementById('sound-icon-mobile');

// Background music setup (only for home.php)
const isHomePage = window.location.pathname.includes('home.php');
let bgAudio = null;
if (isHomePage) {
  bgAudio = new Audio('/sounds/underwater.mp3');
  bgAudio.loop = true;
  bgAudio.volume = 0.6;

  // Allow background audio to start after any user interaction
  const tryPlayBgAudio = () => {
    if (soundEnabled && bgAudio) {
      bgAudio.play().catch(err => {
        console.log('Autoplay blocked until user interacts.');
      });
    }
    document.removeEventListener('click', tryPlayBgAudio);
  };
  document.addEventListener('click', tryPlayBgAudio);
}

// Function to update both icons
function updateSoundIcons() {
  const src = soundEnabled ? '/images/volume.png' : '/images/volume-mute.png';
  if (soundIcon) soundIcon.src = src;
  if (soundIconMobile) soundIconMobile.src = src;
}

// Shared toggle function
function toggleSound(e) {
  e.preventDefault();
  soundEnabled = !soundEnabled;
  updateSoundIcons();

  if (soundEnabled) {
    clickSound.currentTime = 0;
    clickSound.play();
    if (isHomePage && bgAudio) bgAudio.play();
  } else {
    if (isHomePage && bgAudio) bgAudio.pause();
  }
}

// Add event listeners for both toggles
if (soundToggle) {
  soundToggle.addEventListener('click', toggleSound);
}
if (soundToggleMobile) {
  soundToggleMobile.addEventListener('click', toggleSound);
}

// Custom sounds on links/buttons
document.querySelectorAll('[data-sound]').forEach(el => {
  el.addEventListener('click', e => {
    const soundPath = el.getAttribute('data-sound');
    if (soundEnabled && soundPath) {
      e.preventDefault(); // stop immediate navigation
      const customSound = new Audio(soundPath);
      customSound.volume = 0.5;
      customSound.play();

      const target = el.getAttribute('href');
      if (target && target !== '#') {
        setTimeout(() => {
          window.location.href = target;
        }, 200); // small delay so sound plays
      }
    }
  });
});
