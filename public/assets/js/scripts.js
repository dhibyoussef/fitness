document.addEventListener("DOMContentLoaded", function () {
  console.log("JavaScript is loaded and ready to go!");
  // Add your JavaScript code here
});
document.addEventListener("DOMContentLoaded", () => {
  // Initialize theme first
  const savedTheme = localStorage.getItem("theme") || "dark";
  document.documentElement.setAttribute("data-theme", savedTheme);

  // Theme toggler function
  const toggleTheme = () => {
    const htmlEl = document.documentElement;
    const newTheme =
      htmlEl.getAttribute("data-theme") === "dark" ? "light" : "dark";
    htmlEl.setAttribute("data-theme", newTheme);
    localStorage.setItem("theme", newTheme);
  };

  // Smooth scroll initialization
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
        });
      }
    });
  });
});
$(document).ready(function () {
  // Handle form submission
  $("form").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      url: $(this).attr("action"),
      method: "POST",
      data: $(this).serialize(),
      success: function (response) {
        if (response.success) {
          window.location.href = response.redirect;
        } else {
          alert(response.message);
        }
      },
      error: function () {
        alert("An error occurred. Please try again.");
      },
    });
  });
});
