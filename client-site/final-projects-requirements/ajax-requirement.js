// Load bio content using jQuery AJAX
function loadBioContent() {
  $("#bio-content").load(
    "final-projects-requirements/data-for-ajax/profile-bio.html",
    function (response, status, xhr) {
      if (status == "error") {
        // Handle the errors
        console.error(
          "Error loading bio content with jQuery: " +
            xhr.status +
            " " +
            xhr.statusText
        );
        $("#bio-content").html(
          '<p class="text-red-500">Error loading bio content.</p>'
        );
      } else {
        // testing purpose
        console.log("Bio content loaded successfully using jQuery.");
      }
    }
  );
}

// Load mockup order history from a xml file
function loadMockupOrderHistory() {
  const xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "final-projects-requirements/data-for-ajax/order-history.xml",
    true
  );

  // Set responseType to 'document' for automatic XML parsing to make things easy
  try {
    xhr.responseType = "document";
  } catch (e) {
    // Not going to handle this for now
  }

  // Handle the response when the request is complete and successful for the order history
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        const orderHistoryDiv = document.getElementById(
          "order-history-content"
        );
        if (orderHistoryDiv) {
          const xmlDoc =
            xhr.responseXML ||
            new DOMParser().parseFromString(xhr.responseText, "text/xml");

          if (
            !xmlDoc ||
            xmlDoc.getElementsByTagName("parsererror").length > 0
          ) {
            console.error("Error parsing XML.");
            orderHistoryDiv.innerHTML =
              '<p class="text-red-500">Error loading or parsing order history.</p>';
            return;
          }

          const orders = xmlDoc.getElementsByTagName("order");
          let htmlContent = "";

          if (orders.length === 0) {
            htmlContent =
              '<p class="text-gray-500">No order history found.</p>';
          } else {
            htmlContent = '<ul class="divide-y divide-gray-200">';
            for (let i = 0; i < orders.length; i++) {
              const order = orders[i];
              const orderId =
                order.getElementsByTagName("orderId")[0].textContent;
              const date = order.getElementsByTagName("date")[0].textContent;
              const status =
                order.getElementsByTagName("status")[0].textContent;
              const items = order.getElementsByTagName("item");
              let itemsHtml =
                '<ul class="list-disc list-inside pl-4 mt-1 text-sm text-gray-600">';
              let totalOrderPrice = 0;

              for (let j = 0; j < items.length; j++) {
                const item = items[j];
                const name = item.getElementsByTagName("name")[0].textContent;
                const quantity = parseInt(
                  item.getElementsByTagName("quantity")[0].textContent
                );
                const priceElement = item.getElementsByTagName("price")[0];
                const price = parseFloat(priceElement.textContent);
                const currency = priceElement.getAttribute("currency") || "";
                itemsHtml += `<li>${quantity} x ${name} (${price.toFixed(
                  2
                )} ${currency})</li>`;
                totalOrderPrice += quantity * price;
              }
              itemsHtml += "</ul>";

              let statusClass = "text-gray-600";
              if (status === "Shipped") statusClass = "text-blue-600";
              else if (status === "Delivered") statusClass = "text-green-600";
              else if (status === "Processing") statusClass = "text-yellow-600";

              // Create the html for the order items
              htmlContent += `
                                <li class="py-3">
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-medium text-gray-900">Order ID: ${orderId}</p>
                                        <p class="text-sm text-gray-500">${date}</p>
                                    </div>
                                    <div class="mt-1">
                                        ${itemsHtml}
                                    </div>
                                    <div class="mt-2 flex justify-between items-center">
                                        <p class="text-sm font-semibold text-gray-700">Total: ${totalOrderPrice.toFixed(
                                          2
                                        )} ${
                items.length > 0
                  ? items[0]
                      .getElementsByTagName("price")[0]
                      .getAttribute("currency") || ""
                  : ""
              }</p>
                                        <p class="text-sm font-medium ${statusClass}">${status}</p>
                                    </div>
                                </li>
                            `;
            }
            htmlContent += "</ul>";
          }

          orderHistoryDiv.innerHTML = htmlContent;
        } else {
          console.error(
            'Error: Element with ID "order-history-content" not found.'
          );
        }
      } else {
        // Handle HTTP errors (like 404)
        console.error("Error loading order history. Status:", xhr.status);
        const orderHistoryDiv = document.getElementById(
          "order-history-content"
        );
        if (orderHistoryDiv) {
          orderHistoryDiv.innerHTML =
            '<p class="text-red-500">Could not load order history.</p>';
        }
      }
    }
  };

  xhr.onerror = function () {
    console.error(
      "Network error occurred while trying to fetch order history."
    );
    const orderHistoryDiv = document.getElementById("order-history-content");
    if (orderHistoryDiv) {
      orderHistoryDiv.innerHTML =
        '<p class="text-red-500">Network error loading order history.</p>';
    }
  };

  // send the request
  xhr.send();
}

// Load the mockup support tickets from a json file
function loadSupportTickets() {
  const xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "final-projects-requirements/data-for-ajax/support-tickets.json",
    true
  );

  // Handle the response when the request is complete and successful for support tickets
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        const supportTicketsDiv = document.getElementById(
          "support-tickets-content"
        );
        if (supportTicketsDiv) {
          try {
            // Parse the JSON response
            const tickets = JSON.parse(xhr.responseText);
            let htmlContent = "";

            if (!Array.isArray(tickets) || tickets.length === 0) {
              htmlContent =
                '<p class="text-gray-500">No support tickets found.</p>';
            } else {
              htmlContent = '<div class="divide-y divide-gray-200">'; // Use div for layout
              tickets.forEach((ticket) => {
                // Determine badge color based on status
                let statusColor = "bg-gray-100 text-gray-800"; // This would be the default color for the badge
                if (ticket.status === "Open")
                  statusColor = "bg-blue-100 text-blue-800";
                else if (ticket.status === "In Progress")
                  statusColor = "bg-yellow-100 text-yellow-800";
                else if (ticket.status === "Closed")
                  statusColor = "bg-green-100 text-green-800";

                // Determine badge color based on priority
                let priorityColor = "bg-gray-100 text-gray-800"; // This would be the default priority color for the badge
                if (ticket.priority === "High")
                  priorityColor = "bg-red-100 text-red-800";
                else if (ticket.priority === "Medium")
                  priorityColor = "bg-orange-100 text-orange-800";
                else if (ticket.priority === "Low")
                  priorityColor = "bg-green-100 text-green-800";

                // Build HTML for each ticket
                htmlContent += `
                                    <div class="py-3">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="text-sm font-medium text-gray-900">${ticket.subject}</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColor}">
                                                ${ticket.status}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center text-xs text-gray-500">
                                            <span>ID: ${ticket.ticketId} | Submitted: ${ticket.dateSubmitted} | Last Update: ${ticket.lastUpdate}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${priorityColor}">
                                                Priority: ${ticket.priority}
                                            </span>
                                        </div>
                                    </div>
                                `;
              });
              htmlContent += "</div>";
            }
            supportTicketsDiv.innerHTML = htmlContent;
          } catch (e) {
            console.error("Error parsing JSON:", e);
            supportTicketsDiv.innerHTML =
              '<p class="text-red-500">Error loading or parsing support tickets.</p>';
          }
        } else {
          console.error(
            'Error: Element with ID "support-tickets-content" not found.'
          );
        }
      } else {
        // Handle HTTP errors
        console.error("Error loading support tickets. Status:", xhr.status);
        const supportTicketsDiv = document.getElementById(
          "support-tickets-content"
        );
        if (supportTicketsDiv) {
          supportTicketsDiv.innerHTML =
            '<p class="text-red-500">Could not load support tickets.</p>';
        }
      }
    }
  };

  // Handle network errors
  xhr.onerror = function () {
    console.error(
      "Network error occurred while trying to fetch support tickets."
    );
    const supportTicketsDiv = document.getElementById(
      "support-tickets-content"
    );
    if (supportTicketsDiv) {
      supportTicketsDiv.innerHTML =
        '<p class="text-red-500">Network error loading support tickets.</p>';
    }
  };

  // Send it
  xhr.send();
}

// Load all the content when the DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  loadBioContent();
  loadMockupOrderHistory();
  loadSupportTickets();
});
