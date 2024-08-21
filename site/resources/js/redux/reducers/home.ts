import { IJobData } from '@redux/services/jobsApi';
import { createSlice, PayloadAction } from '@reduxjs/toolkit';
import { IRequestResponse } from '@redux/services/matchingApi';
import { IJobSeekerProfile } from '@redux/services/jobSeekerApi';

const initialState = {
  homeData: [] as IJobData[] | IJobSeekerProfile[],
  isMatchingModalVisible: false,
  matchingModalData: [] as IRequestResponse[],
  needsHomeRefresh: false,
  leftSwipedItems: [] as IJobData[] | IJobSeekerProfile[],
  isUndoAllowed: false,
  isFetched: false,
};

const homeSlice = createSlice({
  name: 'home',
  initialState: initialState,
  reducers: {
    setHomeData(
      state,
      action: PayloadAction<{
        data: IJobData[] | IJobSeekerProfile[];
        // Used to reset fetched items without merging from old state values
        reset?: boolean;
        // Used to allow fetch again
        resetFetched?: boolean;
      }>,
    ) {
      const { data, reset, resetFetched } = action.payload;
      const fetchedData = data;
      const feedData = (
        reset ? fetchedData : [...state.homeData, ...fetchedData]
      ) as IJobData[] | IJobSeekerProfile[];
      return {
        ...state,
        homeData: feedData,
        isFetched: resetFetched ? false : feedData.length > 0,
      };
    },
    filterHomeData(state, action: PayloadAction<string>) {
      const updatedItems = state.homeData.filter(
        item => item.id !== action.payload,
      );
      return {
        ...state,
        homeData: updatedItems as IJobData[] | IJobSeekerProfile[],
      };
    },
    setMatchingModalData(
      state,
      action: PayloadAction<{
        data: IRequestResponse[] | null;
        isMatchingModalVisible: boolean;
      }>,
    ) {
      return {
        ...state,
        isMatchingModalVisible: action.payload.isMatchingModalVisible,
        matchingModalData: action.payload.data ?? [],
      };
    },
    setNeedsHomeRefresh(state, action: PayloadAction<boolean>) {
      return {
        ...state,
        needsHomeRefresh: action.payload,
      };
    },
    addLeftSwipedItem(state, action: PayloadAction<string>) {
      const swipedItem = state.homeData.find(
        item => item.id === action.payload,
      );
      if (!swipedItem) {
        return state;
      }
      return {
        ...state,
        leftSwipedItems: [swipedItem, ...state.leftSwipedItems] as
          | IJobData[]
          | IJobSeekerProfile[],
      };
    },
    undoLeftSwipedItem(state) {
      const [lastLeftSwipedItem, ...remainingLeftSwipedItems] =
        state.leftSwipedItems;
      return {
        ...state,
        leftSwipedItems: remainingLeftSwipedItems as
          | IJobData[]
          | IJobSeekerProfile[],
        homeData: lastLeftSwipedItem
          ? ([lastLeftSwipedItem, ...state.homeData] as
              | IJobData[]
              | IJobSeekerProfile[])
          : state.homeData,
      };
    },
    setUndoAllowed(state, action: PayloadAction<boolean>) {
      return {
        ...state,
        isUndoAllowed: action.payload,
      };
    },
    /**
     * Reset left swiped items
     */
    resetLeftSwipedItems(state) {
      return {
        ...state,
        leftSwipedItems: [],
      };
    },
  },
});

export const {
  setHomeData,
  filterHomeData,
  setMatchingModalData,
  setNeedsHomeRefresh,
  addLeftSwipedItem,
  setUndoAllowed,
  undoLeftSwipedItem,
  resetLeftSwipedItems,
} = homeSlice.actions;
const homeReducer = homeSlice.reducer;

export default homeReducer;
