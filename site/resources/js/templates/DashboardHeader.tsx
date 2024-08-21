import React from 'react';

// Components
import LanguagePopper from '@components/LanguagePopper';
import SettingsPopover from '@components/SettingsPopover';
import { AppLogo, AppNameIcon } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Others
import { LogoutSvg } from '@assets/icons/settingsIcons';

const DashboardHeader = () => {
  const { handleLogout, isProfileComplete } = useUserProfile();

  return (
    <nav className="sticky top-0 px-6 py-2 h-[64px] flex items-center justify-between bg-white shadow-md border-b-1 border-b-GRAY_ACACAC z-[1000]">
      <button
        aria-label="homepage"
        className="flex gap-2 items-center"
        onClick={() => (window.location.href = '/home')}>
        <AppLogo small />
        <AppNameIcon small />
      </button>
      <div className="flex gap-4 items-center">
        <LanguagePopper />
        {isProfileComplete && <SettingsPopover />}
        <LogoutSvg
          width={24}
          height={24}
          className="cursor-pointer"
          onClick={() => handleLogout()}
        />
      </div>
    </nav>
  );
};

export default DashboardHeader;
