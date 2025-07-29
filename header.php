<!-- NAVBAR -->
<nav class="navbar">
    <div class="w-full flex justify-between items-center">
        <!-- real nav starts here -->
        <div class="flex items-center space-x-2 animate-logo">
            <img src="/images/oystergif.gif" alt="Fish Icon" class="h-11 w-11" />
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

            <!-- Sidebar -->
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
                <a href="#" class="block py-2">Login</a>
                <a href="#" class="block py-2">Register</a>
            </div>
        </div>

        <!-- sidebar nav ends here -->


        <div class="hidden md:flex items-center space-x-6" x-data="{ open: false }">
            <a href="home.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">Home</a>

            <!-- Dropdown Menu for Shop -->
            <div class="relative" @mouseenter="open = true" @mouseleave="open = false">
                <button class="nav-link">Shop</button>

                <!-- Dropdown Items -->
                <div x-show="open" x-transition class="dropdown-menu">
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


        <div class="hidden md:flex">
            <a href="cart.php" class="nav-link cart-icon" data-sound="/sounds/notification_pop.mp3">
                <img src="/images/cart.png" alt="Cart" class="h-8 w-8" />
            </a>

            <a href="#" class="nav-link" data-sound="/sounds/notification_pop.mp3">Login</a>
            <a href="#" class="nav-link" data-sound="/sounds/notification_pop.mp3">Register</a>

            <!-- Sound Toggle Button -->
            <button id="sound-toggle" class="nav-link" title="Toggle Sound">
                <img id="sound-icon" src="/images/volume.png" alt="Sound Icon" class="h-8 w-8" />
            </button>

        </div>
    </div>
</nav>