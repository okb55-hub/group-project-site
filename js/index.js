'use strict';
// スライドショー
document.addEventListener('DOMContentLoaded', () => {
	const slides = document.querySelectorAll('.fv_img');
	if (!slides.length) return;

	let current = 0;

	const interval = window.matchMedia('(max-width: 768px)').matches
    	? 4800   // スマホ
    	: 7500;  // PC

	slides[current].classList.add('active');

	setInterval(() => {
    	slides[current].classList.remove('active');
    	current = (current + 1) % slides.length;
    	slides[current].classList.add('active');
  	}, interval);

// テイクアウトボタン　トグル処理
const toggleBtn = document.getElementById('takeout_reserve_btn');

if (toggleBtn) {
	toggleBtn.addEventListener('click', () => {
	toggleBtn.classList.toggle('active');
	});
}

const sliderInner = document.getElementById('slider_inner');
const leftBtn = document.getElementById('arrow_left');
const rightBtn = document.getElementById('arrow_right');

if (sliderInner && leftBtn && rightBtn) {
    let autoScrollTimer;

    const stopAutoScroll = () => {
      sliderInner.style.animationPlayState = 'paused';
      clearTimeout(autoScrollTimer);
    };

    const startAutoScroll = () => {
      autoScrollTimer = setTimeout(() => {
        sliderInner.style.animationPlayState = 'running';
      }, 3000);
    };

    rightBtn.addEventListener('mousedown', stopAutoScroll);
    rightBtn.addEventListener('mouseup', startAutoScroll);

    leftBtn.addEventListener('mousedown', stopAutoScroll);
    leftBtn.addEventListener('mouseup', startAutoScroll);

    rightBtn.addEventListener('click', () => {
      const step = sliderInner.offsetWidth / 6;
      sliderInner.style.transform = `translateX(-${step}px)`;
      setTimeout(() => {
        sliderInner.style.transform = '';
      }, 300);
    });

    leftBtn.addEventListener('click', () => {
      const step = sliderInner.offsetWidth / 6;
      sliderInner.style.transform = `translateX(${step}px)`;
      setTimeout(() => {
        sliderInner.style.transform = '';
      }, 300);
    });
}

// フェードイン処理
const fadeTargets = document.querySelectorAll('.fade');

  if (fadeTargets.length) {
    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-active');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.2
    });

    fadeTargets.forEach(el => observer.observe(el));
  }

});
