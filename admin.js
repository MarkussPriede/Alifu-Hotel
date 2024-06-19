document.querySelectorAll("nav a").forEach((link) => {
  link.addEventListener("click", function (e) {
    e.preventDefault();
    document.querySelectorAll("nav a").forEach((navLink) => {
      navLink.classList.remove("active");
    });
    document.querySelectorAll("section").forEach((section) => {
      section.classList.remove("active");
    });
    this.classList.add("active");
    const sectionId = this.getAttribute("data-section");
    document.getElementById(sectionId).classList.add("active");
    localStorage.setItem("activeSection", sectionId); // Save the active section in localStorage
  });
});
