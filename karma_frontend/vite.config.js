import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0', // This allows the server to be accessible from outside the container
    port: 7011, // Make sure the port matches what you're mapping in docker-compose
  },
})

