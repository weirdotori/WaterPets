// const slides = document.querySelectorAll('.carousel-item');
// let currentIndex = 0;

// function updateSlides() {
//   const total = slides.length;
//   const visibleRange = 2; // how many item on left and right

//   slides.forEach((slide, index) => {
//     slide.className = 'carousel-item'; // reset all classes
//     const offset = index - currentIndex;

//     if (offset === 0) {
//       slide.classList.add('center');
//     } else if (offset > 0 && offset <= visibleRange) {
//       slide.classList.add(`right-${offset}`);
//     } else if (offset < 0 && Math.abs(offset) <= visibleRange) {
//       slide.classList.add(`left-${Math.abs(offset)}`);
//     } else {
//       slide.classList.add('hidden');
//     }
//   });
// }

// function prevSlide() {
//   currentIndex = (currentIndex - 1 + slides.length) % slides.length;
//   updateSlides();
// }

// function nextSlide() {
//   currentIndex = (currentIndex + 1) % slides.length;
//   updateSlides();
// }

// // Initialize
// updateSlides();

var swiper = new Swiper(".swiper", {
  effect: "coverflow",
  grabCursor: true,
  centeredSlides: true,
  initialSlide: 2,
  speed: 600,
  preventClicks: true,
  slidesPerView: "auto",
  coverflowEffect: {
  rotate: 0,
  stretch: 40,   // was 80
  depth: 200,    // was 350
  modifier: 1,
  slideShadows: true,
},

  on: {
    click(event) {
      swiper.slideTo(this.clickedIndex);
    },
  },
  pagination: {
    el: ".swiper-pagination"
  }

});