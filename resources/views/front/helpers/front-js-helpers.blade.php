{{-- Utilitas JS front: tambahkan method ke window.FrontHelpers. Beberapa butuh pustaka (contoh: formatNewsDate butuh moment) — panggil setelah script dependensi di layout. --}}
<script>
    (function () {
        window.FrontHelpers = window.FrontHelpers || {};

        // Butuh: moment (ISO/Laravel, dll. → "07 July 2025")
        FrontHelpers.formatNewsDate = function (publishedAt) {
            if (publishedAt == null || publishedAt === "") {
                return "—";
            }
            const m = moment(publishedAt);
            return m.isValid() ? m.format("DD MMMM YYYY") : "—";
        };

        // Potong string untuk kartu ringkas; ellipsis hanya jika melebihi maxLength
        FrontHelpers.truncate = function (text, maxLength, ifNull = "") {
            if (text == null || text === "") {
                return ifNull;
            }
            const s = String(text);
            if (s.length <= maxLength) {
                return s;
            }
            return s.slice(0, maxLength) + "…";
        };
    })();
</script>
