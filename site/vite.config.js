import { defineConfig } from 'vite';
import svgr from 'vite-plugin-svgr';
import laravel from 'laravel-vite-plugin';
import viteReact from '@vitejs/plugin-react';

// const useHttps = process.env.VITE_HTTPS === 'true';
const appUrl = new URL(process.env.VITE_BASE_URL || 'http://localhost');
export default defineConfig(({ command, mode }) => ({
  plugins: [
    laravel({
      input: [
        'resources/sass/app.scss',
        'resources/js/app.jsx',
        'resources/css/app.css',
      ],
      refresh: true,
    }),
    viteReact(),
    svgr({
      include: '**/*.svg',
    }),
  ],
  resolve: {
    alias: {
      '@': '/resources/js',
      '@assets': '/resources/js/assets',
      '@components': '/resources/js/components',
      '@constants': '/resources/js/constants',
      '@customHooks': '/resources/js/customHooks',
      '@lang': '/resources/js/lang',
      '@pages': '/resources/js/pages',
      '@redux': '/resources/js/redux',
      '@routes': '/resources/js/routes',
      '@templates': '/resources/js/templates',
      '@utils': '/resources/js/utils',
      '@services': '/resources/js/services',
    },
  },
  server: {
    // https: useHttps, // Use the converted boolean value here
    hmr: {
      // Set the hmr connection URL
      host: appUrl.hostname,
      port: appUrl.port ?? 3000,
       protocol: appUrl.protocol === 'https:' ? 'wss' : 'ws',
    },
  },
}));
