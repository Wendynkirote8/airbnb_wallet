<?php
session_start();
require_once '../config/db_connect.php';

// Fetch rooms from the database. Adjust the ORDER BY clause as needed.
$sql = "SELECT * FROM rooms ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>weshPAY – Airbnb E-Wallet</title>
  <meta name="description" content="Manage your vacation finances easily and securely with the new weshPAY E-Wallet for Airbnb.">
  
  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
  <!-- Leaflet CSS for map integration -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

  <!-- Flatpickr CSS for the date pickers -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />

  <!-- External CSS (Adjust path as needed) -->
  <link rel="stylesheet" href="../assets/css/landing.css">

  <style>
    /* (Your existing inline CSS styles here) */
  </style>
</head>
<body>

  <!-- TOP NAV BAR -->
  <header class="top-header">
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

    <div class="right-nav">
      <a href="login.php">Pay for your room</a>
      <i class="fas fa-globe" id="regionIcon"></i>
      <!-- Profile Dropdown Container -->
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
      <?php if (!empty($rooms)): ?>
        <?php foreach ($rooms as $room): ?>
          <div class="listing-card">
            <!-- Use room 'image' column for the featured image -->
            <img src="<?php echo !empty($room['image']) ? $room['image'] : 'https://via.placeholder.com/800x600?text=No+Image'; ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" />
            <div class="listing-details">
              <div class="listing-title"><?php echo htmlspecialchars($room['name']); ?></div>
              <div class="listing-price">Ksh <?php echo number_format($room['price'], 2); ?>/night</div>
              <!-- Link to room details page -->
              <a href="room_details.php?id=<?php echo $room['id']; ?>">View Details</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No rooms available at the moment.</p>
      <?php endif; ?>
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
    // (Your custom JavaScript code here, if any)
  </script>
</body>
</html>
