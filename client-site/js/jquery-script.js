/* ------------------------------ */
/* Mobile Menu Logic */
/* ------------------------------ */

// Mobile menu button and container reference
let openMobileMenuButton = $("#open-mobile-menu-button");
let closeMobileMenuButton = $("#close-mobile-menu-button");
let mobileMenuContainer = $("#mobile-menu-container");

// Set the default state of the mobile menu to hidden
mobileMenuContainer.hide();

/* ------ Animations  ----- */

// Fading in for hero images when the page loads
for (let i = 1; i <= 5; i++) {
  let heroImage = $(`#hero-image-${i}`);
  if (document.title === "HayatiFits") {
    // Set there images
    heroImage.children()[0].src = `/client-site/images/hero-image-${i}.png`;
  }

  // Fade in the hero image
  heroImage.hide().fadeIn("slow");
}

// Fading in for service cards when the page loads
for (let i = 1; i <= 2; i++) {
  let service = $(`#service-${i}`);
  service.hide().fadeIn("slow");
}

// Add hover effects to hero images
for (let i = 1; i <= 5; i++) {
  let heroImage = $(`#hero-image-${i}`);
  heroImage.hover(
    function () {
      $(this).css({
        transform: "scale(1.05)",
        transition: "transform 0.3s ease",
      });
    },
    function () {
      $(this).css({
        transform: "scale(1)",
        transition: "transform 0.3s ease",
      });
    }
  );
}

// Add hover effects to service cards
for (let i = 1; i <= 2; i++) {
  let service = $(`#service-${i}`);
  service.hover(
    function () {
      $(this).css({
        transform: "translateY(-10px)",
        transition: "transform 0.3s ease",
        "box-shadow": "0 10px 20px rgba(0,0,0,0.1)",
      });
    },
    function () {
      $(this).css({
        transform: "translateY(0)",
        transition: "transform 0.3s ease",
        "box-shadow": "none",
      });
    }
  );
}

/* ---------- */

/* ------ Events ----- */

// Handle opening of mobile menu
openMobileMenuButton.on("click", function () {
  mobileMenuContainer.show();
});

// Handle closing of mobile menu
closeMobileMenuButton.on("click", function () {
  mobileMenuContainer.hide();
});

/* ---------- */

/* ------------------------------ */
