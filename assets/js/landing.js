// Sample data array for listings
const listingsData = [
    {
      id: 1,
      location: "Diani Beach, Kenya",
      rating: 5.0,
      price: 6206,
      favorite: true,
      wishlist: false,
      image: "../uploads/landing_page/bedroom.jpg",
      lat: -4.316667,
      lng: 39.583333,
      availabilityStart: "2025-05-01",
      availabilityEnd: "2025-05-15"
    },
    {
      id: 2,
      location: "Diani Beach, Kenya",
      rating: 4.88,
      price: 6206,
      favorite: true,
      wishlist: false,
      image: "../uploads/landing_page/beach.jpg",
      lat: -4.318000,
      lng: 39.590000,
      availabilityStart: "2025-04-20",
      availabilityEnd: "2025-05-10"
    },
    {
      id: 3,
      location: "Ukunda, Kenya",
      rating: 4.81,
      price: 4500,
      favorite: false,
      wishlist: false,
      image: "../uploads/landing_page/login_landscape.jpg",
      lat: -4.280000,
      lng: 39.570000,
      availabilityStart: "2025-05-05",
      availabilityEnd: "2025-05-05"
    },
    {
      id: 4,
      location: "Mombasa, Kenya",
      rating: 4.7,
      price: 5500,
      favorite: false,
      wishlist: false,
      image: "../uploads/landing_page/landscape_register.jpg",
      lat: -4.043477,
      lng: 39.668206,
      availabilityStart: "2025-06-01",
      availabilityEnd: "2025-06-10"
    },
    {
      id: 5,
      location: "Nairobi, Kenya",
      rating: 4.9,
      price: 7000,
      favorite: false,
      wishlist: false,
      image: "../uploads/landing_page/about.jpg",
      lat: -1.286389,
      lng: 36.817223,
      availabilityStart: "2025-05-10",
      availabilityEnd: "2025-05-20"
    }
    // Add more listings as needed...
  ];

  // Pagination variables
  let currentPage = 1;
  const listingsPerPage = 3;

  // Leaflet map & marker group
  let map, markerGroup;

  // On window load, initialize map & listings
  window.addEventListener('load', () => {
    initMap();
    renderListings(listingsData);

    // Initialize Flatpickr for date range
    flatpickr("#searchDateRange", {
      mode: "range",           // single calendar for start & end
      showMonths: 2,           // show 2 months side by side
      minDate: "today",        // disable past dates
      dateFormat: "Y-m-d",     // the actual input value
      altInput: true,          // display a friendlier format
      altFormat: "F j, Y",     // e.g. "March 29, 2025 to April 2, 2025"
      prevArrow: "<i class='fas fa-chevron-left'></i>",
      nextArrow: "<i class='fas fa-chevron-right'></i>"
    });
  });

  // Initialize Leaflet map
  function initMap() {
    map = L.map('map').setView([-1.286389, 36.817223], 7); // Center on Kenya
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap'
    }).addTo(map);
    markerGroup = L.layerGroup().addTo(map);
  }

  // Render listings (with pagination) in the DOM
  function renderListings(fullList) {
    const container = document.getElementById('listingsGrid');
    container.innerHTML = ''; // Clear existing content
    markerGroup.clearLayers(); // Clear existing markers

    // Determine items to show based on pagination
    const paginatedList = fullList.slice(0, currentPage * listingsPerPage);

    paginatedList.forEach(item => {
      // Create listing card
      const card = document.createElement('div');
      card.classList.add('listing-card');
      card.innerHTML = `
        <img src="${item.image}" alt="${item.location}">
        <div class="listing-details">
          ${ item.favorite ? '<div class="listing-favorite">Guest favorite</div>' : '' }
          <div class="listing-title">${item.location}</div>
          <div class="listing-rating">
            <i class="fas fa-star"></i>
            <span>${item.rating}</span>
          </div>
          <div class="listing-price">KSh ${item.price} / night</div>
        </div>
        <div class="wishlist-icon ${item.wishlist ? 'active' : ''}" 
             onclick="toggleWishlist(${item.id})">
          <i class="fas fa-heart"></i>
        </div>
      `;
      container.appendChild(card);

      // Add marker to map
      const marker = L.marker([item.lat, item.lng]).addTo(markerGroup);
      marker.bindPopup(`<strong>${item.location}</strong><br/>KSh ${item.price}/night`);
    });

    // Show/hide "Load More" button if there's no more data
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (currentPage * listingsPerPage >= fullList.length) {
      loadMoreBtn.style.display = 'none';
    } else {
      loadMoreBtn.style.display = 'inline-block';
    }
  }

  // Wishlist toggle
  function toggleWishlist(id) {
    const listing = listingsData.find(l => l.id === id);
    if (listing) {
      listing.wishlist = !listing.wishlist;
    }
    applySearchFilter();
  }

  // "Load More" pagination
  document.getElementById('loadMoreBtn').addEventListener('click', () => {
    currentPage++;
    applySearchFilter();
  });

  // Simple search logic (optional)
  document.getElementById('searchBtn').addEventListener('click', () => {
    currentPage = 1; // reset pagination
    applySearchFilter();
  });

  function applySearchFilter() {
    const locationVal = document.getElementById('searchLocation').value.toLowerCase().trim();
    const dateRangeVal = document.getElementById('searchDateRange').value.trim();
    const guestsVal = document.getElementById('searchGuests').value.trim();

    let filtered = [...listingsData];

    // Filter by location
    if (locationVal) {
      filtered = filtered.filter(item =>
        item.location.toLowerCase().includes(locationVal)
      );
    }

    // (Optional) Filter by date range if you want real date overlap logic
    // For example, parse dateRangeVal if it contains " to " or check altInput

    // (Optional) Filter by guests if you have capacity data

    renderListings(filtered);
  }

  // Profile Dropdown Toggle
  const profileIcon = document.getElementById('profileIcon');
  const profileDropdown = document.getElementById('profileDropdown');

  document.addEventListener('click', (e) => {
    // If user clicked on the icon, toggle the dropdown
    if (profileIcon.contains(e.target)) {
      profileDropdown.classList.toggle('show');
    } else {
      // Otherwise, if click is outside the dropdown, close it
      if (!profileDropdown.contains(e.target)) {
        profileDropdown.classList.remove('show');
      }
    }
  });

  // Region / Language Modal
  const regionIcon = document.getElementById('regionIcon');
  const regionModal = document.getElementById('regionModal');
  const regionClose = document.getElementById('regionClose');
  const regionTabs = document.querySelectorAll('.region-tab');
  const langTabContent = document.getElementById('langTab');
  const currencyTabContent = document.getElementById('currencyTab');

  // Show modal when clicking the globe icon
  regionIcon.addEventListener('click', () => {
    regionModal.classList.add('show');
  });

  // Close modal when clicking the "×" button
  regionClose.addEventListener('click', () => {
    regionModal.classList.remove('show');
  });

  // Close modal when clicking outside content
  regionModal.addEventListener('click', (e) => {
    if (e.target === regionModal) {
      regionModal.classList.remove('show');
    }
  });

  // Tab switching logic
  regionTabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      // Remove "active" from all tabs
      regionTabs.forEach((t) => t.classList.remove('active'));
      // Hide all tab contents
      langTabContent.style.display = 'none';
      currencyTabContent.style.display = 'none';

      // Mark the clicked tab as active
      tab.classList.add('active');
      // Show the relevant content
      const targetId = tab.getAttribute('data-target');
      if (targetId === 'langTab') {
        langTabContent.style.display = 'block';
      } else {
        currencyTabContent.style.display = 'block';
      }
    });
  });