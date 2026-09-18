module.exports = {
  content: [
    './application/views/**/*.php',
    './js/**/*.js'
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Google Sans', 'Google Sans Text', 'Product Sans', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
      },
      colors: {
        cams: {
          50: '#effdf7',
          100: '#d8f8e8',
          200: '#b4efd3',
          300: '#7ee0b7',
          400: '#43c996',
          500: '#20ad7c',
          600: '#168a64',
          700: '#146f53',
          800: '#135944',
          900: '#104a3a',
          950: '#062f25'
        }
      },
      boxShadow: {
        soft: '0 18px 45px rgba(15, 23, 42, 0.08)',
        panel: '0 12px 32px rgba(2, 47, 37, 0.18)'
      }
    }
  },
  plugins: []
};
