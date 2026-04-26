<script>
    const newsData = {
        berita: [{
                image: "https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?auto=format&fit=crop&w=700&q=80",
                alt: "UI Design Trens",
                date: "10 July 2025",
                views: "2.4k",
                title: "UI Design Trends in 2025",
                excerpt: "Designing for 2025 isn't just about hopping on the latest trends - it's about creating experiences that truly matter to users."
            },
            {
                image: "https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=700&q=80",
                alt: "Branding UI UX",
                date: "10 July 2025",
                views: "1.8k",
                title: "Branding in UI/UX Design",
                excerpt: "Brand identity has to flow through every interface element to create consistent, memorable digital products."
            },
            {
                image: "https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=700&q=80",
                alt: "Teknologi AI",
                date: "10 July 2025",
                views: "3.1k",
                title: "AI Workflow for Product Teams",
                excerpt: "Practical AI adoption now focuses on augmenting design and development process, not replacing creativity."
            },
            {
                image: "https://images.unsplash.com/photo-1556761175-5973dc0f32b7?auto=format&fit=crop&w=700&q=80",
                alt: "Inovasi Teknologi",
                date: "10 July 2025",
                title: "Inovasi yang Mengubah Industri",
                excerpt: "Consistent innovation culture is built from small, repeatable product improvements backed by user feedback."
            },
            {
                image: "https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=700&q=80",
                alt: "Kolaborasi Tim",
                date: "09 July 2025",
                title: "Kolaborasi Lintas Tim Lebih Efektif",
                excerpt: "Cross-functional communication between product, design, and engineering accelerates delivery quality."
            },
            {
                image: "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=700&q=80",
                alt: "Startup Talk",
                date: "09 July 2025",
                title: "Startup Product Lessons 2025",
                excerpt: "Early-stage teams should prioritize clear problem statements before scaling features."
            },
            {
                image: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=700&q=80",
                alt: "Data Insight",
                date: "09 July 2025",
                title: "Data Insight for UX Decisions",
                excerpt: "Strong UX decisions combine quantitative analytics and qualitative user interviews."
            },
            {
                image: "https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=700&q=80",
                alt: "Program Mahasiswa",
                date: "09 July 2025",
                title: "Gerakan Mahasiswa Berbasis Program",
                excerpt: "Community-based initiatives become sustainable when paired with measurable impact targets."
            }
        ],
        opini: [{
                image: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=700&q=80",
                alt: "Opini Digital",
                date: "08 July 2025",
                title: "Opini: Masa Depan Ekosistem Digital",
                excerpt: "The next wave of digital growth should focus on inclusive access and meaningful user outcomes."
            },
            {
                image: "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&w=700&q=80",
                alt: "Kebijakan Publik",
                date: "08 July 2025",
                title: "Opini: Teknologi dan Kebijakan Publik",
                excerpt: "Public policy should evolve at the same speed as technology to maintain social relevance."
            },
            {
                image: "https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=700&q=80",
                alt: "Transformasi Pendidikan",
                date: "08 July 2025",
                title: "Opini: Transformasi Pendidikan Modern",
                excerpt: "Education needs adaptive learning systems that support diverse student pathways."
            },
            {
                image: "https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=700&q=80",
                alt: "Ekonomi Kreatif",
                date: "08 July 2025",
                title: "Opini: Ekonomi Kreatif dan Generasi Muda",
                excerpt: "Creative economy programs should prioritize mentorship and market-ready capabilities."
            },
            {
                image: "https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=700&q=80",
                alt: "Moderasi Beragama",
                date: "07 July 2025",
                title: "Opini: Moderasi di Era Sosial Media",
                excerpt: "A healthier digital discourse requires strong moderation values and empathy in communication."
            },
            {
                image: "https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?auto=format&fit=crop&w=700&q=80",
                alt: "Inovasi Sosial",
                date: "07 July 2025",
                title: "Opini: Inovasi Sosial Berbasis Komunitas",
                excerpt: "Grassroots innovation often creates faster social impact because it grows from real local needs."
            },
            {
                image: "https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=700&q=80",
                alt: "Kepemimpinan",
                date: "07 July 2025",
                title: "Opini: Kepemimpinan Mahasiswa Saat Ini",
                excerpt: "Student leadership thrives when collaboration and accountability are built into every initiative."
            },
            {
                image: "https://images.unsplash.com/photo-1515169067868-5387ec356754?auto=format&fit=crop&w=700&q=80",
                alt: "Budaya Kerja",
                date: "07 July 2025",
                title: "Opini: Budaya Kerja Adaptif",
                excerpt: "Adaptive working cultures encourage experimentation while keeping strategic focus intact."
            }
        ]
    };

    const newsTabs = document.querySelectorAll("[data-news-tab]");
    const newsCardGrid = document.getElementById("newsCardGrid");
    let activeCategory = "berita";

    function createNewsCard(card) {
        const views = card.views || "1.0k";
        return `
                <div class="col-12 col-sm-6 col-lg-3">
                    <article class="news-card">
                        <div class="news-img-wrapper">
                            <img src="${card.image}" alt="${card.alt}">
                        </div>
                        <div class="news-meta">
                            <p class="news-date mb-0">${card.date}</p>
                            <span class="news-views"><i class="bi bi-eye"></i> ${views}</span>
                        </div>
                        <h5>${card.title}</h5>
                        <p class="news-excerpt">${card.excerpt}</p>
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
