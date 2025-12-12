'use strict';

// テイクアウトボタン　トグル処理
const toggleBtn = document.getElementById('takeout_reserve_btn');
const clickBtn = document.querySelectorAll('.click_btn');

toggleBtn.addEventListener('click', () => {
	toggleBtn.classList.toggle('active');
});

const sliderInner = document.getElementById('slider_inner');
const leftBtn = document.getElementById('arrow_left');
const rightBtn = document.getElementById('arrow_right');

let autoScrollIntervel;

// 自動スクロール停止
function stopAutoScroll() {
	sliderInner.style.animationPlayState = 'paused';
	clearTimeout(autoScrollIntervel);
}

// 自動スクロール再開
function startAutoScroll() {
	autoScrollIntervel = setInterval(() => {
		sliderInner.style.animationPlayState = 'running';
	}, 3000);
}


// ボタン操作
rightBtn.addEventListener('mousedown', () => stopAutoScroll);
rightBtn.addEventListener('mouseup', () => startAutoScroll);
rightBtn.addEventListener('click', () => {
	const step = sliderInner.offsetWidth / 6;
	sliderInner.style.transform = `translateX(${step}px)`;
	setTimeout(() => {
		sliderInner.style.transform = '';
	}, 300);
});

leftBtn.addEventListener('mousedown', () => stopAutoScroll);
leftBtn.addEventListener('mouseup', () => startAutoScroll);
leftBtn.addEventListener('click', () => {
	const step = sliderInner.offsetWidth / 6;
	sliderInner.style.transform = `translateX(${-step}px)`;
	setTimeout(() => {
		sliderInner.style.transform = '';
	}, 300);
});