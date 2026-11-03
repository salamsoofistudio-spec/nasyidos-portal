# nasyidos.com

Siaran nasyid 24 jam. Laman statik [Astro](https://astro.build) yang memainkan video YouTube (pemain rasmi) mengikut jadual, dan semua pendengar nampak video yang sama pada masa yang sama.

## Cara ia berfungsi

- Setiap **rancangan** (`src/data/schedule.json`) bermula pada waktu tetap (Waktu Malaysia) dan memainkan senarai lagunya berulang-ulang.
- Lagu dan kedudukan semasa **dikira daripada jam** (`src/lib/schedule.ts`), jadi tiada pelayan penstriman. Hosting statik sudah cukup.
- Video dimainkan melalui IFrame Player YouTube (`youtube-nocookie.com`). Tiada audio dimuat turun atau distrim semula.
- Video yang tidak boleh dimainkan dilangkau secara automatik.

## Memulakan

```bash
npm install
npm run dev        # http://localhost:4321
```

Laman akan memaparkan "Siaran belum bermula" sehingga senarai lagu diisi (langkah seterusnya).

## Mengisi senarai lagu

1. Buat satu playlist YouTube **awam** untuk setiap rancangan, kemudian letak ID-nya (bahagian selepas `list=`) ke dalam `playlistId` dalam `src/data/schedule.json`.
2. Dapatkan kunci API: Google Cloud Console, aktifkan **YouTube Data API v3**, cipta **API key**. Kuota percuma lebih daripada cukup.
3. Jalankan:

   ```bash
   cp .env.example .env     # isi YT_API_KEY
   npm run library
   ```

   Skrip menulis `src/data/library.json` (tajuk, saluran, tempoh) dan **membuang** video yang tidak boleh di-embed, tidak awam, siaran langsung atau disekat di Malaysia. Senarai video yang dilangkau dipaparkan di terminal.
4. Bina semula dan muat naik (di bawah).

Ulang langkah 3 dan 4 setiap kali playlist berubah. Kunci API **tidak** terdedah dalam laman.

## Menerbitkan ke Hostinger (Web Hosting)

```bash
npm run build      # menghasilkan folder dist/
```

1. Dalam hPanel, buka **Websites → nasyidos.com → File Manager** (atau gunakan FTP).
2. Kosongkan `public_html` (kecuali fail yang anda perlukan), kemudian muat naik **kandungan** folder `dist/` ke dalam `public_html`. Pastikan `.htaccess` (fail tersembunyi) turut dimuat naik.
3. Aktifkan **SSL** (hPanel → Security → SSL) dan pastikan domain menghala ke hosting ini.
4. Buka `https://nasyidos.com` dan semak.

`public/.htaccess` memaksa HTTPS, memampatkan fail dan menetapkan cache. Jangan ketatkan `Referrer-Policy` kerana pemain YouTube memerlukan Referer.

## Struktur

```
src/
  data/schedule.json     rancangan + playlistId
  data/library.json      dijana oleh `npm run library`
  lib/schedule.ts        logik jam -> lagu (diuji)
  scripts/live-player.ts pemain YouTube + paparan langsung
  components/            Header, Hero, LivePlayer, Schedule, Artists, Cta, Footer
  pages/                 index, hak-cipta, 404
scripts/                 update-library.mjs, youtube-lib.mjs
tests/                   ujian logik jadual dan penapis YouTube
```

## Arahan

| Arahan | Fungsi |
| --- | --- |
| `npm run dev` | pelayan pembangunan |
| `npm run build` | bina ke `dist/` |
| `npm run check` | semak jenis |
| `npm test` | ujian unit |
| `npm run library` | kemas kini `library.json` daripada YouTube |

## Perkara yang perlu dilengkapkan

- `src/config.ts`: tukar `contactEmail` kepada e-mel sebenar.
- Gambar kongsi sosial (`og:image`) belum ditetapkan.
- Hak cipta: gunakan video daripada saluran rasmi artis/label dan keluarkan segera jika diminta (lihat `/hak-cipta/`).
