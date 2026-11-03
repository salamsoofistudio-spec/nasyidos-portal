import { defineConfig } from 'astro/config';

// Laman statik sepenuhnya: hasil `npm run build` ialah folder `dist/`
// yang boleh dimuat naik terus ke `public_html` di Hostinger.
export default defineConfig({
  site: 'https://nasyidos.com',
  output: 'static',
  build: { format: 'directory' },
});
