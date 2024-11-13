<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Buddy Finder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="land_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <h1>Your travel companion just a click away...</h1>
    </header>

    <nav>
        <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#know">Know More</a></li>
            <li class="dropdown">
                <a href="#contact" class="dropbtn">Contact Us</a>
                <div class="dropdown-content">
                    <a href="tel:+1234567890">Phone: +123 456 7890</a>
                    <a href="mailto:info@example.com">Email: info@example.com</a>
                </div>
            </li>
        </ul>
        <button id="login" onclick="window.location.href='login.php'">Login/Sign up</button>
    </nav>

    <!-- Home Section -->
    <section id="home">
        <h1>Where do you want to go?</h1>
        <form class="example" action="people2.php" method="POST">
            <input type="text" placeholder="Search for a travel buddy..." name="search" id="search" required>
            <button type="submit"><i class="fa fa-search"></i></button>
        </form>
    </section>

    <!-- About Section -->
    <section id="about">
        <h1>About Us</h1>
        <div class="cont1">
            <div class="ab">
                <p>
                    At Travel Buddy Finder, we believe that travel is more enjoyable when shared with the right companion. 
                    While some love the freedom of solo adventures, others crave the companionship of a fellow explorer. 
                    Finding someone to travel with, who shares your interests and travel style, can be challenging. That’s where we step in!
                </p>
                <p>
                    Our platform connects travelers like you with potential travel buddies based on shared interests, destinations, 
                    and preferences. Whether you're planning a backpacking trip, a luxury vacation, or a weekend getaway, 
                    Travel Buddy Finder helps you find the perfect companion to make your journey more meaningful and memorable.
                </p>
                <p>
                    Join our community, and turn your solo plans into shared adventures. Discover new destinations, 
                    meet new friends, and create unforgettable memories with Travel Buddy Finder!
                </p>
            </div>
            <div class="im">
                <img src="about_b.png" alt="Traveling Companions">
            </div>
        </div>
    </section>

    <section id="know">
        <div class="know-overlay">
            <div class="curly">
                <div class="users">
                    <div class="us">
                        <img src="solo.jpg" alt="Solo Travelers">
                        <h2>Solo Travelers</h2>
                    </div>
                    <div class="us">
                        <img src="bag.jpg" alt="Backpackers">
                        <h2>Backpackers</h2>
                    </div>
                    <div class="us">
                        <img src="dig.jpg" alt="Digital Nomads">
                        <h2>Digital Nomads</h2>
                    </div>
                </div>
                <div class="curl">
                    <p>&#x2775;</p>
                </div>
                <div class="user_h">
                    <h1>Users</h1>
                </div>
                <div class="features">
                    <h1>Features</h1>
                    <ul>
                        <li>User profiles with interests and travel preferences</li>
                        <li>Matching algorithm for finding compatible travel buddies</li>
                        <li>Chat and messaging system</li>
                        <li>User reviews and ratings</li>
                        <li>Safety and verification features</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <footer id="abf">
        <div class="footer-content">
            <div class="footer-left">
                <p>Connecting travelers worldwide. Discover your next travel buddy today!</p>
            </div>
            <div class="footer-right">
                <h3>Contact Us</h3>
                <p>Phone: +123 456 7890</p>
                <p>Email: info@example.com</p>
                <div class="social-icons">
                    <a href="#" class="fa fa-facebook"></a>
                    <a href="#" class="fa fa-twitter"></a>
                    <a href="#" class="fa fa-instagram"></a>
                    <a href="#" class="fa fa-linkedin"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Travel Buddy Finder. All rights reserved.</p>
        </div>
    </footer>

    <script src="landing.js"></script>
</body>
</html>
