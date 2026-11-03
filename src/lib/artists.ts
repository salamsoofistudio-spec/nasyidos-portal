import type { Library } from './schedule';

export interface Artist {
  name: string;
  channelId?: string;
  count: number;
}

/** Senarai saluran unik daripada pustaka lagu, yang paling banyak lagu dahulu. */
export function collectArtists(library: Library, limit = 8): Artist[] {
  const map = new Map<string, Artist & { ids: Set<string> }>();
  for (const tracks of Object.values(library)) {
    for (const t of tracks) {
      const key = t.channelId || t.channel;
      if (!key || !t.channel) continue;
      let entry = map.get(key);
      if (!entry) {
        entry = { name: t.channel, channelId: t.channelId, count: 0, ids: new Set() };
        map.set(key, entry);
      }
      entry.ids.add(t.id);
    }
  }
  return [...map.values()]
    .map(({ ids, ...rest }) => ({ ...rest, count: ids.size }))
    .sort((a, b) => b.count - a.count || a.name.localeCompare(b.name))
    .slice(0, limit);
}
