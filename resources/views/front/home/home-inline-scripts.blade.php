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

    const statsSection = document.getElementById("national-stats");
    const statNumbers = document.querySelectorAll(".stat-number[data-counter-target]");
    let hasCounterAnimated = false;

    function animateCounter(element, duration = 1500) {
        const target = Number(element.dataset.counterTarget || 0);
        const suffix = element.dataset.counterSuffix || "";
        const startTime = performance.now();

        function frame(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const current = Math.round(target * progress);
            element.textContent = String(current) + suffix;

            if (progress < 1) {
                requestAnimationFrame(frame);
            }
        }

        requestAnimationFrame(frame);
    }

    function runStatsCounters() {
        if (hasCounterAnimated || !statNumbers.length) {
            return;
        }

        hasCounterAnimated = true;
        statNumbers.forEach((counter, index) => {
            animateCounter(counter, 1400 + index * 150);
        });
    }

    if (statsSection && "IntersectionObserver" in window) {
        const statsObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                runStatsCounters();
                observer.unobserve(entry.target);
            });
        }, {
            threshold: 0.35
        });

        statsObserver.observe(statsSection);
    } else {
        runStatsCounters();
    }

    AOS.init({
        once: true,
        offset: 100,
    });
</script>
