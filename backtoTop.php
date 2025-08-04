<!-- back to top button -->
<button id="backToTop" title="Back to top" style="display:none;">
    ➤
</button>
<script src="/js/backtoTop.js"></script>

<style>
    /* back to top button */

    #backToTop {
        position: fixed;
        bottom: 30px;
        right: 30px;
        padding: 10px 15px;
        font-size: 24px;
        background-color: #447ab300;
        color: white;
        border: 1px solid;
        border-radius: 50%;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.3s ease;
        z-index: 1000;
        rotate: -90deg;
    }

    #backToTop:hover {
        opacity: 1;
        background-color: #ffffff67;
    }
</style>