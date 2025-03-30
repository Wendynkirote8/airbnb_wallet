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
  
  <!-- Google Font: Poppins -->
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
    rel="stylesheet"
  />
  
  <!-- Font Awesome for icons -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
  
  <!-- Leaflet CSS for map integration -->
  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
  />

  <!-- Flatpickr CSS for the date pickers -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
  />

  <!-- External CSS (Adjust path as needed) -->
  <link rel="stylesheet" href="../assets/css/landing.css">

  <style>
    /* -------------------------------- */
    /*          GLOBAL & RESETS         */
    /* -------------------------------- */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f8f8;
      color: #333;
    }
    a {
      text-decoration: none;
      color: inherit;
    }
    button {
      cursor: pointer;
      border: none;
      background: none;
      font-family: inherit;
    }

    /* -------------------------------- */
    /*          TOP NAVIGATION          */
    /* -------------------------------- */
    .top-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 2rem;
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .left-nav, .center-search, .right-nav {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .logo {
      font-size: 1.5rem;
      font-weight: 600;
      color: #2a2185; /* Airbnb-like color */
    }
    .nav-links {
      list-style: none;
      display: flex;
      gap: 1rem;
    }
    .nav-links li a {
      font-weight: 500;
      color: #333;
    }
    .nav-links li a:hover {
      color: #2a2185;
    }

    /* Centered Search Bar */
    .center-search {
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 50px;
      padding: 0.4rem 0.8rem;
      gap: 0.5rem;
    }
    .center-search input {
      border: none;
      outline: none;
      padding: 0.5rem;
      width: 100px;
      max-width: 120px;
      font-size: 0.95rem;
    }
    .center-search input::placeholder {
      color: #999;
    }
    .center-search button {
      background-color: #2a2185;
      color: #fff;
      padding: 0.5rem 0.8rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .center-search button:hover {
      background-color: #2a2185;
    }

    /* Right Nav */
    .right-nav a {
      font-weight: 500;
      color: #333;
      margin-right: 0.5rem;
    }
    .right-nav a:hover {
      color: #2a2185;
    }

    /* Profile Dropdown Container */
    .profile-container {
      position: relative;
    }
    .profile-dropdown {
      position: absolute;
      top: 45px; /* adjust to your header height */
      right: 0;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      width: 200px;
      display: none;
      flex-direction: column;
      padding: 0.5rem 0;
      z-index: 9999;
    }
    .profile-dropdown.show {
      display: flex;
    }
    .profile-dropdown a {
      display: block;
      padding: 0.5rem 1rem;
      color: #333;
      font-size: 0.95rem;
    }
    .profile-dropdown a:hover {
      background-color: #f7f7f7;
    }
    .profile-dropdown hr {
      margin: 0.5rem 0;
      border: none;
      border-top: 1px solid #eee;
    }

    /* -------------------------------- */
    /*        CATEGORY (TAB) BAR        */
    /* -------------------------------- */
    .category-bar {
      display: flex;
      align-items: center;
      gap: 1rem;
      overflow-x: auto; /* scroll if many items */
      padding: 0.8rem 2rem;
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .category-bar button {
      border: none;
      background-color: #fff;
      padding: 0.4rem 1rem;
      border-radius: 20px;
      font-size: 0.9rem;
      white-space: nowrap;
      transition: background-color 0.2s;
    }
    .category-bar button:hover {
      background-color: #f0f0f0;
    }
    .filters-btn {
      margin-left: auto; /* push it to the right end */
      border: 1px solid #ccc;
    }

    /* -------------------------------- */
    /*         LISTINGS SECTION         */
    /* -------------------------------- */
    .listings {
      max-width: 1200px;
      margin: 1.5rem auto;
      padding: 0 1rem;
    }
    .section-title {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    .map-container {
      width: 100%;
      height: 400px;
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 2rem;
    }
    .listings-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
      gap: 1.5rem;
    }
    /* Property Card */
    .listing-card {
      background-color: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      transition: transform 0.2s;
      position: relative;
    }
    .listing-card:hover {
      transform: translateY(-3px);
    }
    .listing-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .listing-details {
      padding: 1rem;
      flex: 1;
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
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 5px;
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
      color: #FFD700; /* Star color */
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
      padding: 1rem;
      background-color: #fff;
      color: #666;
      font-size: 0.9rem;
      margin-top: 2rem;
    }

    /* -------------------------------- */
    /*          REGION MODAL            */
    /* -------------------------------- */
    .region-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      display: none; /* Hidden by default */
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    .region-modal.show {
      display: flex; /* display flex so we can center content */
    }
    .region-modal-content {
      background-color: #fff;
      width: 90%;
      max-width: 700px;
      border-radius: 8px;
      padding: 1rem;
      position: relative;
    }
    .region-modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1rem;
    }
    .region-modal-header h2 {
      font-size: 1.2rem;
      font-weight: 600;
    }
    .region-modal-close {
      font-size: 1.5rem;
      cursor: pointer;
    }
    .region-modal-body {
      font-size: 0.95rem;
      color: #333;
    }
    .region-tabs {
      display: flex;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .region-tab {
      background-color: transparent;
      border: none;
      padding: 0.5rem 1rem;
      font-size: 0.9rem;
      border-radius: 20px;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    .region-tab.active {
      background-color: #f0f0f0;
    }
    .region-tab:hover {
      background-color: #f0f0f0;
    }
    .region-tab-content {
      display: block;
    }
    .translation-toggle {
      margin-bottom: 1rem;
    }
    .toggle-label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .toggle-slider {
      display: inline-block;
      width: 40px;
      height: 22px;
      background-color: #ccc;
      border-radius: 22px;
      position: relative;
      vertical-align: middle;
      margin-left: 0.5rem;
    }
    .toggle-slider::before {
      content: "";
      position: absolute;
      width: 18px;
      height: 18px;
      top: 2px;
      left: 2px;
      background-color: #fff;
      border-radius: 50%;
      transition: transform 0.3s;
    }
    #translationToggle:checked + .toggle-slider::before {
      transform: translateX(18px);
    }
    #translationToggle:checked + .toggle-slider {
      background-color: #2a2185; /* your theme color */
    }
    .language-list, .currency-list {
      list-style: none;
      margin-top: 0.5rem;
      padding-left: 0;
    }
    .language-list li, .currency-list li {
      margin-bottom: 0.5rem;
    }

    /* -------------------------------- */
    /*        FLATPICKR OVERRIDES       */
    /* -------------------------------- */
    /* Use Poppins in the calendar */
    .flatpickr-calendar,
    .flatpickr-calendar * {
      font-family: 'Poppins', sans-serif !important;
    }
    /* Calendar container styling */
    .flatpickr-calendar {
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    /* Top month navigation bar */
    .flatpickr-months {
      display: flex;
      justify-content: space-between;
      padding: 0.5rem 1rem;
      background: #fff;
    }
    .flatpickr-months .flatpickr-month {
      margin: 0 0.5rem;
    }
    .flatpickr-months .flatpickr-prev-month,
    .flatpickr-months .flatpickr-next-month {
      line-height: 1;
      cursor: pointer;
      color: #333;
      opacity: 0.8;
    }
    .flatpickr-months .flatpickr-prev-month:hover,
    .flatpickr-months .flatpickr-next-month:hover {
      opacity: 1;
    }
    /* Month & Year text */
    .cur-month {
      font-weight: 600;
      font-size: 1rem;
    }
    .cur-year {
      font-size: 0.9rem;
    }
    /* Weekday header (Mon, Tue, etc.) */
    .flatpickr-weekdays {
      background: #fff;
      font-size: 0.85rem;
      font-weight: 500;
      border-bottom: 1px solid #eee;
    }
    /* Individual day cells */
    .flatpickr-day {
      height: 36px;
      line-height: 36px;
      width: 36px;
      margin: 0.1rem;
      font-size: 0.9rem;
      border-radius: 50%;
      transition: background 0.2s;
    }
    /* Hover effect */
    .flatpickr-day:hover {
      background: #f0f0f0;
    }
    /* Selected date range styling */
    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
      background: #2a2185 !important; /* or your brand color */
      color: #fff !important;
    }
    /* Range in-between highlight */
    .flatpickr-day.inRange {
      background: rgba(42, 33, 133, 0.15);
      color: #333;
    }
    /* Disabled days (past, etc.) */
    .flatpickr-day.disabled {
      color: #ccc;
      cursor: not-allowed;
    }
    /* Make sure alt input has consistent font */
    .flatpickr-input[readonly] {
      cursor: pointer;
    }
  </style>
</head>
<body>

  <!-- TOP NAV BAR -->
  <header class="top-header">
    <!-- Left side: Logo + "Homes" / "Experiences" -->
    <div class="left-nav">
      <div class="logo">weshPAY</div>
      <ul class="nav-links">
        <li><a href="landing.php">Homes</a></li>
        <li><a href="#">Experiences</a></li>
      </ul>
    </div>

    <!-- Centered Search Bar (Single Range Input) -->
    <div class="center-search">
      <input type="text" id="searchLocation" placeholder="Where (Map area)" />
      <input type="text" id="searchDateRange" placeholder="Add dates" />
      <input type="text" id="searchGuests" placeholder="Add guests" />
      <button id="searchBtn"><i class="fas fa-search"></i></button>
    </div>

    <!-- Right side: "Pay for your room", globe, user icon, etc. -->
    <div class="right-nav">
      <a href="login.php">Pay for your room</a>
      <i class="fas fa-globe" id="regionIcon"></i>
      
      <!-- Profile container with dropdown -->
      <div class="profile-container">
        <i class="fas fa-user-circle fa-lg" id="profileIcon"></i>
        <div class="profile-dropdown" id="profileDropdown">
          <a href="register.php">Sign up</a>
          <a href="login.php">Log in</a>
          <hr />
          <a href="#">Gift cards</a>
          <a href="#">Airbnb your home</a>
          <a href="#">Host an experience</a>
          <a href="#">Help Center</a>
        </div>
      </div>
    </div>
  </header>

  <!-- CATEGORY BAR (TABS) -->
  <div class="category-bar">
    <button>Bed & Breakfasts</button>
    <button>Tiny Homes</button>
    <button>Countryside</button>
    <button>Beach</button>
    <button>Beachfront</button>
    <button>Luxe</button>
    <button>Amazing views</button>
    <!-- etc... -->
    <button class="filters-btn">Filters</button>
  </div>

  <!-- Region / Language Modal -->
  <div class="region-modal" id="regionModal">
    <div class="region-modal-content">
      <div class="region-modal-header">
        <h2>Language and region</h2>
        <span class="region-modal-close" id="regionClose">&times;</span>
      </div>
      <div class="region-modal-body">
        <!-- Tabs -->
        <div class="region-tabs">
          <button class="region-tab active" data-target="langTab">Language and region</button>
          <button class="region-tab" data-target="currencyTab">Currency</button>
        </div>

        <!-- Tab Content: Language & Region -->
        <div class="region-tab-content" id="langTab">
          <div class="translation-toggle">
            <label class="toggle-label" for="translationToggle">
              Translation
              <input type="checkbox" id="translationToggle" />
              <span class="toggle-slider"></span>
            </label>
            <p>Automatically translate descriptions and reviews to English.</p>
          </div>

          <h3>Choose a language and region</h3>
          <ul class="language-list">
            <li>English (United States)</li>
            <li>English (Canada)</li>
            <li>Bahasa Indonesia</li>
            <li>Deutsch</li>
            <li>Español</li>
            <li>Français</li>
            <!-- Add as many as you like... -->
          </ul>
        </div>

        <!-- Tab Content: Currency -->
        <div class="region-tab-content" id="currencyTab" style="display: none;">
          <h3>Select your currency</h3>
          <ul class="currency-list">
            <li>USD – US Dollar</li>
            <li>EUR – Euro</li>
            <li>KSH – Kenyan Shilling</li>
            <li>GBP – British Pound</li>
            <li>AUD – Australian Dollar</li>
            <!-- etc... -->
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- LISTINGS SECTION -->
  <section class="listings" id="listings">
    <h2 class="section-title">Explore Stays</h2>

    <!-- Map -->
    <div class="map-container" id="map"></div>

    <!-- Dynamic Listings Grid -->
    <div class="listings-grid" id="listingsGrid">
      <!-- Listing cards injected by JavaScript -->
    </div>

    <!-- Load More Button -->
    <div class="load-more-container">
      <button class="btn" id="loadMoreBtn">Load More</button>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    &copy; <?php echo date("Y"); ?> weshPAY. All rights reserved.
  </footer>

  <!-- Leaflet JS for map integration -->
  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

  <!-- Flatpickr JS for the date pickers -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="../assets/js/landing.js"></script>

  <script>
    
  </script>
</body>
</html>
