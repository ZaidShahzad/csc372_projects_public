/*

Used this as a reference for the ajax script
https://www.w3schools.com/Js/js_ajax_intro.asp

*/

// Load the customer reviews HTML into the div with id customer-reviews-to-load-into
function loadCustomerReviewsFromHTML() {
  const xhttp = new XMLHttpRequest();

  xhttp.onload = function () {
    if (this.status !== 200) {
      throw new Error("Failed to load customer reviews");
    }
    document.getElementById("customer-reviews-to-load-into").innerHTML =
      this.responseText;
  };

  xhttp.open("GET", "/client-site/ajax-load/mock-customer-reviews.html", true);
  xhttp.send();
}

// Load the service cards from the XML file to the div with id service-cards-to-load-into
function loadServiceCardsFromXML() {
  const xhttp = new XMLHttpRequest();

  xhttp.onload = function () {
    if (this.status !== 200) {
      throw new Error("Failed to load service cards");
    }
    const xmlDoc = this.responseXML;
    // Initialize empty string for HTML output
    let output = "";
    // Extract all elements from the XML
    const services = xmlDoc.getElementsByTagName("service");

    // Loop through each service in the XML
    for (let i = 0; i < services.length; i++) {
      // Get the id, title, description, and action link from the service
      const id = services[i].getAttribute("id");
      const title = services[i].getElementsByTagName("title")[0].textContent;
      const description =
        services[i].getElementsByTagName("description")[0].textContent;
      const actionLink = services[i].getElementsByTagName("action-link")[0];
      const href = actionLink.getAttribute("href");
      const linkText = actionLink.textContent;

      // Build HTML for each service card using template literals
      output += `
          <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300 service-card" id="${id}">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">${title}</h3>
            <p class="text-sm text-gray-600 mb-4">${description}</p>
            <a href="${href}" class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200 text-sm font-medium">${linkText}</a>
          </div>
        `;
    }

    // Insert the generated HTML into the service cards container
    document.getElementById("service-cards-to-load-into").innerHTML = output;
  };

  xhttp.open("GET", "/client-site/ajax-load/service-cards.xml", true);
  xhttp.send();
}

// Load hero text from JSON file to the hero title and description divs
function loadHeroTextFromJSON() {
  const xhttp = new XMLHttpRequest();

  xhttp.onload = function () {
    if (this.status !== 200) {
      throw new Error("Failed to load hero text");
    }
    const jsonDoc = JSON.parse(this.responseText);

    document.getElementById("hero-title-to-load-into").textContent =
      jsonDoc.title;
    document.getElementById("hero-description-to-load-into").textContent =
      jsonDoc.description;
  };

  xhttp.open("GET", "/client-site/ajax-load/hero-text.json", true);
  xhttp.send();
}

// Load the contact form using Jquery from the HTML file to the div with id contact-form-to-load-into
function loadContactFormFromHTMLUsingJquery() {
  const xhttp = new XMLHttpRequest();

  xhttp.onload = function () {
    if (this.status !== 200) {
      throw new Error("Failed to load contact form");
    }
    // Get the div using Jquery and set it's html to the response text
    let contactFormToLoadInto = $("#contact-form-to-load-into");
    contactFormToLoadInto.html(this.responseText);

    // Add a simple fading in animation as well
    contactFormToLoadInto.hide().fadeIn("slow");
  };

  xhttp.open("GET", "/client-site/ajax-load/contact-form.html", true);
  xhttp.send();
}

// Load the appropriate content based on the document title (to tell which page we want to load content for)
if (document.title === "HayatiFits") {
  // Home page
  loadServiceCardsFromXML();
  loadHeroTextFromJSON();
} else if (document.title === "Customer Reviews") {
  // Customer Reviews page
  loadCustomerReviewsFromHTML();
} else if (document.title === "Contact") {
  // Contact page
  loadContactFormFromHTMLUsingJquery();
}
