<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>weshPAY – Airbnb E-Wallet</title>
  <meta name="description" content="Manage your vacation finances easily and securely with the new weshPAY E-Wallet for Airbnb." />
  <link rel="icon" href="path/to/favicon.ico" />

  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  
  <!-- Font Awesome CDN for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <style>
    /* CSS Reset */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    /* Base Styles */
    body {
      font-family: 'Poppins', sans-serif;
      color: #333;
      background: linear-gradient(45deg, #f7f7f7, #eaeaea);
      background-size: cover;
      position: relative;
      min-height: 100vh;
      scroll-behavior: smooth;
    }

    /* Header */
    header {
      width: 100%;
      padding: 20px 50px;
      background: rgba(255, 255, 255, 0.9);
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
      font-size: 24px;
      font-weight: bold;
      color: #333;
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
      color: #FF5A5F;
    }

    .menu-toggle {
      display: none;
      font-size: 28px;
      cursor: pointer;
      background: none;
      border: none;
      color: #333;
    }

    /* Styling for Sign In/Register Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: #FF5A5F;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      font-size: 1rem;
      font-weight: 600;
      transition: background 0.3s, transform 0.2s;
    }

    .btn:hover {
      background: #e14b50;
      transform: scale(1.05);
    }

    /* Hero Section */
    .hero {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 100vh;
      padding: 100px 50px;
      text-align: left;
    }

    /* Hero Image on Left */
    .hero-image {
      flex: 1;
      display: flex;
      justify-content: center;
      opacity: 0;
      transform: translateX(-50px);
      transition: opacity 1s ease-in 0.5s, transform 1s ease-in 0.5s;
    }

    .hero-image img {
      width: 100%;
      max-width: 550px;
      object-fit: cover;
      border-radius: 10px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .hero-content {
      flex: 1;
      padding-left: 50px;
    }

    .hero-content h1 {
      font-size: 3rem;
      margin-bottom: 20px;
      opacity: 0;
      transform: translateX(50px);
      transition: opacity 1s ease-in, transform 1s ease-in;
      color: #333;
    }

    .hero-content p {
      font-size: 1.1rem;
      max-width: 500px;
      line-height: 1.6;
      margin-bottom: 20px;
      opacity: 0;
      transform: translateX(50px);
      transition: opacity 1s ease-in 0.3s, transform 1s ease-in 0.3s;
      color: #555;
    }

    /* Reveal Animation */
    .hero.reveal .hero-image,
    .hero.reveal .hero-content h1,
    .hero.reveal .hero-content p,
    .hero.reveal .btn {
      opacity: 1;
      transform: translateX(0);
    }

    /* Section Containers */
    section {
      padding: 80px 50px;
    }

    .section-container {
      max-width: 1200px;
      margin: 0 auto;
      text-align: center;
    }

    .section-title {
      font-size: 2rem;
      margin-bottom: 40px;
      color: #333;
      font-weight: 600;
    }

    .section-content {
      color: #555;
      line-height: 1.6;
      font-size: 1rem;
      max-width: 800px;
      margin: 0 auto;
    }

    /* About Section with Image */
    .about-flex {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 40px;
      margin-top: 40px;
    }

    .about-image {
      flex: 1;
    }

    .about-image img {
      width: 100%;
      max-width: 500px;
      border-radius: 10px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .about-text {
      flex: 1;
      text-align: left;
    }

    /* Services Grid with Icons */
    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
      margin-top: 40px;
    }

    .service-card {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      color: #333;
      transition: transform 0.3s;
      text-align: center;
    }

    .service-card:hover {
      transform: translateY(-5px);
    }

    .service-icon {
      width: 50px;
      margin-bottom: 10px;
    }

    .service-card h3 {
      margin-bottom: 10px;
      font-size: 1.2rem;
    }

    /* Featured Blog Card */
    .blog-featured {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 40px;
      margin-top: 40px;
      flex-wrap: wrap;
    }

    .blog-image {
      flex: 1;
    }

    .blog-image img {
      width: 100%;
      max-width: 500px;
      border-radius: 10px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .blog-text {
      flex: 1;
      text-align: left;
    }

    /* Responsive Styles */
    @media (max-width: 1024px) {
      header {
        padding: 20px;
      }

      .hero {
        flex-direction: column;
        text-align: center;
        padding: 70px 20px;
      }

      .hero-content {
        padding-left: 0;
        margin-top: 30px;
      }

      .hero-content h1 {
        font-size: 2.2rem;
      }

      .hero-content p {
        font-size: 1rem;
      }

      .hero-image img {
        max-width: 400px;
      }

      nav ul {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 70px;
        right: 20px;
        background: rgba(255, 255, 255, 0.9);
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

      .about-flex, .blog-featured {
        flex-direction: column;
        text-align: center;
      }
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 20px;
      color: #555;
      font-size: 0.9rem;
      background: #eaeaea;
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
        <li><a href="#home">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#blog">Blog</a></li>
        <!-- Sign In Button with Icon -->
        <li><a href="login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Sign In</a></li>
        <!-- Register Button with Icon -->
        <li><a href="register.php" class="btn"><i class="fas fa-user-plus"></i> Register</a></li>
      </ul>
    </nav>
  </header>

  <!-- Main Content -->
  <main>
    <!-- Hero Section -->
    <section class="hero" id="home">
      <div class="hero-image">
        <!-- Replace with your desired beach image URL -->
        <img src="../uploads/landing_page/beach.jpg" alt="Beautiful beach view" />
      </div>
      <div class="hero-content">
        <h1>Welcome to weshPAY</h1>
        <p>
          Manage your vacation finances easily and securely with the new
          <strong>weshPAY E-Wallet</strong> for Airbnb. Designed to integrate
          seamlessly into your Airbnb experience, our digital wallet system
          simplifies every step of the payment process. Whether you’re saving
          up for your next getaway or collecting earnings as a host, weshPAY
          delivers fast, transparent transactions and robust security to protect
          your funds. Enjoy the freedom to focus on discovering new destinations
          and creating unforgettable memories—let weshPAY handle the rest.
          Experience the future of travel payments, all in one convenient place.
        </p>
      </div>
    </section>

    <!-- About Section -->
    <section id="about">
      <div class="section-container">
        <h2 class="section-title">About weshPAY</h2>
        <div class="about-flex">
          <div class="about-image">
            <!-- Replace with an image that represents travel or financial ease -->
            <img src="../uploads/landing_page/about.jpg" alt="About weshPAY" />
          </div>
          <div class="about-text">
            <p class="section-content">
              weshPAY was built to streamline your financial transactions while you travel.
              We believe in simple, secure, and transparent payment solutions so you can
              focus on exploring. Our E-Wallet is tailored to fit perfectly into your Airbnb
              journey—whether you're planning your dream vacation or hosting guests from
              around the world.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="services">
      <div class="section-container">
        <h2 class="section-title">Our Services</h2>
        <div class="services-grid">
          <div class="service-card">
            <img src="../uploads/landing_page/instant_pay_icon.png" alt="Instant Payments Icon" class="service-icon" />
            <h3>Instant Payments</h3>
            <p>Send and receive funds in seconds, with minimal fees and zero hassle.</p>
          </div>
          <div class="service-card">
            <img src="../uploads/landing_page/security_icon.png" alt="Security First Icon" class="service-icon" />
            <h3>Security First</h3>
            <p>Robust encryption and advanced fraud detection keep your money safe.</p>
          </div>
          <div class="service-card">
            <img src="../uploads/landing_page/integration_icon.png" alt="Seamless Integration Icon" class="service-icon" />
            <h3>Seamless Integration</h3>
            <p>Works hand-in-hand with Airbnb bookings, allowing easy tracking of every transaction.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Blog Section -->
    <section id="blog">
      <div class="section-container">
        <h2 class="section-title">Latest Blog Posts</h2>
        <div class="blog-featured">
          <div class="blog-image">
            <!-- Replace with your featured blog image -->
            <img src="../uploads/landing_page/blog_featured.jpg" alt="Featured Blog Post" />
          </div>
          <div class="blog-text">
            <p class="section-content">
              Coming soon: Expert tips on managing your travel finances, saving for dream destinations,
              and making the most out of weshPAY’s advanced features. Stay tuned for insightful articles
              that empower you to travel smart and live free.
            </p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer>
    <p>&copy; <?php echo date("Y"); ?> weshPAY. All rights reserved.</p>
  </footer>

  <!-- Scripts -->
  <script>
    // Smooth Scroll for navigation links (fallback for older browsers)
    document.querySelectorAll('nav ul li a').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const targetId = this.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
          e.preventDefault();
          window.scrollTo({
            top: targetElement.offsetTop - 70,
            behavior: "smooth"
          });
        }
      });
    });

    // Reveal Hero Section on load
    window.addEventListener("load", function() {
      document.querySelector(".hero").classList.add("reveal");
    });

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
