import React from 'react';

import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import Button from '@components/common/CustomButton';
import LanguagePopper from '@components/LanguagePopper';
import { AppLogo, AppNameIcon } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Others
import { LogoutSvg } from '@assets/icons/settingsIcons';

const Header = () => {
  const navigate = useNavigate();
  const { t } = useTranslation(['auth']);

  const { accessToken, handleLogout } = useUserProfile();

  return (
    <nav className="sticky top-0 px-6 py-2 h-[64px] flex items-center justify-between bg-white shadow-md border-b-1 border-b-GRAY_ACACAC z-[1000]">
      <button
        aria-label="homepage"
        className="flex gap-2 items-center"
        onClick={() => (window.location.href = '/')}>
        <AppLogo small />
        <AppNameIcon small />
      </button>
      <div className="flex gap-2 md:gap-3 items-center">
        <LanguagePopper />
        {accessToken ? (
          <LogoutSvg
            width={24}
            height={24}
            className="cursor-pointer"
            onClick={() => handleLogout()}
          />
        ) : (
          <>
            <Button
              title={t('signIn')}
              type="link"
              onClick={() => navigate('/login')}
            />
            <Button title={t('signUp')} onClick={() => navigate('/signup')} />
          </>
        )}
      </div>
    </nav>
  );
};

export default Header;
