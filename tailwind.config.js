/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      animation: {
        'blob': 'blob 12s infinite',
        'float': 'float 6s ease-in-out infinite',
        'pulse-glow': 'pulse-glow 3s ease-in-out infinite',
        'gradient-x': 'gradient-x 8s ease infinite',
      },
      keyframes: {
        blob: {
          '0%, 100%': { transform: 'scale(1)' },
          '33%': { transform: 'scale(1.1) translate(50px, -20px) rotate(20deg)' },
          '66%': { transform: 'scale(0.9) translate(-30px, 30px) rotate(-20deg)' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-10px)' },
        },
        'pulse-glow': {
          '0%, 100%': { opacity: '0.6' },
          '50%': { opacity: '1' },
        },
        'gradient-x': {
          '0%, 100%': { backgroundPosition: '0% 50%' },
          '50%': { backgroundPosition: '100% 50%' },
        },
      },
      backdropBlur: {
        'xl': '20px',
        '2xl': '30px',
        '3xl': '40px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
