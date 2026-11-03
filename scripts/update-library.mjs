#!/usr/bin/env node
/**
 * Tarik senarai video + tempoh daripada playlist YouTube bagi setiap rancangan
 * dalam src/data/schedule.json, lalu tulis src/data/library.json.
 *
 *   YT_API_KEY=xxxx npm run library
 *
 * Rancangan tanpa `playlistId` dibiarkan seperti sedia ada dalam library.json.
 */
import { readFile, writeFile } from 'node:fs/promises';
import { existsSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { chunk, checkPlayable, toTrack } from './youtube-lib.mjs';

const root = fileURLToPath(new URL('..', import.meta.url));
const schedulePath = `${root}src/data/schedule.json`;
const libraryPath = `${root}src/data/library.json`;

async function loadDotEnv() {
  const p = `${root}.env`;
  if (!existsSync(p)) return;
  for (const line of (await readFile(p, 'utf8')).split(/\r?\n/)) {
    const m = /^\s*([A-Z0-9_]+)\s*=\s*(.*?)\s*$/.exec(line);
    if (m && !(m[1] in process.env)) process.env[m[1]] = m[2].replace(/^["']|["']$/g, '');
  }
}

async function api(path, params, key) {
  const url = new URL(`https://www.googleapis.com/youtube/v3/${path}`);
  for (const [k, v] of Object.entries({ ...params, key })) if (v !== undefined) url.searchParams.set(k, v);
  const res = await fetch(url);
  if (!res.ok) {
    const body = await res.text();
    throw new Error(`YouTube API ${path} gagal (${res.status}): ${body.slice(0, 300)}`);
  }
  return res.json();
}

async function playlistVideoIds(playlistId, key) {
  const ids = [];
  let pageToken;
  do {
    const data = await api('playlistItems', { part: 'contentDetails', maxResults: '50', playlistId, pageToken }, key);
    for (const item of data.items ?? []) if (item.contentDetails?.videoId) ids.push(item.contentDetails.videoId);
    pageToken = data.nextPageToken;
  } while (pageToken);
  return [...new Set(ids)];
}

async function main() {
  await loadDotEnv();
  const key = process.env.YT_API_KEY;
  if (!key) {
    console.error('YT_API_KEY tiada. Jalankan:  YT_API_KEY=kunci_anda npm run library  (atau letak dalam fail .env)');
    process.exit(1);
  }

  const blocks = JSON.parse(await readFile(schedulePath, 'utf8'));
  const library = existsSync(libraryPath) ? JSON.parse(await readFile(libraryPath, 'utf8')) : {};
  const region = process.env.TARGET_REGION || 'MY';

  for (const block of blocks) {
    if (!block.playlistId) {
      console.log(`- ${block.name}: tiada playlistId, dilangkau`);
      continue;
    }
    const ids = await playlistVideoIds(block.playlistId, key);
    const tracks = [];
    const skipped = [];
    const found = new Map();
    for (const group of chunk(ids, 50)) {
      const data = await api('videos', { part: 'snippet,contentDetails,status', id: group.join(',') }, key);
      for (const v of data.items ?? []) found.set(v.id, v);
    }
    for (const id of ids) {
      const video = found.get(id);
      const verdict = checkPlayable(video, region);
      if (verdict.ok) tracks.push(toTrack(video));
      else skipped.push(`${id} (${verdict.reason})`);
    }
    library[block.id] = tracks;
    const mins = Math.round(tracks.reduce((s, t) => s + t.duration, 0) / 60);
    console.log(`- ${block.name}: ${tracks.length} lagu, ~${mins} minit`);
    for (const s of skipped) console.log(`    dilangkau: ${s}`);
    if (mins > 0 && mins < 30) console.warn(`    amaran: senarai pendek, lagu akan berulang kerap`);
  }

  const total = blocks.reduce((sum, b) => sum + (library[b.id]?.length ?? 0), 0);
  if (process.env.REQUIRE_TRACKS === '1' && total === 0) {
    console.error(
      '\nTiada satu pun lagu ditemui. Semak `playlistId` dalam src/data/schedule.json dan pastikan playlist itu awam ' +
        'serta mengandungi video yang boleh di-embed.',
    );
    process.exit(1);
  }

  await writeFile(libraryPath, JSON.stringify(library, null, 2) + '\n');
  console.log(`\nSiap: ${libraryPath}`);
}

main().catch((err) => {
  console.error(err.message);
  process.exit(1);
});
