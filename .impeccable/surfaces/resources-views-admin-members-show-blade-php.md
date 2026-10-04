---
version: 1
slug: "resources-views-admin-members-show-blade-php"
primary_target: "resources/views/admin/members/show.blade.php"
related_targets: []
---

# Admin — detail anggota

Scope: `admin/members/{member}` (admin.members.show). Mode: Operate. Primary user: operator input data. Tasks: verify identity data, handle the KTA, contact the member, audit missing data and documents. Visual world: incumbent Sneat admin (Bootstrap 5), unchanged.

## Direction contract

THESIS: The member's KTA is the page's lead object, shown as the real card, not a badge; the category-default "avatar card + label: value list" is refused.

OWN-WORLD: Sneat as shipped: white cards on #f5f5f9, primary #8B1515 (PMMBN maroon) for actions only, bg-label badges for state, boxicons. The KTA sits on a body-bg stage inside the top card; data in three sectioned columns with muted labels, "Belum diisi" for empty values.

STORY: The operator sees at once whether the member is verified and holds a KTA, who they are, how to reach them, and what is missing, then opens the card, edits, or contacts.

FIRST VIEWPORT: Page header (title + Ubah / Kembali). One card: left 5/12 the scaled live KTA preview with number and open/print actions (or a teaching empty state); right 7/12 name, NIM, status badges, contact actions (email, telepon, WhatsApp), missing-data notice linking to Ubah. Below: data card in three columns, then documents with type, size, image thumbnails.

FORM: "KTA di depan", position 4 of 7 on my ordered list; seed key 1389f68a.

FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance
