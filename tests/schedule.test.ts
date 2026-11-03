import { describe, expect, it } from 'vitest';
import { activeBlock, formatClock, parseStart, resolveNow, type Block, type Library } from '../src/lib/schedule';
import { collectArtists } from '../src/lib/artists';

const blocks: Block[] = [
  { id: 'subuh', name: 'Subuh', desc: '', start: '05:00' },
  { id: 'pagi', name: 'Pagi', desc: '', start: '08:00' },
  { id: 'malam', name: 'Malam', desc: '', start: '21:00' },
];

// 12:00:00 MYT pada 2026-03-10 = 04:00:00 UTC
const myt = (hh: number, mm = 0, ss = 0, day = 10) => Date.UTC(2026, 2, day, hh - 8, mm, ss);

const track = (id: string, duration: number, channel = 'Saluran A') => ({
  id,
  title: `Lagu ${id}`,
  channel,
  channelId: `ch-${channel}`,
  duration,
});

describe('parseStart', () => {
  it('menerima HH:MM', () => {
    expect(parseStart('05:00')).toBe(300);
    expect(parseStart('5:30')).toBe(330);
  });
  it('menolak format tidak sah', () => {
    expect(() => parseStart('25:00')).toThrow();
    expect(() => parseStart('abc')).toThrow();
  });
});

describe('activeBlock', () => {
  it('memilih rancangan mengikut Waktu Malaysia', () => {
    expect(activeBlock(blocks, myt(5, 0)).block.id).toBe('subuh');
    expect(activeBlock(blocks, myt(7, 59, 59)).block.id).toBe('subuh');
    expect(activeBlock(blocks, myt(8, 0)).block.id).toBe('pagi');
    expect(activeBlock(blocks, myt(20, 59)).block.id).toBe('pagi');
  });

  it('rancangan malam berterusan hingga selepas tengah malam', () => {
    const a = activeBlock(blocks, myt(2, 0, 0, 11));
    expect(a.block.id).toBe('malam');
    expect(a.startMs).toBe(myt(21, 0, 0, 10));
    expect(a.endMs).toBe(myt(5, 0, 0, 11));
    expect(a.nextBlock.id).toBe('subuh');
  });

  it('satu rancangan sahaja berjalan 24 jam', () => {
    const one = [{ id: 'x', name: 'X', desc: '', start: '06:00' }];
    const a = activeBlock(one, myt(12));
    expect(a.endMs - a.startMs).toBe(86_400_000);
  });

  it('tidak bergantung pada susunan data', () => {
    const shuffled = [blocks[2]!, blocks[0]!, blocks[1]!];
    expect(activeBlock(shuffled, myt(9)).block.id).toBe('pagi');
  });
});

describe('resolveNow', () => {
  const library: Library = {
    subuh: [track('a', 100), track('b', 200), track('c', 300)], // jumlah 600
    pagi: [track('d', 60)],
  };

  it('pulangkan null jika tiada lagu langsung', () => {
    expect(resolveNow(blocks, {}, myt(6))).toBeNull();
  });

  it('mengira lagu dan kedudukan dari permulaan rancangan', () => {
    const r = resolveNow(blocks, library, myt(5, 0, 0))!;
    expect(r.track.id).toBe('a');
    expect(r.offsetSec).toBeCloseTo(0);

    const r2 = resolveNow(blocks, library, myt(5, 0, 150))!; // 150s => lagu b, offset 50
    expect(r2.track.id).toBe('b');
    expect(r2.offsetSec).toBeCloseTo(50);
    expect(r2.remainingSec).toBeCloseTo(150);
  });

  it('senarai berulang apabila habis', () => {
    // 650s = satu pusingan (600s) + 50s => lagu a, offset 50
    const r = resolveNow(blocks, library, myt(5, 0, 0) + 650_000)!;
    expect(r.track.id).toBe('a');
    expect(r.offsetSec).toBeCloseTo(50);
  });

  it('semua pendengar mendapat jawapan sama pada masa sama', () => {
    const t = myt(6, 17, 42);
    expect(resolveNow(blocks, library, t)!.slotKey).toBe(resolveNow(blocks, library, t)!.slotKey);
  });

  it('slotKey berubah apabila lagu bertukar dan apabila pusingan baharu bermula', () => {
    const base = myt(5, 0, 0);
    const keys = [0, 99, 100, 299, 300, 599, 600].map((s) => resolveNow(blocks, library, base + s * 1000)!.slotKey);
    expect(keys[0]).toBe(keys[1]);
    expect(keys[1]).not.toBe(keys[2]);
    expect(keys[2]).toBe(keys[3]);
    expect(keys[3]).not.toBe(keys[4]);
    expect(keys[5]).not.toBe(keys[6]); // lagu a pusingan 2 != lagu a pusingan 1
    expect(keys[6]).not.toBe(keys[0]);
  });

  it('upNext hanya termasuk lagu yang sempat bermula sebelum rancangan tamat', () => {
    // Pagi: 08:00-21:00. Satu lagu 60s sahaja, jadi upNext ialah lagu sama berulang.
    const r = resolveNow(blocks, library, myt(8, 0, 10))!;
    expect(r.upNext.length).toBe(4);
    // 5 saat sebelum rancangan tamat, tiada lagu seterusnya yang sempat bermula
    const near = resolveNow(blocks, library, myt(20, 59, 59, 10))!;
    expect(near.upNext.length).toBe(0);
  });

  it('guna rancangan lain jika rancangan semasa tiada lagu', () => {
    const r = resolveNow(blocks, { subuh: library.subuh! }, myt(10))!;
    expect(r.usedFallback).toBe(true);
    expect(r.block.id).toBe('pagi');
    expect(['a', 'b', 'c']).toContain(r.track.id);
  });

  it('mengabaikan lagu dengan tempoh tidak sah', () => {
    const lib: Library = { subuh: [track('bad', 0), track('ok', 120)] };
    const r = resolveNow(blocks, lib, myt(5, 1))!;
    expect(r.track.id).toBe('ok');
  });

  it('jam yang sedikit sebelum permulaan tidak menghasilkan kedudukan negatif', () => {
    const a = activeBlock(blocks, myt(5, 0, 0));
    const r = resolveNow(blocks, library, a.startMs - 500)!;
    expect(r.offsetSec).toBeGreaterThanOrEqual(0);
  });
});

describe('formatClock', () => {
  it('memformat minit dan jam', () => {
    expect(formatClock(0)).toBe('0:00');
    expect(formatClock(83)).toBe('1:23');
    expect(formatClock(3725)).toBe('1:02:05');
    expect(formatClock(-5)).toBe('0:00');
  });
});

describe('collectArtists', () => {
  it('mengira lagu unik setiap saluran', () => {
    const lib: Library = {
      a: [track('1', 10, 'X'), track('2', 10, 'X'), track('3', 10, 'Y')],
      b: [track('1', 10, 'X')],
    };
    const artists = collectArtists(lib);
    expect(artists[0]).toMatchObject({ name: 'X', count: 2 });
    expect(artists[1]).toMatchObject({ name: 'Y', count: 1 });
  });
});
