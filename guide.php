<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Guide</title>
    <link rel="stylesheet" href="/css/guide_style.css">
    <!-- Tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

    <section class="guide-section">
        <?php include 'header.php'; ?>
        <hr class="header-divider">

        <div class="care-guide-landing">
            <!-- Left side: Text -->
            <div class="landing-text">
                <h1>Discover Our Care Guides</h1>
                <p class="text-big">Expert Tips & Insights for Your Water Pet's Well-being</p>
                <p class="text-small">Fish Care Tips, Coral Maintenance Guide and Tank Setup</p>
                <div class="cta-buttons">
                    <a href="#guides" class="btn">Explore Guides</a>

                </div>
            </div>

            <!-- Right side: Image -->
            <div class="landing-image">
                <img src="/images/green-radient-rosetail-betta.png" alt="Fish Care Guide">
            </div>
        </div>


    </section>

    <section>
        <!-- carousel -->
        <div class="carousel">
            <!-- list item -->
            <div class="list">
                <div class="item">
                    <img src="/images/img6.jpg">
                    <div class="content">
                        <div class="author">LUNDEV</div>
                        <div class="title">DESIGN SLIDER</div>
                        <div class="topic">ANIMAL</div>
                        <div class="des">
                            <!-- lorem 50 -->
                            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ut sequi, rem magnam nesciunt minima placeat, itaque eum neque officiis unde, eaque optio ratione aliquid assumenda facere ab et quasi ducimus aut doloribus non numquam. Explicabo, laboriosam nisi reprehenderit tempora at laborum natus unde. Ut, exercitationem eum aperiam illo illum laudantium?
                        </div>
                        <div class="buttons">
                            <button>SEE MORE</button>
                            <button>SUBSCRIBE</button>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <img src="images/img5.jpg">
                    <div class="content">
                        <div class="author">LUNDEV</div>
                        <div class="title">DESIGN SLIDER</div>
                        <div class="topic">ANIMAL</div>
                        <div class="des">
                            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ut sequi, rem magnam nesciunt minima placeat, itaque eum neque officiis unde, eaque optio ratione aliquid assumenda facere ab et quasi ducimus aut doloribus non numquam. Explicabo, laboriosam nisi reprehenderit tempora at laborum natus unde. Ut, exercitationem eum aperiam illo illum laudantium?
                        </div>
                        <div class="buttons">
                            <button>SEE MORE</button>
                            <button>SUBSCRIBE</button>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <img src="images/img7.jpg">
                    <div class="content">
                        <div class="author">LUNDEV</div>
                        <div class="title">DESIGN SLIDER</div>
                        <div class="topic">ANIMAL</div>
                        <div class="des">
                            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ut sequi, rem magnam nesciunt minima placeat, itaque eum neque officiis unde, eaque optio ratione aliquid assumenda facere ab et quasi ducimus aut doloribus non numquam. Explicabo, laboriosam nisi reprehenderit tempora at laborum natus unde. Ut, exercitationem eum aperiam illo illum laudantium?
                        </div>
                        <div class="buttons">
                            <button>SEE MORE</button>
                            <button>SUBSCRIBE</button>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <img src="images/img8.jpg">
                    <div class="content">
                        <div class="author">LUNDEV</div>
                        <div class="title">DESIGN SLIDER</div>
                        <div class="topic">ANIMAL</div>
                        <div class="des">
                            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ut sequi, rem magnam nesciunt minima placeat, itaque eum neque officiis unde, eaque optio ratione aliquid assumenda facere ab et quasi ducimus aut doloribus non numquam. Explicabo, laboriosam nisi reprehenderit tempora at laborum natus unde. Ut, exercitationem eum aperiam illo illum laudantium?
                        </div>
                        <div class="buttons">
                            <button>SEE MORE</button>
                            <button>SUBSCRIBE</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- list thumnail -->
            <div class="thumbnail">
                <div class="item">
                    <img src="/images/img6.jpg">
                    <div class="content">
                        <div class="title">
                            Name Slider
                        </div>
                        <div class="description">
                            Description
                        </div>
                    </div>
                </div>
                <div class="item">
                    <img src="images/img5.jpg">
                    <div class="content">
                        <div class="title">
                            Name Slider
                        </div>
                        <div class="description">
                            Description
                        </div>
                    </div>
                </div>
                <div class="item">
                    <img src="images/img7.jpg">
                    <div class="content">
                        <div class="title">
                            Name Slider
                        </div>
                        <div class="description">
                            Description
                        </div>
                    </div>
                </div>
                <div class="item">
                    <img src="images/img8.jpg">
                    <div class="content">
                        <div class="title">
                            Name Slider
                        </div>
                        <div class="description">
                            Description
                        </div>
                    </div>
                </div>
            </div>
            <!-- next prev -->

            <div class="arrows">
                <button id="prev">
                    < </button>
                        <button id="next">></button>
            </div>
            <!-- time running -->
            <div class="time"></div>
        </div>

    </section>

    <section class="resource-section">
        <div class="resources-container">
            <div class="resources-header">
                <h2>Helpful Resources</h2>
                <p>Explore curated articles, tutorials, and videos to better care for your aquatic pets.</p>
            </div>

            <div class="resources-grid">
                <!-- Resource Card 1 -->
                <div class="resource-card">
                    <h3>Caring for Freshwater Fish</h3>
                    <p>Learn the essentials of keeping your freshwater fish healthy and happy.</p>
                    <a href="https://www.aquariumcoop.com/blogs/aquarium/fish-care-tips" target="_blank">Read Article</a>
                </div>

                <!-- Resource Card 2 -->
                <div class="resource-card">
                    <h3>Setting Up a Saltwater Tank</h3>
                    <p>Step-by-step guide for beginners to set up a thriving saltwater aquarium.</p>
                    <a href="https://youtu.be/H82orWWNs0A?si=h9HiyT9rzcwI-ro_" target="_blank">Watch Video</a>
                </div>

                <!-- Resource Card 3 -->
                <div class="resource-card">
                    <h3>Aquarium Plants Care</h3>
                    <p>Tips and tricks to maintain beautiful and healthy aquatic plants.</p>
                    <a href="https://youtu.be/PtXmn-ZowzE?si=vq-5h0OrIzSwjJwQ" target="_blank">Watch Video</a>
                </div>

                <!-- Resource Card 4 -->
                <div class="resource-card">
                    <h3>Fish Nutrition Guide</h3>
                    <p>Understand the best diet and feeding schedule for your aquarium fish.</p>
                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/KPlVF1gpnrk?si=NxsQdt2u5RSN_Y18" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>

                <!-- Resource Card 5: Embedded Video -->
                <div class="resource-card">
                    <h3>Beginner Aquarium Setup</h3>
                    <p>Easy tips on setting up a tank.</p>
                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/vOubuXdLIjs?si=mN8TWblmvhk4HnDw" title="Beginner Aquarium Setup" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>

                <!-- Resource Card 6: Embedded Video -->
                <div class="resource-card">
                    <h3>Top 5 Aquarium Fish for Beginners</h3>
                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/og9aN9_jJ-o?si=C_PPsafGYtyADve2" title="Top 5 Aquarium Fish for Beginners" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    <?php include 'chatbot.php'; ?>
    <?php include 'backToTop.php'; ?>

    <script src="js/guideSlider.js"></script>
</body>

</html>