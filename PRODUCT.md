# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users
Primary: operator input data — staff who enter, tidy, and check member records in the admin panel day to day. Public visitors (prospective members) use the public site and never log in.

## Product Purpose
Sistem Informasi PMMBN is the membership and content system of PMMBN, a student movement organisation that started from the Duta Moderasi Beragama movement in Surabaya. It takes public member self-registrations (email-OTP verified), lets admins review them into verified members, issues each verified member a KTA (Kartu Tanda Anggota) with a QR code, and runs the public site's articles, documents, and programs.

## Operating Context
- Admin work: reviewing pending member activations, accepting or rejecting them, correcting member data, checking supporting documents, and opening or printing KTA cards.
- On a member record, an operator checks identity data (NIM, name, birth, gender, college, regional leader, village/address), handles the KTA, finds contact details, and audits which data and documents are missing.
- Access is permission-based per resource (view / create / update / delete); an Administrator role gets everything.

## Capabilities and Constraints
- Laravel 13 + plain Blade; admin panel uses the Sneat (Bootstrap 5) template as its established UI. Select2 for selects.
- KTA numbers are either auto-generated (yearly sequence, locked) or manual for "anggota khusus" (special members, editable).
- Supporting documents: up to 20 files per member (PDF, Office, images, ZIP, text).
- UI copy is Indonesian.

## Evidence on Hand
- Logo assets: `pmmbn.png`, `pmmbn.ico`.
- No member photos are collected; do not invent avatars or portraits of members.

## Product Principles
- Operators should see a record's state (verified, special, incomplete) without reading every field.
- Consistency with the rest of the admin panel beats novelty on any single page.
- Never fabricate member data; empty fields stay visibly empty.
