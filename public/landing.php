<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>weshPAY – Airbnb E-Wallet</title>
  <meta name="description" content="Manage your vacation finances easily and securely with the new weshPAY E-Wallet for Airbnb.">
  <link rel="icon" href="path/to/favicon.ico" />
  
  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Leaflet CSS for map integration -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

  <style>
    /* -------------------------------- */
    /*          GLOBAL STYLES           */
    /* -------------------------------- */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Poppins', sans-serif;
      color: #333;
      background: linear-gradient(135deg, #ffffff, #f2f2f2);
      background-size: cover;
      position: relative;
      min-height: 100vh;
      scroll-behavior: smooth;
    }
    h1, h2, h3, h4 {
      color: #2a2185; /* Keep your primary theme color */
    }

    /* -------------------------------- */
    /*            HEADER NAV            */
    /* -------------------------------- */
    header {
      width: 100%;
      padding: 20px 50px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .logo {
      font-size: 28px;
      font-weight: 600;
      color: #2a2185;
    }
    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      align-items: center;
    }
    nav ul li a {
      text-decoration: none;
      color: #333;
      font-size: 16px;
      transition: color 0.3s;
    }
    nav ul li a:hover {
      color: #2a2185;
    }
    .menu-toggle {
      display: none;
      font-size: 28px;
      cursor: pointer;
      background: none;
      border: none;
      color: #333;
    }
    /* Button Styles */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 18px;
      background: #2a2185;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      font-size: 1rem;
      font-weight: 600;
      transition: background 0.3s, transform 0.2s;
      cursor: pointer;
    }
    .btn:hover {
      background: rgb(62, 55, 134);
      transform: scale(1.05);
    }

    /* -------------------------------- */
    /*        LISTINGS SECTION          */
    /* -------------------------------- */
    .listings {
      padding: 120px 50px 50px; /* top padding accommodates the fixed header */
      text-align: left;
    }
    .listings .section-container {
      max-width: 1200px;
      margin: 0 auto;
    }
    .section-title {
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 20px;
    }

    /* Filter Bar */
    .filters {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      align-items: flex-end;
      margin-bottom: 30px;
    }
    .filter-item {
      display: flex;
      flex-direction: column;
    }
    .filter-item label {
      font-weight: 500;
      margin-bottom: 5px;
    }
    .filter-item input[type="number"],
    .filter-item input[type="text"],
    .filter-item input[type="date"],
    .filter-item select {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 0.95rem;
      width: 180px;
      max-width: 100%;
    }

    /* Map Container */
    .map-container {
      width: 100%;
      height: 400px;
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 30px;
    }

    /* Listings Grid */
    .listings-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }
    .listing-card {
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s;
      position: relative;
    }
    .listing-card:hover {
      transform: translateY(-5px);
    }
    .listing-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
    .listing-details {
      padding: 15px;
      display: flex;
      flex-direction: column;
      gap: 5px;
    }
    .listing-favorite {
      background: #2a2185;
      color: #fff;
      padding: 5px 10px;
      display: inline-block;
      border-radius: 3px;
      font-size: 0.85rem;
      margin-bottom: 5px;
    }
    .listing-title {
      font-size: 1.2rem;
      color: #333;
      margin-bottom: 5px;
      font-weight: 600;
    }
    .listing-rating {
      color: #2a2185;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 1rem;
    }
    .listing-rating i {
      color: #FFD700; /* Star color (gold) */
    }
    .listing-price {
      font-size: 1rem;
      color: #333;
      font-weight: 500;
    }

    /* Wishlist Icon */
    .wishlist-icon {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 1.3rem;
      color: #fff;
      background-color: rgba(0, 0, 0, 0.4);
      border-radius: 50%;
      width: 35px;
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .wishlist-icon:hover {
      background-color: rgba(0, 0, 0, 0.6);
    }
    .wishlist-icon.active {
      color: #ff4d4d; /* Red heart if active */
    }

    /* Load More */
    .load-more-container {
      text-align: center;
      margin-top: 30px;
    }

    /* -------------------------------- */
    /*             FOOTER               */
    /* -------------------------------- */
    footer {
      text-align: center;
      padding: 20px;
      color: #555;
      font-size: 0.9rem;
      background: #eaeaea;
      margin-top: 40px;
    }

    /* -------------------------------- */
    /*        RESPONSIVE STYLES         */
    /* -------------------------------- */
    @media (max-width: 1024px) {
      header {
        padding: 20px;
      }
      nav ul {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 70px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        width: 200px;
        text-align: left;
        padding: 20px;
        border-radius: 10px;
      }
      nav ul.active {
        display: flex;
      }
      nav ul li {
        margin: 15px 0;
      }
      .menu-toggle {
        display: block;
      }
      .filters {
        flex-direction: column;
        align-items: flex-start;
      }
      .map-container {
        height: 300px;
      }
    }
  </style>
</head>
<body>
  <!-- Header / Navigation -->
  <header>
    <div class="logo">weshPAY</div>
    <button class="menu-toggle" aria-label="Toggle navigation">&#9776;</button>
    <nav>
      <ul>
        <li><a href="#listings">Home</a></li>
        <li><a href="login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Sign In</a></li>
        <li><a href="register.php" class="btn"><i class="fas fa-user-plus"></i> Register</a></li>
      </ul>
    </nav>
  </header>

  <!-- Listings Section -->
  <section class="listings" id="listings">
    <div class="section-container">
      <h2 class="section-title">Bed & Breakfasts</h2>

      <!-- Filter & Sort Bar -->
      <div class="filters">
        <div class="filter-item">
          <label for="locationSearch">Location</label>
          <input type="text" id="locationSearch" placeholder="e.g. Diani Beach">
        </div>
        <div class="filter-item">
          <label for="sortBy">Sort by</label>
          <select id="sortBy">
            <option value="none">None</option>
            <option value="priceLow">Price (Low to High)</option>
            <option value="priceHigh">Price (High to Low)</option>
            <option value="ratingHigh">Rating (High to Low)</option>
          </select>
        </div>
        <div class="filter-item">
          <label for="minPrice">Min Price</label>
          <input type="number" id="minPrice" placeholder="e.g. 1000">
        </div>
        <div class="filter-item">
          <label for="maxPrice">Max Price</label>
          <input type="number" id="maxPrice" placeholder="e.g. 10000">
        </div>
        <div class="filter-item">
          <label for="checkIn">Check-in</label>
          <input type="date" id="checkIn">
        </div>
        <div class="filter-item">
          <label for="checkOut">Check-out</label>
          <input type="date" id="checkOut">
        </div>
        <button class="btn" id="applyFilters">Apply</button>
      </div>

      <!-- Map -->
      <div class="map-container" id="map"></div>

      <!-- Dynamic Listings Grid -->
      <div class="listings-grid" id="listingsGrid">
        <!-- Cards injected by JavaScript -->
      </div>

      <!-- Load More Button -->
      <div class="load-more-container">
        <button class="btn" id="loadMoreBtn">Load More</button>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; <?php echo date("Y"); ?> weshPAY. All rights reserved.</p>
  </footer>

  <!-- Leaflet JS for map integration -->
  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

  <!-- Scripts -->
  <script>
    // Sample data array for listings
    const listingsData = [
      {
        id: 1,
        location: "Diani Beach, Kenya",
        rating: 5.0,
        price: 6206,
        favorite: true,
        wishlist: false,  // whether user has 'hearted' it
        image: "../uploads/landing_page/bedroom.jpg",
        lat: -4.316667,
        lng: 39.583333,
        availabilityStart: "2025-05-01",
        availabilityEnd:   "2025-05-15"
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
        availabilityEnd:   "2025-05-10"
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
        availabilityEnd:   "2025-05-12"
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
        availabilityEnd:   "2025-06-10"
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
        availabilityEnd:   "2025-05-20"
      },
      // Add more listings as needed...
    ];

    // Pagination variables
    let currentPage = 1;
    const listingsPerPage = 3;

    // Leaflet map + markers
    let map, markerGroup;

    // On window load, initialize map & listings
    window.addEventListener('load', () => {
      initMap();
      renderListings(listingsData);
    });

    // Initialize Leaflet map
    function initMap() {
      map = L.map('map').setView([-1.286389, 36.817223], 7); // center on Kenya
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
      }).addTo(map);

      // Layer group to hold all markers
      markerGroup = L.layerGroup().addTo(map);
    }

    // Render listings (with pagination & filters) in the DOM
    function renderListings(fullList) {
      const container = document.getElementById('listingsGrid');
      container.innerHTML = ''; // Clear existing content

      // Determine how many items to show based on pagination
      const paginatedList = fullList.slice(0, currentPage * listingsPerPage);

      // Clear existing markers from map
      markerGroup.clearLayers();

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

      // Show/hide "Load More" button if there's more data
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
      // Re-render with current filters & pagination
      applyFilterAndSort();
    }

    // "Load More" pagination
    document.getElementById('loadMoreBtn').addEventListener('click', () => {
      currentPage++;
      applyFilterAndSort();
    });

    // Filter & Sort logic
    document.getElementById('applyFilters').addEventListener('click', () => {
      // Reset pagination to page 1 on new filter
      currentPage = 1;
      applyFilterAndSort();
    });

    function applyFilterAndSort() {
      // Make a copy of the original data
      let filtered = [...listingsData];

      const locationSearch = document.getElementById('locationSearch').value.trim().toLowerCase();
      const sortBy = document.getElementById('sortBy').value;
      const minPrice = parseInt(document.getElementById('minPrice').value) || 0;
      const maxPrice = parseInt(document.getElementById('maxPrice').value) || Infinity;
      const checkIn = document.getElementById('checkIn').value;
      const checkOut = document.getElementById('checkOut').value;

      // Filter by location
      if (locationSearch) {
        filtered = filtered.filter(item =>
          item.location.toLowerCase().includes(locationSearch)
        );
      }

      // Filter by price range
      filtered = filtered.filter(item => item.price >= minPrice && item.price <= maxPrice);

      // Filter by availability (date range overlap)
      if (checkIn && checkOut) {
        filtered = filtered.filter(item => datesOverlap(
          checkIn,
          checkOut,
          item.availabilityStart,
          item.availabilityEnd
        ));
      }

      // Sort logic
      if (sortBy === 'priceLow') {
        filtered.sort((a, b) => a.price - b.price);
      } else if (sortBy === 'priceHigh') {
        filtered.sort((a, b) => b.price - a.price);
      } else if (sortBy === 'ratingHigh') {
        filtered.sort((a, b) => b.rating - a.rating);
      }

      // Finally, render with new data
      renderListings(filtered);
    }

    // Helper function to check date overlap
    function datesOverlap(checkIn, checkOut, availStart, availEnd) {
      // Convert to numeric timestamps for comparison
      const userStart = new Date(checkIn).getTime();
      const userEnd   = new Date(checkOut).getTime();
      const listingStart = new Date(availStart).getTime();
      const listingEnd   = new Date(availEnd).getTime();

      // Overlap occurs if the listing's start is <= user's end
      // and the listing's end is >= user's start
      return listingStart <= userEnd && listingEnd >= userStart;
    }

    // Mobile Menu Toggle
    const menuToggle = document.querySelector(".menu-toggle");
    const navMenu = document.querySelector("nav ul");

    menuToggle.addEventListener("click", function() {
      navMenu.classList.toggle("active");
    });

    // Close menu when clicking outside
    document.addEventListener("click", function(event) {
      if (!menuToggle.contains(event.target) && !navMenu.contains(event.target)) {
        navMenu.classList.remove("active");
      }
    });
  </script>
</body>
</html>
