<?php
session_start();
$isLoggedIn = isset($_SESSION['userID']) && $_SESSION['role'] === 'customer';
?>


<style>
    /* header */

    .logo-font {
        font-family: "Quicksand", sans-serif;
        font-size: 1.6rem;
        font-weight: 400;
        color: #ffffff;
    }

    .logo-font:hover {
        text-shadow: 1px 1px 6px rgb(255, 255, 255);
    }

    .navbar {
        /* position: sticky;
  top: 0;
  z-index: 1000; */

        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 32px;
        /* background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(2px);
        box-shadow: 0 8px 30px rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        margin-left: 30px;
        margin-right: 30px; */
    }

    .nav-link {
        font-family: "Quicksand", sans-serif !important;
        font-size: large;
        font-weight: 400;
        color: white;
        text-decoration: none;
        margin: 0 12px;
        position: relative;
        transition: all 0.3s ease;
    }


    /* Hover underline */
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 0%;
        height: 2px;
        background-color: #FF0000;
        transition: width 0.3s ease;
    }

    .nav-link:hover::after {
        width: 100%;
    }

    /* Optional hover text color */
    .nav-link:hover {
        color: #FF0000;
    }

    /* Cart Icon*/
    .cart-icon {
        display: flex;
        align-items: center;
        margin-left: 1rem;
        transition: transform 0.3s ease;
    }


    .cart-icon:hover {
        transform: scale(1.1);
    }

    .dropdown-menu {
        position: absolute;
        margin-top: 0.5rem;
        width: 12rem;
        /* w-48 */
        border-radius: 0.75rem;
        /* rounded-xl */
        z-index: 50;
        background-color: rgba(255, 255, 255, 1);
        /* bg-white/20 */
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        /* shadow-2xl */
        border: 1px solid rgba(255, 255, 255, 0.1);
        /* border-white/10 */
        backdrop-filter: blur(10px);
        /* blur effect */
        border: 1px solid rgba(255, 255, 255, 0.1);
        /* ring-white/10 */
    }

    .dropdown-item {
        display: block;
        padding: 0.625rem 1.25rem;
        /* px-5 py-2.5 */
        color: black;
        text-decoration: none;
        border-radius: 0.375rem;
        /* rounded-md */
        transition: background-color 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: rgba(179, 179, 179, 1);
        /* hover:bg-white/10 */
    }


    /* Icon dropdown container */
    .icon-dropdown {
        position: absolute;
        top: 3rem;
        right: 0;
        background: rgba(255, 255, 255, 1);
        backdrop-filter: blur(8px);
        border-radius: 12px;
        padding: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        flex-direction: column;
        gap: 12px;
        z-index: 100;
    }

    /* Each icon link */
    .icon-dropdown a {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: black;
        font-family: "Quicksand", sans-serif;
        font-size: 0.85rem;
        transition: transform 0.2s ease;
    }

    .icon-dropdown a:hover {
        transform: scale(1.05);
    }

    .icon-dropdown img {
        width: 32px;
        height: 32px;
    }
</style>

<!-- Google font -->
<link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">

<!-- Alpine js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="w-full flex justify-between items-center">
        <!-- real nav starts here -->
        <div class="flex items-center space-x-2 animate-logo">
            <img src="/images/oystergif.gif" alt="waterpets logo" class="h-11 w-11" />
            <span class="h-8 border-l-2 border-blue-500 mx-3"></span>
            <a href="#" class="logo-font animate-slide-in-left" data-sound="/sounds/notification_pop.mp3">WaterPets</a>
        </div>

        <!-- Sidebar toggle and sound on small screens -->
        <div class="flex items-center space-x-4 md:hidden" x-data="{ open: false }">
            <!-- Sound Toggle Button -->
            <button id="sound-toggle-mobile" class="focus:outline-none" title="Toggle Sound">
                <img id="sound-icon-mobile" src="/images/volume.png" alt="Sound Icon" class="h-7 w-7" />
            </button>

            <!-- Hamburger Toggle Button -->
            <button @click="open = !open" class="text-white focus:outline-none border border-white rounded-full p-2 hover:border-blue-400 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Mobile Sidebar -->
            <div
                x-show="open"
                x-transition
                class="fixed top-0 right-0 h-full w-64 
         bg-blue-950/80 text-white 
         backdrop-blur-md shadow-2xl 
         border-l border-blue-500/40 
         p-6 rounded-l-2xl z-50">

                <button @click="open = false" class="absolute top-4 right-4 text-xl">&times;</button>

                <a href="home.php" class="block py-2">Home</a>
                <a href="fish.php" class="block py-2">Fish</a>
                <a href="plants.php" class="block py-2">Coral Reefs</a>
                <a href="supplies.php" class="block py-2">Supplies</a>
                <a href="equipment.php" class="block py-2">Equipment</a>
                <a href="about.php" class="block py-2">About</a>
                <a href="contact.php" class="block py-2">Contact</a>
                <a href="cart.php" class="block py-2">Cart</a>
                <?php if ($isLoggedIn): ?>
                    <a href="userProfile.php" class="block py-2">Profile</a>
                    <a href="logout.php" class="block py-2">Logout</a>
                <?php else: ?>
                    <a href="userLogin.php" class="block py-2">Login</a>
                    <a href="register.php" class="block py-2">Register</a>
                <?php endif; ?>

            </div>
        </div>

        <!-- mobile sidebar nav ends here -->


        <div class="hidden md:flex items-center space-x-6" x-data="{ open: false }">
            <a href="home.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">Home</a>

            <!-- Dropdown Menu for Shop -->
            <div class="relative" x-data="{ open: false }">
                <button class="nav-link" @click="open = !open">
                    Shop
                </button>

                <!-- Dropdown Items -->
                <div x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="dropdown-menu">
                    <a href="fish.php" class="dropdown-item" data-sound="/sounds/notification_pop.mp3">Fish</a>
                    <a href="plants.php" class="dropdown-item" data-sound="/sounds/notification_pop.mp3">Coral Reefs</a>
                    <a href="supplies.php" class="dropdown-item" data-sound="/sounds/notification_pop.mp3">Supplies</a>
                    <a href="equipment.php" class="dropdown-item" data-sound="/sounds/notification_pop.mp3">Equipment</a>
                </div>
            </div>


            <a href="about.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">Guide</a>
            <a href="about.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">About</a>
            <a href="contact.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">Contact</a>
        </div>


        <!-- Desktop Right side -->
        <div class="hidden md:flex items-center space-x-3 relative" x-data="{ menuOpen: false }">

            <!-- Sound Toggle -->
            <button id="sound-toggle" class="focus:outline-none" title="Toggle Sound">
                <img id="sound-icon" src="/images/volume.png" alt="Sound Icon" class="h-8 w-8" />
            </button>

            <?php if (isset($_SESSION['userID']) && $_SESSION['role'] === 'customer'): ?>
                <a href="userProfile.php" title="Profile">
                    <img src="<?= $_SESSION['profile_pic'] ?? '/images/user.png' ?>" alt="Profile" class="h-8 w-8 rounded-full border border-white hover:scale-105 transition" />
                </a>
            <?php endif; ?>


            <!-- Menu Icon -->
            <button @click="menuOpen = !menuOpen" class="focus:outline-none">
                <img src="/images/menu.png" alt="Menu" class="h-8 w-8" />
            </button>

            <!-- Dropdown -->
            <div x-show="menuOpen" @click.outside="menuOpen = false" x-transition class="icon-dropdown">
                <a href="cart.php">
                    <img src="/images/cart.png" alt="Cart">
                    <span>Cart</span>
                </a>
                <a href="wishlist.php">
                    <img src="/images/wishlist.png" alt="Wishlist">
                    <span>Wishlist</span>
                </a>
                <?php if ($isLoggedIn): ?>
                    <a href="logout.php">
                        <img src="/images/logout.png" alt="Logout">
                        <span>Logout</span>
                    </a>
                <?php else: ?>
                    <a href="userLogin.php">
                        <img src="/images/user.png" alt="Login">
                        <span>Login</span>
                    </a>
                    <a href="register.php">
                        <img src="/images/register.png" alt="Register">
                        <span>Register</span>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>