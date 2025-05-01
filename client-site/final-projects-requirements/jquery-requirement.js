// Toggle visibility of profile info with animation using events
$(document).ready(function () {
  $("#toggle-info-btn").on("click", function () {
    const sectionsToToggle = $("#profile-info, #about-me-section");

    sectionsToToggle.slideToggle(400, function () {
      const isVisible = $("#profile-info").is(":visible");
      $("#toggle-info-btn").text(isVisible ? "Hide Info" : "Show Info");

      if (isVisible) {
        $("#profile-visibility-placeholder").html(
          '<p class="dynamic-message">Profile details are visible.</p>'
        );
        $("#profile-card button").css("opacity", "1");
      } else {
        $("#profile-visibility-placeholder").html(
          '<p class="dynamic-message text-red-600">Profile details are hidden.</p>'
        );
      }
    });
  });

  // Modify CSS on hover for the username heading
  $("#profile-info h1").hover(
    function () {
      $(this).css("color", "teal");
    },
    function () {
      $(this).css("color", "");
    }
  );

  // Add initial message since info is visible by default
  $("#profile-visibility-placeholder").html(
    '<p class="dynamic-message">Profile details are visible.</p>'
  );
});
