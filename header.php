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
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(2px);
        box-shadow: 0 8px 30px rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        margin-left: 30px;
        margin-right: 30px;
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
        background-color: rgba(255, 255, 255, 0.2);
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
        background-color: rgba(255, 255, 255, 0.1);
        /* hover:bg-white/10 */
    }
</style>

<!-- Google font -->
<link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">

<!-- Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Navbar + Sidebar in same x-data -->
<div x-data="{ open: false }">
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="w-full flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-2 animate-logo">
                <img src="/images/oystergif.gif" alt="waterpets logo" class="h-11 w-11" />
                <span class="h-8 border-l-2 border-blue-500 mx-3"></span>
                <a href="#" class="logo-font animate-slide-in-left" data-sound="/sounds/notification_pop.mp3">WaterPets</a>
            </div>

            <!-- Mobile toggle buttons -->
            <div class="flex items-center space-x-4 md:hidden">
                <!-- Sound Toggle -->
                <button id="sound-toggle-mobile" class="focus:outline-none" title="Toggle Sound">
                    <img id="sound-icon-mobile" src="/images/volume.png" alt="Sound Icon" class="h-7 w-7" />
                </button>

                <!-- Hamburger Toggle -->
                <button @click="open = !open"
                    class="text-white focus:outline-none border border-white rounded-full p-2 hover:border-blue-400 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Desktop menu -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="home.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">Home</a>

                <!-- Shop dropdown -->
                <div class="relative" x-data="{ shopOpen: false }">
                    <button class="nav-link" @click="shopOpen = !shopOpen">
                        Shop
                    </button>
                    <div x-show="shopOpen" @click.outside="shopOpen = false" x-transition class="dropdown-menu">
                        <a href="fish.php" class="dropdown-item">Fish</a>
                        <a href="plants.php" class="dropdown-item">Coral Reefs</a>
                        <a href="supplies.php" class="dropdown-item">Supplies</a>
                        <a href="equipment.php" class="dropdown-item">Equipment</a>
                    </div>
                </div>

                <a href="about.php" class="nav-link">Guide</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="contact.php" class="nav-link">Contact</a>
            </div>

            <!-- Desktop right side -->
            <div class="hidden md:flex">
                <a href="cart.php" class="nav-link cart-icon">
                    <img src="/images/cart.png" alt="Cart" class="h-8 w-8" />
                </a>
                <a href="#" class="nav-link" data-sound="/sounds/notification_pop.mp3">Login</a>
                <a href="#" class="nav-link" data-sound="/sounds/notification_pop.mp3">Register</a>

                <!-- Desktop Sound Toggle -->
                <button id="sound-toggle" class="nav-link" title="Toggle Sound">
                    <img id="sound-icon" src="/images/volume.png" alt="Sound Icon" class="h-8 w-8" />
                </button>
            </div>
        </div>
    </nav>

    <!-- SIDEBAR (mobile only) -->
    <div x-show="open" x-transition
        class="fixed top-0 right-0 h-full w-64 bg-blue-950/80 text-white backdrop-blur-md shadow-2xl border-l border-blue-500/40 p-6 rounded-l-2xl z-50 md:hidden">
        <button @click="open = false" class="absolute top-4 right-4 text-xl">&times;</button>

        <a href="home.php" class="block py-2">Home</a>
        <a href="fish.php" class="block py-2">Fish</a>
        <a href="plants.php" class="block py-2">Coral Reefs</a>
        <a href="supplies.php" class="block py-2">Supplies</a>
        <a href="equipment.php" class="block py-2">Equipment</a>
        <a href="about.php" class="block py-2">About</a>
        <a href="contact.php" class="block py-2">Contact</a>
        <a href="cart.php" class="block py-2">Cart</a>
        <a href="#" class="block py-2">Login</a>
        <a href="#" class="block py-2">Register</a>
    </div>
</div>
