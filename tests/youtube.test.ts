import { describe, expect, it } from 'vitest';
import { checkPlayable, chunk, parseIsoDuration, toTrack } from '../scripts/youtube-lib.mjs';

const video = (over: Record<string, unknown> = {}) => ({
  id: 'abc',
  snippet: { title: 'Tajuk', channelTitle: 'Saluran', channelId: 'UC1', liveBroadcastContent: 'none' },
  contentDetails: { duration: 'PT4M5S' },
  status: { privacyStatus: 'public', embeddable: true, uploadStatus: 'processed' },
  ...over,
});

describe('parseIsoDuration', () => {
  it('menukar ISO 8601 kepada saat', () => {
    expect(parseIsoDuration('PT4M5S')).toBe(245);
    expect(parseIsoDuration('PT1H2M3S')).toBe(3723);
    expect(parseIsoDuration('PT45S')).toBe(45);
    expect(parseIsoDuration('P1DT1S')).toBe(86401);
    expect(parseIsoDuration('P0D')).toBe(0);
    expect(parseIsoDuration('')).toBe(0);
    expect(parseIsoDuration(undefined)).toBe(0);
    expect(parseIsoDuration('rosak')).toBe(0);
  });
});

describe('checkPlayable', () => {
  it('menerima video yang sah', () => {
    expect(checkPlayable(video()).ok).toBe(true);
  });
  it('menolak video yang tiada', () => {
    expect(checkPlayable(undefined).ok).toBe(false);
  });
  it('menolak yang tidak boleh di-embed atau tidak awam', () => {
    expect(checkPlayable(video({ status: { privacyStatus: 'public', embeddable: false } })).ok).toBe(false);
    expect(checkPlayable(video({ status: { privacyStatus: 'unlisted', embeddable: true } })).ok).toBe(false);
  });
  it('menolak siaran langsung dan tempoh sifar', () => {
    expect(checkPlayable(video({ snippet: { liveBroadcastContent: 'live' } })).ok).toBe(false);
    expect(checkPlayable(video({ contentDetails: { duration: 'P0D' } })).ok).toBe(false);
  });
  it('menghormati sekatan rantau', () => {
    expect(checkPlayable(video({ contentDetails: { duration: 'PT1M', regionRestriction: { blocked: ['MY'] } } })).ok).toBe(false);
    expect(checkPlayable(video({ contentDetails: { duration: 'PT1M', regionRestriction: { allowed: ['US'] } } })).ok).toBe(false);
    expect(checkPlayable(video({ contentDetails: { duration: 'PT1M', regionRestriction: { blocked: ['US'] } } })).ok).toBe(true);
  });
});

describe('toTrack / chunk', () => {
  it('membina rekod lagu', () => {
    expect(toTrack(video())).toEqual({ id: 'abc', title: 'Tajuk', channel: 'Saluran', channelId: 'UC1', duration: 245 });
  });
  it('memecah senarai', () => {
    expect(chunk([1, 2, 3, 4, 5], 2)).toEqual([[1, 2], [3, 4], [5]]);
  });
});
