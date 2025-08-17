// Step 1: get DOM
let nextDom = document.getElementById('next');
let prevDom = document.getElementById('prev');

let carouselDom = document.querySelector('.carousel');
let SliderDom = carouselDom.querySelector('.carousel .list');
let thumbnailBorderDom = document.querySelector('.carousel .thumbnail');
let thumbnailItemsDom = thumbnailBorderDom.querySelectorAll('.item');
let timeDom = document.querySelector('.carousel .time');

thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
let timeRunning = 3000;
let timeAutoNext = 7000;

// Click handlers for next/prev
nextDom.onclick = function () {
    showSlider('next');
};

prevDom.onclick = function () {
    showSlider('prev');
};

// Auto next
let runTimeOut;
let runNextAuto = setTimeout(() => {
    nextDom.click();
}, timeAutoNext);

// Function to move slider
function showSlider(type) {
    let SliderItemsDom = SliderDom.querySelectorAll('.carousel .list .item');
    let thumbnailItemsDom = document.querySelectorAll('.carousel .thumbnail .item');

    if (type === 'next') {
        SliderDom.appendChild(SliderItemsDom[0]);
        thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
        carouselDom.classList.add('next');
    } else if (type === 'prev') {
        SliderDom.prepend(SliderItemsDom[SliderItemsDom.length - 1]);
        thumbnailBorderDom.prepend(thumbnailItemsDom[thumbnailItemsDom.length - 1]);
        carouselDom.classList.add('prev');
    } else if (typeof type === 'number') {
        // Jump to specific slide
        for (let i = 0; i < type; i++) {
            SliderDom.appendChild(SliderItemsDom[i]);
            thumbnailBorderDom.appendChild(thumbnailItemsDom[i]);
        }
        carouselDom.classList.add('next');
    }

    clearTimeout(runTimeOut);
    runTimeOut = setTimeout(() => {
        carouselDom.classList.remove('next');
        carouselDom.classList.remove('prev');
    }, timeRunning);

    clearTimeout(runNextAuto);
    runNextAuto = setTimeout(() => {
        nextDom.click();
    }, timeAutoNext);
}

// Add click event to thumbnails
thumbnailItemsDom.forEach((thumb, index) => {
    thumb.addEventListener('click', () => {
        // Move clicked thumbnail to first position
        for (let i = 0; i < index; i++) {
            SliderDom.appendChild(SliderDom.querySelectorAll('.item')[0]);
            thumbnailBorderDom.appendChild(thumbnailBorderDom.querySelectorAll('.item')[0]);
        }
        showSlider('next'); // Trigger animation
    });
});
