// public/assets/app.js
// Global app enhancements
document.addEventListener("DOMContentLoaded", () => {
  // Animate flash messages
  const flashMessages = document.querySelectorAll(".flash-message");
  flashMessages.forEach((msg) => {
    msg.classList.add("animate__animated", "animate__fadeInDown");
    setTimeout(() => msg.classList.add("animate__fadeOutUp"), 3000);
  });

  // Smooth scroll for navigation
  document.querySelectorAll('a[href^="/"]').forEach((anchor) => {
    anchor.addEventListener("click", (e) => {
      const href = anchor.getAttribute("href");
      if (href !== "#" && href.startsWith("/")) {
        e.preventDefault();
        window.location.href = href; // Simplified; add smooth scroll if SPA
      }
    });
  });
});

// Utility function for Three.js cleanup
function resizeThreeRenderer(container, renderer, camera) {
  renderer.setSize(container.clientWidth, container.clientHeight);
  camera.aspect = container.clientWidth / container.clientHeight;
  camera.updateProjectionMatrix();
}
