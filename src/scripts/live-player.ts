/**
 * Pemain siaran langsung: semua pendengar dikira daripada jam yang sama (lihat lib/schedule.ts),
 * jadi tiada pelayan penstriman diperlukan. Video dimainkan melalui pemain rasmi YouTube.
 */
import { formatClock, resolveNow, type Block, type Library, type Resolved, type Track } from '../lib/schedule';

declare global {
  interface Window {
    YT?: any;
    onYouTubeIframeAPIReady?: () => void;
  }
}

interface LiveData {
  blocks: Block[];
  library: Library;
}

const YT_PLAYING = 1;
const YT_PAUSED = 2;
const YT_ENDED = 0;

const byId = <T extends HTMLElement = HTMLElement>(id: string) => document.getElementById(id) as T | null;

function main() {
  const root = byId('lp-root');
  const dataEl = byId('live-data');
  const el = {
    stage: byId('lp-stage'),
    start: byId<HTMLButtonElement>('lp-start'),
    empty: byId('lp-empty'),
    status: byId('lp-status'),
    title: byId('lp-title'),
    channel: byId('lp-channel'),
    progress: byId('lp-progress'),
    bar: byId('lp-progress-bar'),
    elapsed: byId('lp-elapsed'),
    total: byId('lp-total'),
    toggle: byId<HTMLButtonElement>('lp-toggle'),
    share: byId<HTMLButtonElement>('lp-share'),
    fs: byId<HTMLButtonElement>('lp-fs'),
    volume: byId<HTMLInputElement>('lp-volume'),
    queue: byId('lp-queue'),
    note: byId('lp-block-note'),
    cover: byId('lp-cover'),
  };
  if (!root || !dataEl || !el.title || !el.channel || !el.queue) return;

  let data: LiveData;
  try {
    data = JSON.parse(dataEl.textContent || '{}');
  } catch {
    return;
  }
  const blocks = data.blocks ?? [];
  const library = data.library ?? {};

  // ---------- keadaan ----------
  let clockOffsetMs = 0; // betulkan jam peranti yang tersasar jauh
  let player: any = null;
  let ready = false;
  let wantPlay = false; // pendengar mahu mendengar
  let playingSlot: string | null = null; // slot (lagu) yang dimuatkan mengikut jam
  let desync: { slot: string; idx: number; track: Track } | null = null; // lagu dilangkau kerana ralat
  let lastLoadAt = 0;
  let errorStreak = 0;
  let driftTicks = 0;
  let latest: Resolved | null = null;
  let renderedSlot: string | null = null;
  let shownTrackId: string | null = null;
  let statusTimer: number | undefined;

  const now = () => Date.now() + clockOffsetMs;
  const resolve = () => resolveNow(blocks, library, now());

  function say(message: string, ms = 7000) {
    if (!el.status) return;
    el.status.textContent = message;
    window.clearTimeout(statusTimer);
    if (ms > 0) statusTimer = window.setTimeout(() => (el.status!.textContent = ''), ms);
  }

  function setPlayingUI(playing: boolean) {
    el.toggle?.setAttribute('data-state', playing ? 'playing' : 'paused');
    el.toggle?.setAttribute('aria-label', playing ? 'Jeda' : 'Main (sertai siaran semasa)');
  }

  // ---------- jam ----------
  async function syncClock() {
    try {
      const t0 = Date.now();
      const res = await fetch(location.pathname, { method: 'HEAD', cache: 'no-store' });
      const t1 = Date.now();
      const header = res.headers.get('date');
      const server = header ? Date.parse(header) : NaN;
      if (!Number.isNaN(server)) {
        const offset = server - (t0 + t1) / 2;
        // Header Date hanya tepat sesaat; guna hanya jika jam peranti jelas tersasar.
        if (Math.abs(offset) > 3000) clockOffsetMs = offset;
      }
    } catch {
      /* tiada rangkaian: guna jam peranti */
    }
  }

  // ---------- pemain YouTube ----------
  let apiPromise: Promise<void> | null = null;
  function loadApi(): Promise<void> {
    if (window.YT?.Player) return Promise.resolve();
    if (apiPromise) return apiPromise;
    apiPromise = new Promise<void>((resolve, reject) => {
      const previous = window.onYouTubeIframeAPIReady;
      window.onYouTubeIframeAPIReady = () => {
        previous?.();
        resolve();
      };
      const s = document.createElement('script');
      s.src = 'https://www.youtube.com/iframe_api';
      s.async = true;
      s.onerror = () => {
        apiPromise = null;
        reject(new Error('Gagal memuatkan pemain YouTube'));
      };
      document.head.appendChild(s);
    });
    return apiPromise;
  }

  function createPlayer(videoId: string) {
    if (player || !window.YT?.Player) return;
    player = new window.YT.Player('lp-yt', {
      host: 'https://www.youtube-nocookie.com',
      width: '100%',
      height: '100%',
      videoId,
      playerVars: {
        autoplay: 0,
        controls: 0,
        disablekb: 1,
        fs: 0,
        rel: 0,
        playsinline: 1,
        iv_load_policy: 3,
        modestbranding: 1,
        hl: 'ms',
        origin: location.origin,
      },
      events: { onReady, onStateChange, onError },
    });
  }

  function onReady() {
    ready = true;
    applyVolume();
    if (wantPlay) playLive();
  }

  function playLive(r: Resolved | null = resolve()) {
    if (!player || !ready || !r) return;
    playingSlot = r.slotKey;
    desync = null;
    lastLoadAt = Date.now();
    player.loadVideoById({ videoId: r.track.id, startSeconds: Math.floor(r.offsetSec) });
    applyVolume();
    try {
      player.unMute();
    } catch {
      /* abaikan */
    }
  }

  function skipTo(r: Resolved, idx: number) {
    const track = r.tracks[idx]!;
    desync = { slot: r.slotKey, idx, track };
    lastLoadAt = Date.now();
    player.loadVideoById({ videoId: track.id, startSeconds: 0 });
  }

  function onStateChange(e: { data: number }) {
    if (e.data === YT_PLAYING) {
      errorStreak = 0;
      root!.setAttribute('data-playing', '');
      setPlayingUI(true);
    } else if (e.data === YT_PAUSED) {
      // abaikan jeda sementara semasa memuatkan video baharu
      if (wantPlay && Date.now() - lastLoadAt > 2000) {
        wantPlay = false;
        setPlayingUI(false);
      }
    } else if (e.data === YT_ENDED) {
      if (wantPlay && desync && latest) skipTo(latest, (desync.idx + 1) % latest.tracks.length);
      else tick();
    }
  }

  function onError() {
    const r = latest;
    if (!r || !wantPlay) return;
    errorStreak++;
    if (errorStreak > r.tracks.length) {
      wantPlay = false;
      setPlayingUI(false);
      say('Tiada video yang dapat dimainkan sekarang. Cuba lagi sebentar lagi.', 0);
      return;
    }
    skipTo(r, ((desync?.idx ?? r.index) + 1) % r.tracks.length);
    say('Satu video tidak dapat dimainkan. Melangkau ke lagu seterusnya.');
  }

  // ---------- paparan ----------
  function applyVolume() {
    if (!player || !ready || !el.volume) return;
    try {
      player.setVolume(Number(el.volume.value));
    } catch {
      /* abaikan */
    }
  }

  function li(track: Track, n: number): HTMLLIElement {
    const item = document.createElement('li');
    item.className = 'q-item';
    const num = document.createElement('span');
    num.className = 'q-num';
    num.textContent = String(n);
    const text = document.createElement('div');
    text.className = 'q-text';
    const title = document.createElement('span');
    title.className = 'q-title';
    title.textContent = track.title;
    const channel = document.createElement('span');
    channel.className = 'q-channel';
    channel.textContent = track.channel;
    text.append(title, channel);
    const dur = document.createElement('span');
    dur.className = 'q-dur';
    dur.textContent = formatClock(track.duration);
    item.append(num, text, dur);
    return item;
  }

  function showTrack(track: Track) {
    if (shownTrackId === track.id) return;
    shownTrackId = track.id;
    el.title!.textContent = track.title;
    el.channel!.textContent = track.channel;
    el.cover?.style.setProperty('--thumb', `url("https://i.ytimg.com/vi/${encodeURIComponent(track.id)}/hqdefault.jpg")`);
    if ('mediaSession' in navigator && typeof MediaMetadata !== 'undefined') {
      navigator.mediaSession.metadata = new MediaMetadata({ title: track.title, artist: track.channel, album: 'Nasyidos' });
    }
  }

  function renderSlot(r: Resolved) {
    renderedSlot = r.slotKey;
    el.queue!.replaceChildren(...r.upNext.map((t, i) => li(t, i + 1)));
    if (r.upNext.length === 0) {
      const none = document.createElement('li');
      none.className = 'q-none';
      none.textContent = `Rancangan ${r.nextBlock.name} akan bermula pada ${r.nextBlock.start}.`;
      el.queue!.append(none);
    }
    if (el.note) {
      el.note.textContent = `Rancangan: ${r.block.name} · Seterusnya: ${r.nextBlock.name} (${r.nextBlock.start})`;
    }
    document.querySelectorAll<HTMLElement>('[data-block]').forEach((card) => {
      card.toggleAttribute('data-live', card.dataset.block === r.block.id);
    });
  }

  function renderProgress(track: Track, offsetSec: number) {
    const pct = Math.min(100, Math.max(0, (offsetSec / track.duration) * 100));
    if (el.progress) el.progress.style.width = `${pct}%`;
    if (el.bar) el.bar.setAttribute('aria-valuenow', String(Math.round(pct)));
    if (el.elapsed) el.elapsed.textContent = formatClock(offsetSec);
    if (el.total) el.total.textContent = formatClock(track.duration);
  }

  function renderEmpty() {
    el.title!.textContent = 'Siaran belum bermula';
    el.channel!.textContent = 'Senarai lagu belum ditetapkan.';
    if (el.empty) el.empty.hidden = false;
    if (el.start) el.start.hidden = true;
    if (el.toggle) el.toggle.disabled = true;
  }

  // ---------- gelung utama ----------
  function tick() {
    const r = resolve();
    latest = r;
    if (!r) return renderEmpty();

    if (r.slotKey !== renderedSlot) renderSlot(r);

    let display = r.track;
    let offset = r.offsetSec;
    if (desync && desync.slot === r.slotKey) {
      display = desync.track;
      offset = player && ready ? Number(player.getCurrentTime?.() ?? 0) : 0;
    }
    showTrack(display);
    renderProgress(display, offset);

    if (!wantPlay || !player || !ready) return;

    if (r.slotKey !== playingSlot) {
      playLive(r);
      return;
    }
    // betulkan hanyutan masa jika pemain terlalu jauh daripada jam
    if (!desync && player.getPlayerState?.() === YT_PLAYING) {
      const drift = Math.abs(Number(player.getCurrentTime()) - r.offsetSec);
      driftTicks = drift > 6 ? driftTicks + 1 : 0;
      if (driftTicks >= 3) {
        driftTicks = 0;
        player.seekTo(r.offsetSec, true);
      }
    }
  }

  // ---------- interaksi ----------
  async function beginListening() {
    wantPlay = true;
    root!.setAttribute('data-started', '');
    setPlayingUI(true);
    if (ready) return playLive();
    say('Menyediakan pemain…', 0);
    try {
      await loadApi();
      const r = resolve();
      if (r) createPlayer(r.track.id); // onReady -> playLive()
      say('');
    } catch {
      wantPlay = false;
      root!.removeAttribute('data-started');
      setPlayingUI(false);
      say('Pemain YouTube tidak dapat dimuatkan. Semak sambungan internet atau penyekat iklan, kemudian cuba lagi.', 0);
    }
  }

  el.start?.addEventListener('click', beginListening);

  el.toggle?.addEventListener('click', () => {
    if (wantPlay && ready) {
      wantPlay = false;
      player.pauseVideo();
      setPlayingUI(false);
    } else if (!wantPlay) {
      beginListening(); // sertai semula siaran semasa
    }
  });

  el.share?.addEventListener('click', async () => {
    const url = `${location.origin}/`;
    try {
      if (navigator.share) {
        await navigator.share({ title: document.title, text: 'Dengar siaran nasyid 24 jam di nasyidos.com', url });
      } else {
        await navigator.clipboard.writeText(url);
        say('Pautan disalin.', 3000);
      }
    } catch {
      /* pengguna membatalkan */
    }
  });

  if (el.fs) {
    if (document.fullscreenEnabled && el.stage?.requestFullscreen) {
      el.fs.addEventListener('click', () => {
        if (document.fullscreenElement) void document.exitFullscreen();
        else void el.stage!.requestFullscreen();
      });
    } else {
      el.fs.hidden = true;
    }
  }

  if (el.volume) {
    try {
      const saved = localStorage.getItem('nasyidos:volume');
      if (saved !== null && Number(saved) >= 0 && Number(saved) <= 100) el.volume.value = saved;
    } catch {
      /* abaikan */
    }
    el.volume.addEventListener('input', () => {
      applyVolume();
      try {
        localStorage.setItem('nasyidos:volume', el.volume!.value);
      } catch {
        /* abaikan */
      }
    });
  }

  if ('mediaSession' in navigator) {
    try {
      navigator.mediaSession.setActionHandler('play', () => !wantPlay && beginListening());
      navigator.mediaSession.setActionHandler('pause', () => el.toggle?.click());
    } catch {
      /* tidak disokong */
    }
  }

  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) tick();
  });

  // ---------- mula ----------
  setPlayingUI(false);
  tick();
  window.setInterval(tick, 1000);
  void syncClock().then(tick);

  if (resolve()) {
    // muat pemain semasa terluang supaya butang Mula terus berfungsi dalam satu klik
    const warm = () =>
      loadApi()
        .then(() => {
          const r = resolve();
          if (r) createPlayer(r.track.id);
        })
        .catch(() => {});
    window.addEventListener('load', () => {
      const idle = (window as any).requestIdleCallback as undefined | ((cb: () => void, o?: object) => void);
      if (typeof idle === 'function') idle(warm, { timeout: 4000 });
      else setTimeout(warm, 1500);
    });
  }
}

main();
