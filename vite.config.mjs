import { defineConfig, loadEnv } from 'vite';
import FullReload from 'vite-plugin-full-reload';
import { v4wp } from '@kucrut/vite-for-wp';
import fs from 'node:fs';
import path from 'node:path';
import getPort, { portNumbers } from 'get-port';

function readSiteUrl() {
  try {
    return fs.readFileSync(path.resolve(__dirname, '.site-url'), 'utf8').trim();
  } catch {
    return null;
  }
}

const FALLBACK_URL = 'http://localhost:8080/';
const siteUrlPath = path.resolve(__dirname, '.site-url');
const raw = readSiteUrl() || FALLBACK_URL;
const site = new URL(raw);

const isHttps = site.protocol === 'https:';
console.log('[vite] .site-url path =', siteUrlPath);
console.log('[vite] .site-url raw  =', raw || '(FALLBACK)');
console.log(
  '[vite] env VITE_DEV_PORT (OS) =',
  process.env.VITE_DEV_PORT || '(unset)'
);
console.log(
  '[vite] env NODE_ENV (OS)      =',
  process.env.NODE_ENV || '(unset)'
);
console.log('[vite] host (site.hostname) =', site.hostname);
try {
  console.log('[vite] parsed:', {
    protocol: site.protocol,
    host: site.hostname,
    pathname: site.pathname,
  });
} catch (e) {
  console.warn('[vite] URL parse failed for', raw, e?.message);
}

export default defineConfig(async ({ mode }) => {
  // Подхватываем значения из .env.* (Vite не кладет их в process.env автоматически для конфига)
  const env = loadEnv(mode, process.cwd(), 'VITE_');
  console.log(
    '[vite] env VITE_DEV_PORT (.env) =',
    env.VITE_DEV_PORT || '(unset)'
  );

  const preferred = Number(
    env.VITE_DEV_PORT || process.env.VITE_DEV_PORT || 5173
  );
  const devPort = await getPort({
    // Диапазон: 5173..5193 (можно сузить/расширить)
    port: portNumbers(preferred, preferred + 20),
    // host: site.hostname, // обычно лучше не указывать, чтобы проверить все локальные адреса
  });

  console.log('[vite] devPort chosen =', devPort);

  const DebugLogger = () => ({
    name: 'debug-logger',
    configResolved(config) {
      try {
        const { base, server, build } = config;
        const summary = {
          base,
          server: {
            host: server.host,
            port: server.port,
            strictPort: server.strictPort,
            https: Boolean(server.https),
            cors: server.cors,
            origin: server.origin,
            hmr:
              typeof server.hmr === 'object'
                ? {
                    protocol: server.hmr.protocol,
                    host: server.hmr.host,
                    port: server.hmr.port,
                    clientPort: server.hmr.clientPort,
                  }
                : server.hmr,
          },
          build: {
            outDir: build.outDir,
            assetsDir: build.assetsDir,
            manifest: build.manifest,
          },
        };
        console.log('[vite:debug] resolved config:', summary);
      } catch (err) {
        console.log('[vite:debug] failed to log config:', err?.message);
      }
    },
    configureServer(server) {
      const onListening = () => {
        try {
          console.log(
            '[vite:debug] server origin =',
            server.config.server.origin
          );
          console.log('[vite:debug] server hmr    =', server.config.server.hmr);
          server.printUrls?.();
        } catch (err) {
          console.log('[vite:debug] printUrls failed:', err?.message);
        }
      };
      server.httpServer?.once('listening', onListening);
    },
    buildStart() {
      console.log('[vite:debug] buildStart');
    },
    generateBundle(_, bundle) {
      try {
        const files = Object.keys(bundle);
        console.log('[vite:debug] generateBundle artifacts:', files.length);
      } catch {}
    },
  });

  return {
    base: site.pathname.endsWith('/') ? site.pathname : site.pathname + '/',
    server: {
      host: site.hostname,
      port: devPort,
      strictPort: true,
      origin: `${site.protocol}//${site.hostname}:${devPort}`,
      hmr: {
        protocol: isHttps ? 'wss' : 'ws',
        host: site.hostname,
        port: devPort,
        clientPort: devPort,
        overlay: true,
      },
      // Если ловишь CORS между :80/8888 и :5173 — раскомментируй:
      cors: true,
      headers: {
        'Access-Control-Allow-Origin': site.port
          ? `${site.protocol}//${site.hostname}:${site.port}`
          : `${site.protocol}//${site.hostname}`,
      },
      https: isHttps || false,
    },
    plugins: [
      DebugLogger(),
      v4wp({
        input: {
          VICTORY: 'assets/js/src/main.ts',
          cartStore: 'assets/js/src/modules/cartv3/store/cart.store.ts',
        },
        outDir: 'assets/build',
      }),
      FullReload(['./**/*.php'], { delay: 150 }),
    ],
    build: {
      manifest: true,
      outDir: 'assets/build',
      // если не нужна промежуточная папка assets/ внутри build:
      assetsDir: '',

      // чтобы мелкие картинки не инлайнились в base64:
      assetsInlineLimit: 0,

      rollupOptions: {
        output: {
          entryFileNames: 'js/[name]-[hash].js',
          chunkFileNames: 'js/[name]-[hash].js',
          assetFileNames: (assetInfo) => {
            const name = assetInfo.name ?? '';
            const ext = name.split('.').pop()?.toLowerCase() ?? '';

            // 👇 все изображения в папку images/
            if (/(png|jpe?g|gif|svg|webp|ico|avif)$/i.test(ext)) {
              return 'images/[name]-[hash][extname]'; // или без хэша: "images/[name][extname]"
            }

            // css отдельно (если хочешь в свою папку)
            if (ext === 'css') return 'css/[name]-[hash][extname]';

            // шрифты в fonts/
            if (/(woff2?|ttf|otf|eot)$/i.test(ext)) {
              return 'fonts/[name]-[hash][extname]';
            }

            // всё остальное по умолчанию
            return '[name]-[hash][extname]';
          },
        },
      },
    },
    emptyOutDir: true,
  };
});
