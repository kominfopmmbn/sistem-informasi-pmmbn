@extends('front.layouts.app', ['bodyClass' => 'page-news'])

@section('title', 'Berita')

@section('content')


    <div class="container">
        <div class="news-hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="breadcrumb-custom">Artikel <i class="fa-solid fa-chevron-right fa-xs mx-2"></i> <span
                        id="heroCrumbLabel">Berita</span></div>
                <h1 class="display-5 fw-bold mb-3" id="heroTitle">Berita</h1>
                <p class="lead" id="heroLead" style="max-width: 600px; font-size: 1.05rem;">Menyajikan berita, pembaruan,
                    dan perkembangan terbaru seputar dunia digital, desain, dan teknologi yang relevan untuk dibaca hari
                    ini.</p>
            </div>
        </div>
    </div>

    <div class="subnav-wrapper mt-4">
        <div class="container" role="tablist" aria-label="Kategori artikel">
            <a href="#" class="subnav-link" data-news-tab="opini" role="tab" aria-selected="false"
                aria-controls="articleCardGrid" id="tab-opini">Opini</a>
            <a href="#" class="subnav-link active" data-news-tab="berita" role="tab" aria-selected="true"
                aria-controls="articleCardGrid" id="tab-berita">Berita</a>
        </div>
    </div>

    <div class="container my-5">

        <div class="row justify-content-center mb-5">
            <div class="col-md-8 d-flex gap-3 justify-content-center flex-wrap">
                <div class="search-wrapper" style="width: 250px;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="form-control filter-control" placeholder="Search">
                </div>
                <select class="form-select filter-control" style="width: 150px;">
                    <option selected>Bulan</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                </select>
                <select class="form-select filter-control" style="width: 150px;">
                    <option selected>Tahun</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                </select>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="articleCardGrid" role="tabpanel"
            aria-labelledby="tab-berita"></div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-5 pt-3">
            <div class="text-muted mb-3 mb-md-0" id="articleResultsSummary" style="font-size: 0.9rem;">
                Menampilkan 1 - 8 dari 8 hasil
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-custom mb-0">
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link border-0 text-muted" href="#">...</a></li>
                    <li class="page-item"><a class="page-link" href="#">8</a></li>
                    <li class="page-item"><a class="page-link" href="#"><i
                                class="fa-solid fa-chevron-right fa-xs"></i></a></li>
                </ul>
            </nav>
        </div>

    </div>
@endsection
@push('scripts')
<script>
    const newsData = {
        berita: [
            {
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
        opini: [
            {
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

    const categoryHero = {
        berita: {
            crumb: "Berita",
            title: "Berita",
            lead: "Menyajikan berita, pembaruan, dan perkembangan terbaru seputar dunia digital, desain, dan teknologi yang relevan untuk dibaca hari ini.",
            docTitle: "Berita - PMMBN"
        },
        opini: {
            crumb: "Opini",
            title: "Opini",
            lead: "Kolom pandangan dan analisis reflektif tentang isu moderasi beragama, kebangsaan, serta peran mahasiswa dalam pembangunan bermartabat.",
            docTitle: "Opini - PMMBN"
        }
    };

    const newsTabs = document.querySelectorAll("[data-news-tab]");
    const articleCardGrid = document.getElementById("articleCardGrid");
    const heroCrumbLabel = document.getElementById("heroCrumbLabel");
    const heroTitle = document.getElementById("heroTitle");
    const heroLead = document.getElementById("heroLead");
    const articleResultsSummary = document.getElementById("articleResultsSummary");

    let activeCategory = "berita";

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/"/g, "&quot;");
    }

    function createArticleCard(card) {
        return `
        <div class="col">
            <a href="{{ route('article.show', 'slug-contoh') }}" class="card article-card text-decoration-none">
                <img src="${card.image}" class="card-img-top" alt="${escapeHtml(card.alt)}">
                <div class="card-body p-4">
                    <div class="card-date">${escapeHtml(card.date)}</div>
                    <h5 class="card-title">${escapeHtml(card.title)}</h5>
                    <p class="card-text">${escapeHtml(card.excerpt)}</p>
                </div>
            </a>
        </div>`;
    }

    function renderArticleCards(category) {
        const cards = newsData[category] || [];
        articleCardGrid.innerHTML = cards.map(createArticleCard).join("");
    }

    function updateHero(category) {
        const meta = categoryHero[category];
        if (!meta) return;
        heroCrumbLabel.textContent = meta.crumb;
        heroTitle.textContent = meta.title;
        heroLead.textContent = meta.lead;
        document.title = meta.docTitle;
    }

    function updateResultsSummary(category) {
        const n = (newsData[category] || []).length;
        if (n === 0) {
            articleResultsSummary.textContent = "Tidak ada hasil";
            return;
        }
        articleResultsSummary.textContent = `Menampilkan 1 – ${n} dari ${n} hasil`;
    }

    function setActiveSubnav(category) {
        newsTabs.forEach((tab) => {
            const isActive = tab.dataset.newsTab === category;
            tab.classList.toggle("active", isActive);
            tab.setAttribute("aria-selected", isActive ? "true" : "false");
        });
        const tabId = category === "opini" ? "tab-opini" : "tab-berita";
        articleCardGrid.setAttribute("aria-labelledby", tabId);
    }

    function applyCategory(category) {
        activeCategory = category;
        setActiveSubnav(category);
        updateHero(category);
        renderArticleCards(category);
        updateResultsSummary(category);
    }

    newsTabs.forEach((tab) => {
        tab.addEventListener("click", (event) => {
            event.preventDefault();
            const selected = tab.dataset.newsTab;
            if (!selected || selected === activeCategory) {
                return;
            }
            applyCategory(selected);
        });
    });

    applyCategory(activeCategory);
</script>
@endpush
