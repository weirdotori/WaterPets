// sound-control.js

const clickSound = new Audio('/sounds/glassyclick_pop.mp3');
clickSound.volume = 0.5;

let soundEnabled = true;

const soundToggle = document.getElementById('sound-toggle');
const soundIcon = document.getElementById('sound-icon');

// Background music setup
const bgAudio = new Audio('/sounds/underwater.mp3');
bgAudio.loop = true;
bgAudio.volume = 0.6;

// Allow background audio to start after any user interaction
const tryPlayBgAudio = () => {
  if (soundEnabled) {
    bgAudio.play().catch(err => {
      console.log('Autoplay blocked until user interacts.');
    });
  }
  document.removeEventListener('click', tryPlayBgAudio);
};
document.addEventListener('click', tryPlayBgAudio);

// Toggle sound ON/OFF
soundToggle.addEventListener('click', (e) => {
  e.preventDefault();
  soundEnabled = !soundEnabled;
  soundIcon.src = soundEnabled ? '/images/volume.png' : '/images/volume-mute.png';

  // Play toggle click sound
  if (soundEnabled) {
    clickSound.currentTime = 0;
    clickSound.play();
    bgAudio.play(); // Resume bg audio
  } else {
    bgAudio.pause(); // Mute bg audio
  }
});

// Custom sounds on links/buttons
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
