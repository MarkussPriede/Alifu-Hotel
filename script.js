window.onscroll = function () {
  var hero = document.getElementById("hero");
  var scrollPos = window.scrollY;
  var windowHeight = window.innerHeight;
  var opacity = 1 - scrollPos / (windowHeight * 0.4); // Adjust the factor to control the rate of text disappearance
  var minHeight = 0; // Minimum height you want the hero section to reach before disappearing
  var initialHeroHeight = windowHeight * 0.5; // Set this to the initial height of the hero section

  if (opacity >= 0) {
    hero.style.opacity = opacity;
  }

  var newHeight = initialHeroHeight - scrollPos;

  if (newHeight > minHeight) {
    hero.style.height = newHeight + "px";
  } else {
    hero.style.height = minHeight + "px";
  }
};

window.onbeforeunload = function () {
  window.scrollTo(0, 0);
};
