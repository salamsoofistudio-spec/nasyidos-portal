// Fungsi tulen untuk memproses data YouTube Data API v3 (diuji dalam tests/youtube.test.ts).

/** "PT4M5S" -> 245, "P0D" (siaran langsung) -> 0 */
export function parseIsoDuration(iso) {
  const m = /^P(?:(\d+)D)?(?:T(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?)?$/.exec(iso ?? '');
  if (!m) return 0;
  const [, d, h, min, s] = m;
  return (Number(d) || 0) * 86400 + (Number(h) || 0) * 3600 + (Number(min) || 0) * 60 + (Number(s) || 0);
}

/**
 * Hanya video yang boleh di-embed, awam, bukan siaran langsung dan tidak disekat di rantau sasaran.
 * Pulangkan { ok: true } atau { ok: false, reason }.
 */
export function checkPlayable(video, region = 'MY') {
  if (!video) return { ok: false, reason: 'tidak dijumpai (dipadam atau peribadi)' };
  const status = video.status ?? {};
  if (status.privacyStatus !== 'public') return { ok: false, reason: `privasi: ${status.privacyStatus}` };
  if (status.embeddable !== true) return { ok: false, reason: 'embed dimatikan oleh pemilik' };
  if (status.uploadStatus && status.uploadStatus !== 'processed') return { ok: false, reason: 'belum siap diproses' };
  if (video.snippet?.liveBroadcastContent && video.snippet.liveBroadcastContent !== 'none') {
    return { ok: false, reason: 'siaran langsung' };
  }
  const rr = video.contentDetails?.regionRestriction;
  if (rr?.blocked?.includes(region)) return { ok: false, reason: `disekat di ${region}` };
  if (rr?.allowed && !rr.allowed.includes(region)) return { ok: false, reason: `tidak dibenarkan di ${region}` };
  if (parseIsoDuration(video.contentDetails?.duration) <= 0) return { ok: false, reason: 'tempoh tidak sah' };
  return { ok: true };
}

export function toTrack(video) {
  return {
    id: video.id,
    title: video.snippet?.title ?? '',
    channel: video.snippet?.channelTitle ?? '',
    channelId: video.snippet?.channelId ?? '',
    duration: parseIsoDuration(video.contentDetails?.duration),
  };
}

export function chunk(arr, size) {
  const out = [];
  for (let i = 0; i < arr.length; i += size) out.push(arr.slice(i, i + size));
  return out;
}
