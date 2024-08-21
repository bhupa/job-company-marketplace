import { createSlice, PayloadAction } from '@reduxjs/toolkit';

const initialState = {
  isProfilePickerVisible: false,
};

const profileSlice = createSlice({
  name: 'profile',
  initialState: initialState,
  reducers: {
    setProfilePickerVisible(state, action: PayloadAction<boolean>) {
      state.isProfilePickerVisible = action.payload;
    },
  },
});

export const { setProfilePickerVisible } = profileSlice.actions;
const profileReducer = profileSlice.reducer;

export default profileReducer;
