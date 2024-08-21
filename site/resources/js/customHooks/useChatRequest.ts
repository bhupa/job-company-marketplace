import { useEffect } from 'react';

import { useTranslation } from 'react-i18next';

// Components
import { useShowMessage } from './useShowMessage';

// Redux
import { useAppDispatch } from '@redux/hook';
import {
  IConfirmMatchingRequestParams,
  useConfirmMatchingRequestMutation,
  useSendChatRequestMutation,
} from '@redux/services/matchingApi';
import { setMatchingLimit } from '@redux/reducers/subscription';

const useChatRequest = () => {
  const { t } = useTranslation('messages');
  const { showSuccess } = useShowMessage();

  const dispatch = useAppDispatch();

  const [sendChatRequest, { isLoading, isSuccess, data, isError, error }] =
    useSendChatRequestMutation();

  useEffect(() => {
    if (isSuccess && data?.data) {
      dispatch(
        setMatchingLimit({
          chatRequestCount: data?.data?.chatRequest,
        }),
      );
      showSuccess(t('request.sendSuccess'));
    }
  }, [isSuccess]);

  const [
    matchRequest,
    { isLoading: isMatchingLoading, isSuccess: isMatchingSuccess },
  ] = useConfirmMatchingRequestMutation();

  const handleMatchRequest = (requestId: string, type: 'accept' | 'refuse') => {
    const acceptParams: IConfirmMatchingRequestParams = {
      requestId,
      type,
    };
    matchRequest(acceptParams);
  };

  return {
    sendChatRequest,
    handleMatchRequest,
    isLoading,
    isSuccess,
    data,
    isMatchingLoading,
    isMatchingSuccess,
    isError,
    error: error as any,
  };
};

export default useChatRequest;
