<style>
    @layer base {
      html, body {
        width: 100%;
        margin: 0;
        padding: 0;
        background-color: #f8fafc;
        color: #1e293b;
        font-family: 'Inter', sans-serif;
      }
      ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
      }
      ::-webkit-scrollbar-track {
        background: #f1f5f9;
      }
      ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
      }
      ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
      }
    }
  </style>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "surface-bright": "#39393b",
            "primary-fixed": "#e2dfff",
            "primary-container": "#4f46e5",
            "on-tertiary": "#003640",
            "tertiary-fixed": "#acedff",
            "secondary-fixed-dim": "#adc6ff",
            "on-tertiary-fixed-variant": "#004e5c",
            "surface-container-highest": "#353437",
            "surface-tint": "#c3c0ff",
            "on-secondary-fixed": "#001a42",
            "primary-fixed-dim": "#c3c0ff",
            "surface-container-low": "#1c1b1d",
            "on-secondary-fixed-variant": "#004395",
            "on-secondary-container": "#e6ecff",
            "on-tertiary-container": "#93e8ff",
            "on-primary-container": "#dad7ff",
            "tertiary": "#4cd7f6",
            "outline-variant": "#464555",
            "on-surface-variant": "#c7c4d8",
            "secondary-fixed": "#d8e2ff",
            "inverse-on-surface": "#313032",
            "surface-container": "#201f22",
            "tertiary-fixed-dim": "#4cd7f6",
            "on-primary-fixed": "#0f0069",
            "error-container": "#93000a",
            "surface": "#131315",
            "surface-dim": "#131315",
            "error": "#ffb4ab",
            "inverse-surface": "#e5e1e4",
            "on-primary": "#1d00a5",
            "on-error-container": "#ffdad6",
            "secondary": "#adc6ff",
            "primary": "#c3c0ff",
            "background": "#131315",
            "on-primary-fixed-variant": "#3323cc",
            "on-secondary": "#002e6a",
            "on-error": "#690005",
            "on-surface": "#e5e1e4",
            "surface-variant": "#353437",
            "secondary-container": "#0566d9",
            "on-tertiary-fixed": "#001f26",
            "on-background": "#e5e1e4",
            "tertiary-container": "#006a7c",
            "surface-container-lowest": "#0e0e10",
            "inverse-primary": "#4d44e3",
            "outline": "#918fa1",
            "surface-container-high": "#2a2a2c"
          },
          borderRadius: {
            "DEFAULT": "0.25rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "full": "9999px"
          },
          fontFamily: {
            "body-md": ["Inter", "sans-serif"],
            "body-sm": ["Inter", "sans-serif"],
            "body-lg": ["Inter", "sans-serif"],
            "headline-lg": ["Inter", "sans-serif"],
            "label-sm": ["Inter", "sans-serif"],
            "headline-xl": ["Inter", "sans-serif"],
            "headline-md": ["Inter", "sans-serif"],
            "headline-sm": ["Inter", "sans-serif"],
            "label-md": ["Inter", "sans-serif"],
            "data-mono": ["JetBrains Mono", "monospace"]
          }
        }
      }
    };
  </script>