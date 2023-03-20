const hero = document.querySelector('.hero');

window.addEventListener('scroll', () => {
  const scrollPosition = window.scrollY;
  const minHeight = 50;
  const maxHeight = 80;
  
  // Calculate new height based on scroll position
  const height = Math.max(maxHeight - scrollPosition / 10, minHeight);
  
  // Set new height
  hero.style.height = `${height}vh`;
});


