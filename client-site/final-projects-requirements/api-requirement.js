// Make a api call to a public weather api and display the weather information on the page
$(document).ready(function () {
  $.ajax({
    url: "https://wttr.in/Kingston,Rhode%20Island?format=j1",
    method: "GET",
    dataType: "json",
    success: function (data) {
      // Check if the data is valid and grab the data we need
      if (data.current_condition && data.current_condition.length > 0) {
        const condition = data.current_condition[0];
        const location =
          data.nearest_area && data.nearest_area.length > 0
            ? data.nearest_area[0].areaName[0].value
            : "Weather Info";
        const tempC = condition.temp_C;
        const feelsLikeC = condition.FeelsLikeC;
        const description =
          condition.weatherDesc && condition.weatherDesc.length > 0
            ? condition.weatherDesc[0].value
            : "N/A";

        // Create the html and then we just inject it into the page after
        const weatherHtml = `
                    <div id="weather-section" class="bg-green-100 border border-green-300 rounded-lg shadow overflow-hidden mb-8 p-6">
                        <h2 class="text-xl font-semibold text-green-900 mb-3">${location} Weather</h2>
                        <div class="text-green-800">
                            <p class="text-lg"><strong>Temperature:</strong> ${tempC}°C</p>
                            <p><strong>Feels Like:</strong> ${feelsLikeC}°C</p>
                            <p><strong>Condition:</strong> ${description}</p>
                        </div>
                    </div>
                `;

        // Inject it before the about me section
        $("#about-me-section").before(weatherHtml);
      } else {
        console.error("Weather data format unexpected:", data);
        displayWeatherError();
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error("Failed to fetch weather:", textStatus, errorThrown);
      displayWeatherError();
    },
  });

  function displayWeatherError() {
    const errorHtml = `
            <div id="weather-section" class="bg-red-100 border border-red-300 rounded-lg shadow overflow-hidden mb-8 p-6">
                <h2 class="text-xl font-semibold text-red-900 mb-3">Weather Information</h2>
                <p class="text-red-800">Could not load weather information at this time.</p>
            </div>
        `;
    $("#about-me-section").before(errorHtml);
  }
});
