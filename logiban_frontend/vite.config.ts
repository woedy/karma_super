import { defineConfig, loadEnv } from 'vite';
import react from '@vitejs/plugin-react';

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
  // Load env file based on `mode` in the current working directory.
  // Set the third parameter to '' to load all env regardless of the `VITE_` prefix.
  const env = loadEnv(mode, process.cwd(), '');

  return {
    plugins: [react()],
    optimizeDeps: {
      exclude: ['lucide-react'],
    },
    // Define environment variables that should be available in the client
    define: {
      __APP_VERSION__: JSON.stringify(process.env.npm_package_version),
    },
    // Expose environment variables to the client (only VITE_ prefixed ones)
    envPrefix: 'VITE_',
    server: {
      port: 3000,
      proxy: {
        // Proxy API requests to Django backend during development
        '/api': {
          target: env.VITE_API_BASE_URL || 'http://localhost:8000',
          changeOrigin: true,
          secure: false,
        },
      },
    },
  };
});
