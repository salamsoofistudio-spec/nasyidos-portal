/**
 * Logik "siaran langsung" tanpa pelayan.
 *
 * Setiap rancangan (blok) bermula pada waktu tetap (Waktu Malaysia, UTC+8) dan
 * memainkan senarai lagunya berulang-ulang. Kedudukan semasa dikira daripada jam,
 * jadi semua pendengar nampak video yang sama pada saat yang sama.
 */

export interface Track {
  id: string;
  title: string;
  channel: string;
  channelId?: string;
  /** tempoh dalam saat */
  duration: number;
}

export interface Block {
  id: string;
  name: string;
  desc: string;
  /** "HH:MM" mengikut Waktu Malaysia */
  start: string;
  /** ID playlist YouTube, digunakan oleh skrip `npm run library` */
  playlistId?: string;
}

export type Library = Record<string, Track[]>;

export const MYT_OFFSET_MS = 8 * 3600_000;
const DAY_MS = 86_400_000;

export function parseStart(start: string): number {
  const m = /^(\d{1,2}):(\d{2})$/.exec(start);
  if (!m) throw new Error(`Masa mula tidak sah: "${start}" (guna format HH:MM)`);
  const h = Number(m[1]);
  const min = Number(m[2]);
  if (h > 23 || min > 59) throw new Error(`Masa mula tidak sah: "${start}"`);
  return h * 60 + min;
}

export interface ActiveBlock {
  block: Block;
  startMs: number;
  endMs: number;
  nextBlock: Block;
}

/** Rancangan yang sedang bersiaran pada `nowMs`, termasuk yang bermula semalam. */
export function activeBlock(blocks: Block[], nowMs: number): ActiveBlock {
  if (blocks.length === 0) throw new Error('Jadual siaran kosong');
  const sorted = blocks
    .map((block) => ({ block, min: parseStart(block.start) }))
    .sort((a, b) => a.min - b.min);

  const myt = nowMs + MYT_OFFSET_MS;
  const dayStart = Math.floor(myt / DAY_MS) * DAY_MS;
  const minuteOfDay = (myt - dayStart) / 60_000;

  let i = -1;
  for (let k = 0; k < sorted.length; k++) if (sorted[k]!.min <= minuteOfDay) i = k;

  let startDay = dayStart;
  if (i === -1) {
    // sebelum rancangan pertama hari ini: rancangan terakhir semalam masih bersiaran
    i = sorted.length - 1;
    startDay = dayStart - DAY_MS;
  }

  const cur = sorted[i]!;
  const next = sorted[(i + 1) % sorted.length]!;
  const endDay = next.min <= cur.min ? startDay + DAY_MS : startDay;

  return {
    block: cur.block,
    startMs: startDay + cur.min * 60_000 - MYT_OFFSET_MS,
    endMs: endDay + next.min * 60_000 - MYT_OFFSET_MS,
    nextBlock: next.block,
  };
}

export interface Resolved {
  block: Block;
  nextBlock: Block;
  blockEndMs: number;
  tracks: Track[];
  index: number;
  track: Track;
  /** saat dari permulaan lagu semasa */
  offsetSec: number;
  remainingSec: number;
  /** berubah setiap kali lagu/rancangan bertukar */
  slotKey: string;
  upNext: Track[];
  usedFallback: boolean;
}

const playable = (tracks: Track[] | undefined): Track[] =>
  (tracks ?? []).filter((t) => t && t.id && Number.isFinite(t.duration) && t.duration > 0);

/**
 * Apa yang patut dimainkan sekarang. Pulangkan `null` jika tiada satu pun lagu.
 * Jika rancangan semasa tiada lagu, lagu rancangan pertama yang ada digunakan.
 */
export function resolveNow(
  blocks: Block[],
  library: Library,
  nowMs: number,
  upNextCount = 4,
): Resolved | null {
  const active = activeBlock(blocks, nowMs);

  let tracks = playable(library[active.block.id]);
  let usedFallback = false;
  if (tracks.length === 0) {
    for (const b of blocks) {
      const t = playable(library[b.id]);
      if (t.length > 0) {
        tracks = t;
        usedFallback = true;
        break;
      }
    }
  }
  if (tracks.length === 0) return null;

  const total = tracks.reduce((sum, t) => sum + t.duration, 0);
  const elapsed = Math.max(0, (nowMs - active.startMs) / 1000);
  const cycle = Math.floor(elapsed / total);
  const pos = elapsed - cycle * total;

  let acc = 0;
  let index = tracks.length - 1;
  for (let i = 0; i < tracks.length; i++) {
    if (pos < acc + tracks[i]!.duration) {
      index = i;
      break;
    }
    acc += tracks[i]!.duration;
  }

  const track = tracks[index]!;
  const offsetSec = pos - acc;
  const remainingSec = track.duration - offsetSec;

  // lagu seterusnya, hanya yang sempat bermula sebelum rancangan tamat
  const upNext: Track[] = [];
  let startsAt = nowMs + remainingSec * 1000;
  for (let k = 1; k <= upNextCount; k++) {
    if (startsAt >= active.endMs) break;
    const t = tracks[(index + k) % tracks.length]!;
    upNext.push(t);
    startsAt += t.duration * 1000;
  }

  return {
    block: active.block,
    nextBlock: active.nextBlock,
    blockEndMs: active.endMs,
    tracks,
    index,
    track,
    offsetSec,
    remainingSec,
    slotKey: `${active.block.id}|${active.startMs}|${cycle}|${index}`,
    upNext,
    usedFallback,
  };
}

/** 83 -> "1:23", 3725 -> "1:02:05" */
export function formatClock(totalSeconds: number): string {
  const s = Math.max(0, Math.floor(totalSeconds));
  const h = Math.floor(s / 3600);
  const m = Math.floor((s % 3600) / 60);
  const sec = String(s % 60).padStart(2, '0');
  return h > 0 ? `${h}:${String(m).padStart(2, '0')}:${sec}` : `${m}:${sec}`;
}
