document.addEventListener("DOMContentLoaded", () => {
  const navLinks = document.querySelectorAll("nav a");
  const sections = document.querySelectorAll(".container section");

  function showSection(sectionId) {
    // Hide all sections
    sections.forEach((section) => {
      section.classList.remove("active");
    });
    // Show the selected section
    document.getElementById(sectionId).classList.add("active");

    // Update active link class
    navLinks.forEach((link) => {
      link.classList.remove("active");
    });
    document
      .querySelector(`nav a[data-section="${sectionId}"]`)
      .classList.add("active");

    // Update the URL hash
    window.location.hash = sectionId;
  }

  function saveCurrentSection(sectionId) {
    // Save the current section to local storage
    localStorage.setItem("currentSection", sectionId);
  }

  function loadCurrentSection() {
    // Load the current section from local storage or default to 'users'
    const sectionId = localStorage.getItem("currentSection") || "users";
    showSection(sectionId);
  }

  // Handle navigation link clicks
  navLinks.forEach((link) => {
    link.addEventListener("click", function (event) {
      const sectionId = this.getAttribute("data-section");
      if (!sectionId) {
        return; // Allow default behavior for links without data-section (e.g., homepage link)
      }
      event.preventDefault();
      showSection(sectionId);
      saveCurrentSection(sectionId);
    });
  });

  // Handle URL hash changes
  window.addEventListener("hashchange", () => {
    const sectionId = window.location.hash.substring(1);
    showSection(sectionId);
    saveCurrentSection(sectionId);
  });

  // Load the initially saved section
  loadCurrentSection();

  // Handle form submissions asynchronously
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (event) {
      event.preventDefault(); // Prevent the form from submitting the traditional way

      const formData = new FormData(this);
      const section = form.closest("section").id;

      fetch("admin.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert("Action completed successfully.");
            // Refresh the relevant section
            refreshSection(section);
          } else {
            alert("Action failed: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          //alert("An error occurred. Please try again.");
        });
    });
  });

  function refreshSection(section) {
    fetch("admin.php?section=" + section)
      .then((response) => response.text())
      .then((html) => {
        document.getElementById(section).innerHTML = html;

        // Rebind form submit events to handle new forms loaded via AJAX
        const newForms = document.querySelectorAll(`#${section} form`);
        newForms.forEach((form) => {
          form.addEventListener("submit", function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch("admin.php", {
              method: "POST",
              body: formData,
            })
              .then((response) => response.json())
              .then((data) => {
                if (data.success) {
                  alert("Action completed successfully.");
                  refreshSection(section);
                } else {
                  alert("Action failed: " + data.message);
                }
              })
              .catch((error) => {
                console.error("Error:", error);
                alert("An error occurred. Please try again.");
              });
          });
        });
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }
});
