import React from 'react';
import { Provider } from 'react-redux';
import { GoogleOAuthProvider } from '@react-oauth/google';

import '@lang';
import { store } from '@redux/store';
import AppRouter from '@routes/index';

// Others
import { MessageProvider } from '@customHooks/useShowMessage';

// Firebase
import { getAuth } from 'firebase/auth';
import { firebaseConfig } from '../firebase';
import { initializeApp } from 'firebase/app';
import { getMessaging } from 'firebase/messaging';

export const firebaseApp = initializeApp(firebaseConfig);
export const auth = getAuth(firebaseApp);
export const messaging = getMessaging(firebaseApp);

const { VITE_GOOGLE_WEB_CLIENT_ID = '' } = import.meta.env;

const App = () => {
  return (
    <GoogleOAuthProvider clientId={VITE_GOOGLE_WEB_CLIENT_ID}>
      <MessageProvider>
        <Provider store={store}>
          <AppRouter />
        </Provider>
      </MessageProvider>
    </GoogleOAuthProvider>
  );
};

export default App;
