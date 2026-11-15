# Fumiko POS - Design System & Visual Direction

## Brand Identity
- **Brand Name**: Fumiko Styrofoam (富美子 FUMIKO Styrofoam)
- **Tagline**: Designed for life, Built to Last
- **Product Category**: Industrial & Commercial B2B POS, Inventory, Warehouse Stock Opname, Invoicing (A5), and Cash/Receivable Management for Styrofoam (EPS) manufacturing and distribution.

## Design Read (Anti-Slop Dials)
- **ENERGY**: `2` (Crisp, authoritative industrial-commercial B2B interface with warm gold accents against deep slate/obsidian and clean light operational surfaces)
- **RHYTHM**: `2` (Structured operational density: clear tabular data, scannable A5 print documents, and focused form sections)
- **MOTION**: `1` (Functional, fast feedback on interactions; zero decorative loops)

## Color Palette & Contrast Tokens
- **Primary Brand Accent**: Warm Gold / Amber (`#b45309` on light backgrounds for >= 4.5:1 text contrast; `#f59e0b` / `#fbbf24` on dark `#0f172a` backgrounds for >= 7:1 contrast)
- **Dark Surface (Login Hero & Brand Header)**: Deep Obsidian Slate (`#0b0f19` to `#111827`) with subtle warm gold border (`rgba(245, 158, 11, 0.28)`)
- **Light Surface (Workspace & A5 Documents)**: Crisp Alabaster (`#f8fafc`) and Pure White (`#ffffff`) cards with Slate (`#0f172a`) primary text and Muted Slate (`#475569`) secondary text (contrast ratio 7.5:1 on white)
- **Semantic Status Tokens**:
  - `Lunas` / `Approved` (Paid / Settled): Emerald (`#047857` text on `#ecfdf5` badge)
  - `Belum Lunas` / `Pending` (Unpaid / Outstanding): Amber (`#b45309` text on `#fffbeb` badge)
  - `Jatuh Tempo` / `Overdue` / `Canceled`: Rose (`#be123c` text on `#fff1f2` badge)

## Typography & Print Standards
- **UI Sans**: Plus Jakarta Sans / Inter, with `font-variant-numeric: tabular-nums` on all currency, stock quantity, and dimension (`ukuran`) columns.
- **A5 Document Print Standard**:
  - `@page { size: A5 landscape; margin: 8mm; }` (with portrait/landscape flexibility)
  - Compact two-column header (Brand Logo + Company Info on left, Document Title + Nomor + Tanggal + Customer/Toko + No HP + Alamat on right)
  - High-legibility bordered item table including `Nama Barang`, `Ukuran`, `Qty`, `Satuan`, `Harga Satuan`, `Diskon`, and `Subtotal`
  - Signature blocks at the bottom (`Penerima / Pembeli`, `Sopir / Pengirim`, `Hormat Kami - Fumiko Styrofoam`).
