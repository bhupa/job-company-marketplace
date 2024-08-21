import React, { createContext, useContext } from 'react';
import { message } from 'antd';

interface MessageContextType {
  showSuccess: (content: string) => void;
  showError: (content: string) => void;
  showWarning: (content: string) => void;
}

const MessageContext = createContext<MessageContextType | undefined>(undefined);

export const useShowMessage = (): MessageContextType => {
  const context = useContext(MessageContext);
  if (!context) {
    throw new Error('useMessage must be used within a MessageProvider');
  }
  return context;
};

export const MessageProvider: React.FC<{ children: React.ReactNode }> = ({
  children,
}) => {
  const [messageApi, contextHolder] = message.useMessage();

  const showSuccess = (content: string) => {
    messageApi.open({
      type: 'success',
      content: content,
    });
  };

  const showError = (content: string) => {
    messageApi.open({
      type: 'error',
      content: content,
    });
  };

  const showWarning = (content: string) => {
    messageApi.open({
      type: 'warning',
      content: content,
    });
  };

  const value: MessageContextType = {
    showSuccess,
    showError,
    showWarning,
  };

  return (
    <MessageContext.Provider value={value}>
      {contextHolder}
      {children}
    </MessageContext.Provider>
  );
};
