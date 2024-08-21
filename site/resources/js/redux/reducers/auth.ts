import Cookies from 'js-cookie';
import { createSlice, PayloadAction } from '@reduxjs/toolkit';

import {
  IForgotResponse,
  IUser,
  SOCIALPROVIDER,
} from '@redux/services/authApi';

export enum UserType {
  JobSeeker = 1,
  Company = 2,
}

const initialState = {
  userType: UserType.JobSeeker,
  isLoggedIn: false,
  provider: SOCIALPROVIDER.DEFAULT,
  signupData: {
    email: '',
    countryCode: '',
    phone: '',
    password: '',
    confirmPassword: '',
  },
  user: {
    id: '',
    email: '',
    countryCode: 0,
    phone: '',
    userType: UserType.JobSeeker,
    status: 1,
    isProfileComplete: false,
    deviceToken: '',
    appleId: '',
    facebookId: '',
    googleId: '',
    language: '',
  } as IUser,
  forgotData: {
    userId: 0,
    email: '',
    countryCode: 0,
    phone: '',
  },
  errorMessage: '',
};

const authSlice = createSlice({
  name: 'auth',
  initialState: initialState,
  reducers: {
    saveUser(state, action: PayloadAction<IAuthData>) {
      const { isLoggedIn, user, provider, accessToken } = action.payload;
      Cookies.set('accessToken', accessToken, { expires: 365 });
      state.isLoggedIn = isLoggedIn;
      state.provider = provider || SOCIALPROVIDER.DEFAULT;
      state.userType = user.userType;
      state.user = {
        ...state.user,
        ...user,
      };
    },
    setUserType(state, action: PayloadAction<UserType>) {
      state.userType = action.payload;
    },
    setSignupData(state, action: PayloadAction<ISignupData>) {
      state.signupData = {
        ...state.signupData,
        ...action.payload,
      };
    },
    setForgotData(state, action: PayloadAction<IForgotResponse>) {
      state.forgotData = action.payload;
    },
    setLoggedIn(state, action: PayloadAction<boolean>) {
      state.isLoggedIn = action.payload;
      state.user.isProfileComplete = true;
    },
    saveAccessToken(state, action: PayloadAction<string>) {
      Cookies.set('accessToken', action.payload, { expires: 365 });
    },
    setDeviceLanguage(state, action: PayloadAction<string>) {
      state.user.language = action.payload;
    },
    setErrorMessage(state, action: PayloadAction<string>) {
      state.errorMessage = action.payload;
    },
    resetAuthData() {
      Cookies.remove('accessToken');
      return initialState;
    },
  },
});

export const logout = () => ({
  type: 'LOGOUT',
});

export const {
  saveUser,
  setUserType,
  setSignupData,
  resetAuthData,
  setForgotData,
  setLoggedIn,
  saveAccessToken,
  setDeviceLanguage,
  setErrorMessage,
} = authSlice.actions;
const authReducer = authSlice.reducer;

export default authReducer;

export interface IAuthData {
  isLoggedIn: boolean;
  accessToken: string;
  provider: SOCIALPROVIDER;
  user: IUser;
}

export interface ISignupData {
  email?: string;
  countryCode?: string;
  phone?: string;
  password?: string;
  confirmPassword?: string;
}
