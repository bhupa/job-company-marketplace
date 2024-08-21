import { createSlice, PayloadAction } from '@reduxjs/toolkit';

const initialState = {
  requestCount: 0,
  // canRefresh is used to set whether or not to refresh request list
  canRefresh: false,
  needsRefresh: false,
};

const requestSlice = createSlice({
  name: 'requests',
  initialState: initialState,
  reducers: {
    setTotalRequests(state, action: PayloadAction<number>) {
      state.requestCount = action.payload;
      if (state.canRefresh) {
        state.needsRefresh = true;
      }
      // When app is opened, set it to false so that from next time when request count event is fired, refresh request list.
      state.canRefresh = true;
    },
    setNeedsRequestRefresh(state, action: PayloadAction<boolean>) {
      state.needsRefresh = action.payload;
    },
  },
});

export const { setTotalRequests, setNeedsRequestRefresh } =
  requestSlice.actions;
const requestReducer = requestSlice.reducer;

export default requestReducer;
