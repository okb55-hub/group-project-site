'use strict';

const hamburger = document.getElementById('hamburger');
const navList = document.getElementById('nav_list');
hamburger.addEventListener('click', () => {
	hamburger.classList.toggle('active');
	navList.classList.toggle('active');
})