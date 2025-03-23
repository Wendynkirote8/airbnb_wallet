<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Wallet Landing Page</title>
    <style>
        /* General Page Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            /* background: url('../uploads/landing_page/bg.png') no-repeat center center fixed; */
            background: linear-gradient(-45deg, #0056b3, #003f7f, #007bff, #002244); 
            /* background-size: 400% 400%; */
            background-size: cover;
            animation: gradientAnimation 10s ease infinite;
            color: white;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2); /* Adjust opacity for better contrast */
            z-index: -1;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Header */
header {
    width: 100%;
    display: flex;
    justify-content: center; /* Center the navbar */
    align-items: center;
    padding: 20px 50px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
        }

        /* Logo */
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0056b3;
            position: absolute;
            left: 50px; /* Keeps the logo on the left */
        }

        /* Navbar */
        nav {
            display: flex;
            justify-content: center;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            display: inline;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
            transition: color 0.3s;
        }

        nav ul li a:hover {
            color: #0056b3;
        }

        /* Mobile Menu */
        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
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

        .hero-content {
            flex: 1;
            padding-right: 50px;
        }

        .hero h1 {
            font-size: 50px;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(-30px);
            transition: opacity 1s ease-in, transform 1s ease-in;
        }

        .hero p {
            font-size: 20px;
            max-width: 500px;
            line-height: 1.5;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(-30px);
            transition: opacity 1s ease-in 0.3s, transform 1s ease-in 0.3s;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #ffcc00;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            transition: background 0.3s, transform 0.2s;
            opacity: 0;
            transform: translateY(-30px);
            transition: opacity 1s ease-in 0.6s, transform 1s ease-in 0.6s;
        }

        .btn:hover {
            background: #ff9900;
            transform: scale(1.05);
        }

        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
            opacity: 0;
            transform: translateX(50px);
            transition: opacity 1s ease-in 0.5s, transform 1s ease-in 0.5s;
        }

        .hero-image img {
            width: 100%;
            max-width: 550px;
            object-fit: cover;
            border-radius: 0px; /* Removed Edges */
        }

        /* Reveal Animation */
        .hero.reveal .hero-image,
        .hero.reveal .hero-content h1,
        .hero.reveal .hero-content p,
        .hero.reveal .hero-content .btn {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding: 50px 20px;
            }

            .hero-content {
                text-align: center;
                padding-right: 0;
                margin-bottom: 30px;
            }

            .hero-image img {
                max-width: 400px;
            }

            nav ul {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 70px;
                right: 50px;
                background: rgba(255, 255, 255, 0.1);
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
                margin: 15px;
            }

            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">E-Wallet</div>
        <div class="menu-toggle">&#9776;</div>
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#blog">Blog</a></li>
                
            </ul>
        </nav>
    </header>

    <section class="hero" id="home">
        <div class="hero-content">
            <h1>E-WALLET</h1>
            <p>Manage your finances easily and securely with our digital wallet system.</p>
            <a href="login.php" class="btn">Sign In</a>
        </div>
        <div class="hero-image">
            <img src="../uploads/landing_page/9895010.jpg" alt="E-Wallet Illustration">
        </div>
    </section>

    <script>
        // Smooth Scroll Effect
        document.querySelectorAll('nav ul li a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 70,
                        behavior: "smooth"
                    });
                }
            });
        });

        // Reveal Hero Section on Load
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
