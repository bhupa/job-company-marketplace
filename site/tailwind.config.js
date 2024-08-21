/** @type {import('tailwindcss').Config} */
export default {
  content: ['./resources/**/*.blade.php', './resources/**/*.{js,jsx,ts,tsx}'],
  theme: {
    extend: {
      colors: {
        primary: '#004D80',
        disabled: '#A6A6A6',

        // White
        WHITE_F6F6F6: '#F6F6F6',
        WHITE_E8E6EA: '#E8E6EA',
        WHITE_E0E2E4: '#E0E2E4',
        WHITE_EFF0F2: '#EFF0F2',
        WHITE_EFEFEF: '#EFEFEF',
        SHADOW_93939340: '#939393', // DARK
        SHADOW_00000014: '#000000', // LIGHT

        // Blue
        BLUE_004D80: '#004D80',
        BLUE_25396F: '#25396F',
        BLUE_368FF9: '#368FF9',
        BLUE_D9E3FF: '#D9E3FF',
        BLUE_004D80E0: '#004D80E0',
        BLUE_004D801A: '#004D801A',
        BLUE_0D80FF0D: '#0D80FF0D',

        // RED
        RED_FF4D4D: '#FF4D4D',

        // GREEN
        GREEN_4EBE59: '#4EBE59',

        // ORANGE
        ORANGE_EFC269: '#EFC269',

        // GRAY
        GRAY_9A9A9A: '#9A9A9A',
        GRAY_807C83: '#807C83',
        GRAY_77838F: '#77838F',
        GRAY_5E5E5E: '#5E5E5E',
        GRAY_545454: '#545454',
        GRAY_A6A6A6: '#A6A6A6',
        GRAY_ADAFBB: '#ADAFBB',
        GRAY_ACACAC: '#ACACAC',

        // BLACK
        BLACK_1E2022: '#1E2022',
        BLACK_656565: '#656565',
        BLACK_323635: '#323635',
        BLACK_000000B2: '#000000B2', // transparent black
        BLACK_000000E2: '#000000E2', // more dark
      },
      screens: {
        xs: '420px',
      },
    },
  },
  plugins: [],
};
