<script>
    const newsTabs = document.querySelectorAll("[data-news-tab]");
    const newsCardGridBerita = document.getElementById("newsCardGridBerita");
    const newsCardGridOpini = document.getElementById("newsCardGridOpini");
    const newsCardGrids = document.getElementById("newsCardGrids");

    let activeCategory = "berita";

    function setActiveTab(category) {
        newsTabs.forEach((tab) => {
            const isActive = tab.dataset.newsTab === category;
            tab.classList.toggle("active", isActive);
            tab.setAttribute("aria-pressed", isActive ? "true" : "false");
        });

        if (newsCardGridBerita && newsCardGridOpini) {
            const showBerita = category === "berita";
            newsCardGridBerita.classList.toggle("d-none", !showBerita);
            newsCardGridOpini.classList.toggle("d-none", showBerita);
        }

        if (newsCardGrids) {
            newsCardGrids.classList.add("is-switching");
            requestAnimationFrame(() => {
                newsCardGrids.classList.remove("is-switching");
            });
        }
    }

    newsTabs.forEach((tab) => {
        tab.addEventListener("click", (event) => {
            event.preventDefault();
            const selectedTab = tab.dataset.newsTab;

            if (selectedTab === activeCategory) {
                return;
            }

            activeCategory = selectedTab;
            setActiveTab(activeCategory);
        });
    });

    setActiveTab(activeCategory);

    AOS.init({
        once: true,
        offset: 100,
    });
</script>
