"use strict";
document.addEventListener('DOMContentLoaded', () => {
  const targets = document.querySelectorAll('.fade-in');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-active');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.2
  });

  targets.forEach(target => observer.observe(target));
});
