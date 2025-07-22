<!-- NAVBAR -->
<nav class="navbar">
    <div class="flex items-center space-x-2 animate-logo">
        <img src="/images/oystergif.gif" alt="Fish Icon" class="h-11 w-11" />
        <span class="h-8 border-l-2 border-blue-500 mx-3"></span>
        <a href="#" class="logo-font animate-slide-in-left" data-sound="/sounds/notification_pop.mp3">WaterPets</a>
    </div>


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

        <a href="about.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">About</a>
        <a href="contact.php" class="nav-link" data-sound="/sounds/notification_pop.mp3">Contact</a>
    </div>


    <div class="hidden md:flex">
        <a href="#" class="nav-link" data-sound="/sounds/notification_pop.mp3">Login</a>
        <a href="#" class="nav-link" data-sound="/sounds/notification_pop.mp3">Register</a>

        <!-- Sound Toggle Button -->
        <button id="sound-toggle" class="nav-link" title="Toggle Sound">
            <img id="sound-icon" src="/images/volume.png" alt="Sound Icon" class="h-7 w-7" />
        </button>

    </div>
</nav>