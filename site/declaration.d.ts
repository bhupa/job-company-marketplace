declare module '*.png';
declare module '*.svg' {
  const content: React.FC<React.SVGProps<SVGElement>>;
  export default content;
}
declare module '*.jpeg';
declare module '*.jpg';
declare module '*.json';
declare module 'js-cookie';
declare module 'crypto-js';
declare module 'react-gtm-module';

interface ImportMeta {
  env: {
    VITE_APP_ENV: string;
    VITE_APP_NAME: string;
    VITE_PUSHER_APP_KEY: string;
    VITE_PUSHER_HOST: string;
    VITE_PUSHER_PORT: string;
    VITE_PUSHER_SCHEME: string;
    VITE_PUSHER_APP_CLUSTER: string;
    VITE_BASE_URL: string;
    VITE_PREFIX_URL: string;
    VITE_GOOGLE_WEB_CLIENT_ID: string;
    VITE_FIREBASE_API_KEY: string;
    VITE_FIREBASE_AUTH_DOMAIN: string;
    VITE_FIREBASE_PROJECT_ID: string;
    VITE_FIREBASE_STORAGE_BUCKET: string;
    VITE_FIREBASE_MESSAGING_SENDER_ID: string;
    VITE_FIREBASE_APP_ID: string;
    VITE_FIREBASE_MEASUREMENT_ID: string;
    VITE_FACEBOOK_APP_ID: string;
    VITE_APPLE_SERVICE_ID: string;
    VITE_APPLE_REDIRECT_URL: string;
    VITE_CHAT_SERVER_HOST_URL: string;
    VITE_ESEWA_BASE_URL: string;
    VITE_ESEWA_PREFIX_URL: string;
    VITE_ESEWA_PRODUCT_CODE: string;
    VITE_ESEWA_SECRET_KEY: string;
    VITE_GOOGLE_TAG_MANAGER_ID: string;
  };
}
