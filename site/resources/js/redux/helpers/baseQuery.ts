import {
  BaseQueryFn,
  FetchArgs,
  fetchBaseQuery,
  FetchBaseQueryError,
} from '@reduxjs/toolkit/query';
import Cookies from 'js-cookie';

// Others
import i18n from '../../lang';
import messagesEn from '@lang/en/messages.json';
import messagesJa from '@lang/ja/messages.json';
import { store } from '@redux/store';
import { logout, setErrorMessage } from '@redux/reducers/auth';

const { VITE_BASE_URL = '', VITE_PREFIX_URL = '' } = import.meta.env;

const generateUniqueId = () => {
  let id = Cookies.get('deviceId');
  if (!id) {
    id = Math.random().toString(36).substring(2, 9);
    Cookies.set('deviceId', id);
  }
  return id;
};

const baseQueryForUrl = fetchBaseQuery({
  baseUrl: VITE_BASE_URL + VITE_PREFIX_URL,
  timeout: 15000,
  prepareHeaders: async headers => {
    const token = Cookies.get('accessToken');
    if (token) {
      headers.set('Authorization', `Bearer ${token}`);
    }
    headers.set('DeviceID', generateUniqueId());
    headers.set('UserAgent', navigator?.userAgent ?? '');
    headers.set('Accept-Language', i18n.language);
    return headers;
  },
});

export const baseQuery: BaseQueryFn<
  string | FetchArgs,
  unknown,
  FetchBaseQueryError
> = async (args, api, extraOptions) => {
  const result: any = await baseQueryForUrl(args, api, extraOptions);
  const messages = i18n?.language === 'ja' ? messagesJa : messagesEn;
  if (result.error) {
    console.log(result);
    if (
      result.error.status === 401 &&
      !result.meta?.request?.url?.includes('/register')
    ) {
      // remove token and logout
      store.dispatch(logout());
    } else {
      const errorMsgObj = result.error?.data?.errors;
      if (errorMsgObj) {
        if (errorMsgObj.message) {
          store.dispatch(setErrorMessage(errorMsgObj.message));
        } else {
          const errorKeys = Object.keys(errorMsgObj);
          let errMsg = errorMsgObj?.[errorKeys?.[0]]?.[0];
          if (typeof errMsg !== 'string') {
            errMsg = null;
          }
          store.dispatch(setErrorMessage(errMsg ?? messages.somethingWrong));
        }
      } else {
        store.dispatch(setErrorMessage(messages.somethingWrong));
      }
    }
  }
  return result;
};
