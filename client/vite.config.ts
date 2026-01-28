import { defineConfig, loadEnv } from "vite";
import react from "@vitejs/plugin-react";
import path from "path";

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), "");

  // Configuration common to all modes
  const config = {
    plugins: [react()],
    resolve: {
      alias: {
        "@": path.resolve(__dirname, "./src"),
      },
    },
    build: {
      outDir: path.resolve(__dirname, "../public"),
      emptyOutDir: false, // Don't delete public/index.php
      rollupOptions: {
        output: {
          manualChunks: (id: string) => {
            if (id.includes("node_modules")) {
              if (
                id.includes("react") ||
                id.includes("react-dom") ||
                id.includes("react-router-dom")
              ) {
                return "react-vendor";
              }
              // Monaco editor is quite large, keep it separate
              if (
                id.includes("@monaco-editor") ||
                id.includes("monaco-editor")
              ) {
                return "monaco-vendor";
              }
            }
          },
        },
      },
    },
  };

  // Development-specific configuration
  if (mode === "development") {
    const target = env.VITE_API_TARGET || "http://localhost:8100";
    return {
      ...config,
      server: {
        proxy: {
          "/post": {
            target: target,
            changeOrigin: true,
            secure: false,
          },
        },
      },
    };
  }

  // Production configuration
  return config;
});
