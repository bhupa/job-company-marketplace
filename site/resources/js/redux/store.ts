import Cookies from 'js-cookie';
import storage from 'redux-persist/lib/storage';
import { persistStore, persistReducer } from 'redux-persist';
import { combineReducers, configureStore } from '@reduxjs/toolkit';

import homeReducer from './reducers/home';
import authReducer from './reducers/auth';
import chatReducer from './reducers/chat';
import { authApi } from './services/authApi';
import filterReducer from './reducers/filter';
import companyReducer from './reducers/company';
import requestReducer from './reducers/requests';
import bookmarkReducer from './reducers/bookmark';
import jobSeekerReducer from './reducers/jobSeeker';
import subscriptionReducer from './reducers/subscription';
import profileReducer from './reducers/profile';

const reducers = combineReducers({
  //all slices
  auth: authReducer,
  jobSeeker: jobSeekerReducer,
  company: companyReducer,
  subscription: subscriptionReducer,
  requests: requestReducer,
  chat: chatReducer,
  bookmark: bookmarkReducer,
  home: homeReducer,
  filter: filterReducer,
  profile: profileReducer,
  //all apis
  [authApi.reducerPath]: authApi.reducer,
});

const persistConfig = {
  key: 'root',
  storage,
  whitelist: ['auth', 'chat', 'filter'],
  blackList: [
    authApi.reducerPath,
    'jobSeeker',
    'company',
    'subscription',
    'bookmark',
    'home',
    'profile',
  ],
};

const rootReducer = (
  state: ReturnType<typeof reducers> | undefined,
  action: { type: string },
) => {
  if (action.type === 'LOGOUT') {
    Cookies.remove('accessToken');
    // Reset all state to initial state on logout
    state = undefined;
    // window?.location?.reload();
  }
  return reducers(state, action);
};

const persistedReducer = persistReducer(persistConfig, rootReducer);

export const store = configureStore({
  reducer: persistedReducer,
  middleware: (getDefaultMiddleware: any) =>
    getDefaultMiddleware({
      serializableCheck: false,
      immutableCheck: false,
    }).concat([authApi.middleware]),
  devTools: import.meta.env.VITE_APP_ENV !== 'production',
});

export const persistor = persistStore(store);

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;
