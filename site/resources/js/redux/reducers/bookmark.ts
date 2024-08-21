import { createSlice, PayloadAction } from '@reduxjs/toolkit';

const initialState = {
  needsRefresh: false,
};

const bookmarkSlice = createSlice({
  name: 'bookmark',
  initialState: initialState,
  reducers: {
    setNeedsBookmarkRefresh(state, action: PayloadAction<boolean>) {
      state.needsRefresh = action.payload;
    },
  },
});

export const { setNeedsBookmarkRefresh } = bookmarkSlice.actions;
const bookmarkReducer = bookmarkSlice.reducer;

export default bookmarkReducer;
