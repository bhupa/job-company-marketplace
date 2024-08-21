import React from 'react';
import ReactDOM from 'react-dom/client';
import TagManager from 'react-gtm-module';

import App from './pages';

const { VITE_GOOGLE_TAG_MANAGER_ID } = import.meta.env;

const tagManagerArgs = {
  gtmId: VITE_GOOGLE_TAG_MANAGER_ID,
  events: {
    pageview: 'Page View',
  },
};

TagManager.initialize(tagManagerArgs);

const root = ReactDOM.createRoot(document.getElementById('app'));
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
);
