import 'intl-pluralrules';
import i18n, { Module } from 'i18next';
import { initReactI18next } from 'react-i18next';

// Translation files en
import auth from './en/authTexts.json';
import home from './en/home.json';
import chat from './en/chat.json';
import jobs from './en/jobs.json';
import common from './en/common.json';
import profile from './en/profile.json';
import company from './en/company.json';
import messages from './en/messages.json';
import jobseeker from './en/jobseeker.json';
import notification from './en/notification.json';

// Translation files ja
import auth_ja from './ja/authTexts.json';
import home_ja from './ja/home.json';
import chat_ja from './ja/chat.json';
import jobs_ja from './ja/jobs.json';
import common_ja from './ja/common.json';
import profile_ja from './ja/profile.json';
import company_ja from './ja/company.json';
import messages_ja from './ja/messages.json';
import jobseeker_ja from './ja/jobseeker.json';
import notification_ja from './ja/notification.json';

export const resources = {
  en: {
    auth,
    profile,
    common,
    jobseeker,
    home,
    chat,
    company,
    jobs,
    notification,
    messages,
  },
  ja: {
    auth: auth_ja,
    profile: profile_ja,
    common: common_ja,
    jobseeker: jobseeker_ja,
    home: home_ja,
    chat: chat_ja,
    company: company_ja,
    jobs: jobs_ja,
    notification: notification_ja,
    messages: messages_ja,
  },
} as const;

const languageDetector = {
  init: Function.prototype,
  type: 'languageDetector',
  async: true, // flags below detection to be async
  detect: (callback: any) => {
    const savedLanguage = localStorage.getItem('selectedLanguage');
    // Fetch device language
    const deviceLanguage = navigator?.language ?? 'en';
    /**
     * If device language is available in app use device language.
     * If language is selected from app settings use selected language.
     * Use english (default)
     */
    const selectedLanguage = savedLanguage
      ? savedLanguage
      : Object.keys(resources).includes(deviceLanguage.split('_')[0])
        ? deviceLanguage.split('_')[0]
        : 'en';
    if (!savedLanguage) {
      localStorage.setItem('selectedLanguage', selectedLanguage);
    }
    callback(selectedLanguage);
  },
  cacheUserLanguage: () => {},
};

i18n
  .use(languageDetector as Module)
  .use(initReactI18next) // passes i18n down to react-i18next
  .init({
    fallbackLng: 'en',
    resources,
    react: {
      useSuspense: false,
    },
    initImmediate: false,
    interpolation: {
      escapeValue: false, // react already safes from xss
    },
  });

export default i18n;

export { languageDetector };
