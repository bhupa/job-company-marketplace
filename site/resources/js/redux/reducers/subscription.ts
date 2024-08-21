import { createSlice, PayloadAction } from '@reduxjs/toolkit';

const initialState = {
  dailyCount: 0,
  dailyLimit: 0,
  favoriteCount: 0,
  favoriteLimit: 0,
  chatRequestCount: 0,
  chatRequestLimit: 0,
  needsRefresh: false,
};

const subscriptionSlice = createSlice({
  name: 'subscription',
  initialState: initialState,
  reducers: {
    setMatchingLimit(state, action: PayloadAction<IMatchingLimit>) {
      return {
        ...state,
        ...action.payload,
      };
    },
    setNeedsSubscriptionRefresh(state, action: PayloadAction<boolean>) {
      state.needsRefresh = action.payload;
    },
  },
});

export const { setMatchingLimit, setNeedsSubscriptionRefresh } =
  subscriptionSlice.actions;
const subscriptionReducer = subscriptionSlice.reducer;

export default subscriptionReducer;

interface IMatchingLimit {
  dailyCount?: number;
  dailyLimit?: number;
  favoriteCount?: number;
  favoriteLimit?: number;
  chatRequestCount?: number;
  chatRequestLimit?: number;
}
