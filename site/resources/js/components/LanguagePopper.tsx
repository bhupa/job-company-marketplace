import React, { useState } from 'react';
import { Popover } from 'antd';
import { useTranslation } from 'react-i18next';

// Components
import { CustomButton, Title } from './common';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Redux
import { useAppDispatch } from '@redux/hook';
import { setDeviceLanguage } from '@redux/reducers/auth';
import { useChangeLanguageMutation } from '@redux/services/authApi';

// Others
import { ArrowDown } from '@assets/icons';

const LanguagePopper = () => {
  const { t, i18n } = useTranslation(['profile', 'messages']);
  const dispatch = useAppDispatch();
  const { showSuccess, showWarning } = useShowMessage();

  const [changeLanguage] = useChangeLanguageMutation();

  const [isOpened, setOpened] = useState<boolean>(false);

  const handleSetAppLanguage = async (languageCode: 'en' | 'ja') => {
    localStorage.setItem('selectedLanguage', languageCode);
    await i18n.changeLanguage(languageCode);
    dispatch(setDeviceLanguage(languageCode));
    showSuccess(t('language.success', { ns: 'messages' }));
  };

  const handleChangeLanguage = (languageCode: 'en' | 'ja') => {
    if (i18n.language === languageCode) {
      return showWarning(t('nothingToUpdate', { ns: 'messages' }));
    }
    hide();
    changeLanguage(languageCode);
    handleSetAppLanguage(languageCode);
  };

  const hide = () => {
    setOpened(false);
  };

  const handleHoverChange = (open: boolean) => {
    setOpened(open);
  };

  return (
    <Popover
      zIndex={9999}
      content={
        <div className="flex flex-col">
          <CustomButton
            title="English"
            type="link"
            onClick={() => handleChangeLanguage('en')}
          />
          <CustomButton
            title="日本語"
            type="link"
            onClick={() => handleChangeLanguage('ja')}
          />
        </div>
      }
      trigger="click"
      placement="bottomLeft"
      open={isOpened}
      onOpenChange={handleHoverChange}
      className="flex gap-2 items-center cursor-pointer">
      <Title type="body2" className="text-xs sm:text-base">
        {i18n.language === 'ja' ? '日本語' : 'En'}
      </Title>
      <ArrowDown width={12} height={12} />
    </Popover>
  );
};

export default LanguagePopper;
