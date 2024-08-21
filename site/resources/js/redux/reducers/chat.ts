import { createSlice, PayloadAction } from '@reduxjs/toolkit';
import { IChatItem, IChatListResponse } from '@redux/services/chatApi';

const initialState = {
  chatList: [] as IChatListResponse[],
  chatMessages: {} as { [key: string]: IChatItem[] },
  needsRefresh: false,
  needsRefreshChatMessages: false,
  tabUnseenCount: 0,
  lastSeenMessageTime: {} as { [key: string]: string | null },
  isChatPolicyAccepted: false,
};

const chatSlice = createSlice({
  name: 'chat',
  initialState: initialState,
  reducers: {
    setChatList(state, action: PayloadAction<IChatListResponse[]>) {
      return {
        ...state,
        chatList: action.payload,
      };
    },
    /**
     * Update chat count
     * If count is provided or count = 0, then count will be set to 0.
     * otherwise count will be added to initial count value.
     */
    updateUnseenCount(
      state,
      action: PayloadAction<{ chatRoomId: string; count?: number }>,
    ) {
      const { chatRoomId, count = 0 } = action.payload;
      const updatedChatList = state.chatList.map(chatItem =>
        chatItem.id === chatRoomId
          ? { ...chatItem, unseen: count ? chatItem.unseen + count : 0 }
          : chatItem,
      );
      return {
        ...state,
        chatList: updatedChatList,
      };
    },
    /**
     * Add and show latest message in chat list item
     */
    updateLastMessage(
      state,
      action: PayloadAction<{ chatRoomId: string; chat: IChatItem }>,
    ) {
      const { chatRoomId, chat } = action.payload;
      const indexToUpdate = state.chatList.findIndex(
        chatItem => chatItem.id === chatRoomId,
      );
      let updatedChatList = state.chatList;
      if (indexToUpdate !== -1) {
        const updatedItem = {
          ...state.chatList[indexToUpdate],
          chats: chat,
        };
        updatedChatList = [
          updatedItem,
          ...state.chatList.slice(0, indexToUpdate),
          ...state.chatList.slice(indexToUpdate + 1),
        ];
      }
      return {
        ...state,
        chatList: updatedChatList,
      };
    },
    setChatMessages(
      state,
      action: PayloadAction<{ roomId: string; messages: IChatItem[] }>,
    ) {
      const { roomId, messages } = action.payload;
      return {
        ...state,
        chatMessages: {
          ...state.chatMessages,
          [roomId]: messages,
        },
      };
    },
    addSingleMessage(
      state,
      action: PayloadAction<{ roomId: string; message: IChatItem }>,
    ) {
      const { roomId, message } = action.payload;
      if (!state.chatMessages?.[roomId]) {
        return {
          ...state,
          chatMessages: {
            ...state.chatMessages,
            [roomId]: [message],
          },
        };
      }
      return {
        ...state,
        chatMessages: {
          ...state.chatMessages,
          [roomId]: [message, ...state.chatMessages[roomId]],
        },
      };
    },
    deleteMessage(state, action: PayloadAction<{ chatRoomId: string }>) {
      const { chatRoomId } = action.payload;
      const updatedItems = state.chatList.filter(
        item => item.id !== chatRoomId,
      );
      return {
        ...state,
        chatList: updatedItems,
      };
    },
    setNeedsChatRefresh(state, action: PayloadAction<boolean>) {
      state.needsRefresh = action.payload;
    },
    setNeedsChatMessageRefresh(state, action: PayloadAction<boolean>) {
      state.needsRefreshChatMessages = action.payload;
    },
    setChatUnseenCountFromEvent(state, action: PayloadAction<number>) {
      state.tabUnseenCount = action.payload;
    },
    setLastSeenMessageTime(
      state,
      action: PayloadAction<{
        chatRoomId: string;
        lastSeenTime: string | null;
      }>,
    ) {
      const { chatRoomId, lastSeenTime } = action.payload;
      const updatedValue = {
        ...state.lastSeenMessageTime,
        [chatRoomId]: lastSeenTime,
      };
      return {
        ...state,
        lastSeenMessageTime: JSON.parse(JSON.stringify(updatedValue)),
      };
    },
    setIsChatPolicyAccepted(state, action: PayloadAction<boolean>) {
      return {
        ...state,
        isChatPolicyAccepted: action.payload,
      };
    },
  },
});

export const {
  setChatList,
  updateUnseenCount,
  updateLastMessage,
  setChatMessages,
  addSingleMessage,
  deleteMessage,
  setNeedsChatRefresh,
  setChatUnseenCountFromEvent,
  setNeedsChatMessageRefresh,
  setLastSeenMessageTime,
  setIsChatPolicyAccepted,
} = chatSlice.actions;
const chatReducer = chatSlice.reducer;

export default chatReducer;
