<script>
    const newsData = {
        berita: @json($news),
        opini: @json($opinions),
    };

    const newsTabs = document.querySelectorAll("[data-news-tab]");
    const newsCardGrid = document.getElementById("newsCardGrid");
    let activeCategory = "berita";

    const NEWS_TITLE_MAX = 35;
    const NEWS_SUBTITLE_MAX = 120;

    function createNewsCard(card) {
        const { truncate } = window.FrontHelpers;
        return `
                <div class="col-12 col-sm-6 col-lg-3">
                    <article class="news-card">
                        <div class="news-img-wrapper">
                            <img src="${card.cover_photo_path ?? 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=700&q=80'}" alt="${card.title}">
                        </div>
                        <div class="news-meta">
                            <p class="news-date mb-0">${window.FrontHelpers.formatNewsDate(card.published_at)}</p>
                            <span class="news-views"><i class="bi bi-eye"></i> ${card.views_count}</span>
                        </div>
                        <h5>${truncate(card.title, NEWS_TITLE_MAX, '-')}</h5>
                        <p class="news-excerpt">${truncate(card.subtitle, NEWS_SUBTITLE_MAX, '-')}</p>
                    </article>
                </div>
            `;
    }

    function renderNewsCards(category) {
        const cards = newsData[category] || [];
        newsCardGrid.classList.add("is-switching");
        newsCardGrid.innerHTML = cards.map(createNewsCard).join("");
        requestAnimationFrame(() => {
            newsCardGrid.classList.remove("is-switching");
        });
    }

    function setActiveTab(category) {
        newsTabs.forEach((tab) => {
            const isActive = tab.dataset.newsTab === category;
            tab.classList.toggle("active", isActive);
            tab.setAttribute("aria-pressed", isActive ? "true" : "false");
        });
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
            renderNewsCards(activeCategory);
        });
    });

    setActiveTab(activeCategory);
    renderNewsCards(activeCategory);

    AOS.init({
        once: true, // Animasi hanya berjalan sekali saat di-scroll
        offset: 100, // Jarak trigger animasi (px)
    });
</script>
