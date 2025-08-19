<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - WaterPets</title>
    <link rel="stylesheet" href="/css/about_style.css">

    <!-- Tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
</head>

<body>

    <section class="about-section">
        <?php include 'header.php'; ?>
        <hr class="header-divider">

        <!-- About Page Content -->
        <div class="about-page">


            <!-- Hero / Banner Section -->
            <section class="about-hero">
                <div class="about-hero-text">
                    <h1>About WaterPets</h1>
                    <p>Discover the world of aquatics with WaterPets – your ultimate online aquarium store.</p>
                </div>
            </section>

            <!-- Our Story Section -->
            <section class="about-story">
                <h2>Our Story</h2>
                <p>
                    WaterPets started with a passion for aquatics. We provide high-quality fish, corals, aquarium plants, and equipment to help you create and maintain your perfect underwater world. Our team is dedicated to delivering the best products and advice to ensure a happy and healthy aquatic environment.
                </p>
            </section>

            <!-- Our Mission Section -->
            <section class="about-mission">
                <h2>Our Mission</h2>
                <p>
                    Our mission is to make aquarium keeping easy, enjoyable, and accessible for everyone. We aim to provide premium products, expert guidance, and excellent customer service for both beginners and experienced aquarium enthusiasts.
                </p>
            </section>

            <!-- Our Values Section -->
            <section class="about-values">
                <h2>Our Values</h2>
                <ul>
                    <li>Quality and Sustainability: Providing safe and healthy aquatic products.</li>
                    <li>Customer Satisfaction: Helping you create the perfect aquarium experience.</li>
                    <li>Knowledge Sharing: Sharing tips and guidance for successful aquarium keeping.</li>
                </ul>
            </section>

        </div>

        <!-- Fullscreen Stats Section -->
        <section class="about-stats-bg">
            <div class="stats-overlay">
                <div class="stats-container">
                    <div class="stat-card">
                        <h3>1+</h3>
                        <p>WaterPets is 1 year old.</p>
                    </div>

                    <div class="stat-card">
                        <h3>3452</h3>
                        <p>Contributors</p>
                    </div>

                    <div class="stat-card">
                        <h3>1.1</h3>
                        <p>Current version</p>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <?php include 'footer.php'; ?>
    <?php include 'chatbot.php'; ?>
    <?php include 'backToTop.php'; ?>

</body>

</html>