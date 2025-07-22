const links = document.querySelectorAll('.nav-link');
    const current = window.location.pathname.split("/").pop();

    links.forEach(link => {
      if (link.getAttribute("href") === current) {
        link.classList.add("border-b-2", "border-red-500");
      }
    });