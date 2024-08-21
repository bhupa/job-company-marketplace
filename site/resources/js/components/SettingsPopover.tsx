import React from 'react';

import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Popover, Menu, MenuProps, GetProp } from 'antd';

// Components
import { Title } from './common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Others
import {
  KeySvg,
  PersonSvg,
  TermsSvg,
  VerifyEmailSvg,
  PrivacyPolicySvg,
  AccountSettingsSvg,
  GeneralSettingsSvg,
  VerifiedSettingsSvg,
  AccountDeleteGuideSvg,
} from '@assets/icons/settingsIcons';
import { Settings } from '@assets/icons';

// Other Init
const iconProps = {
  width: 16,
  height: 16,
};

const iconClass = 'bg-gray-200 rounded-sm p-1 flex items-center justify-center';

const SettingsPopover = () => {
  return (
    <Popover
      trigger="click"
      placement="bottom"
      title={<PopoverModalHeader />}
      content={<PopoverModalContent />}
      arrow={{
        pointAtCenter: true,
      }}
      zIndex={9999}
      overlayInnerStyle={{
        marginRight: 14,
        padding: '8px 4px',
      }}>
      <span>
        <Settings width={20} height={20} className="cursor-pointer" />
      </span>
    </Popover>
  );
};

/**
 * Modal Header Title
 * @returns
 */
const PopoverModalHeader = () => {
  const { t } = useTranslation('profile');
  return (
    <Title type="body2" className="flex justify-center font-bold">
      {t('settings')}
    </Title>
  );
};

type MenuItem = GetProp<MenuProps, 'items'>[number];
/**
 * Modal Content
 * @returns
 */
const PopoverModalContent = () => {
  const { t } = useTranslation('profile');
  const navigate = useNavigate();
  const { isJobSeeker } = useUserProfile();

  const getMenuItem = (
    key: string,
    label: string,
    route: string,
    icon: any,
    params?: any,
  ): MenuItem => {
    return {
      label: (
        <div
          className="flex items-center gap-3 cursor-pointer"
          onClick={() => navigate(route, { state: { params: params } })}>
          <div className={iconClass}>{icon}</div>
          <span>{label}</span>
        </div>
      ),
      key,
    } as MenuItem;
  };

  const FULL_MENU_ITEMS = isJobSeeker
    ? JOBSEEKER_MENU_ITEMS
    : COMPANY_MENU_ITEMS;

  return (
    <Menu
      style={{ border: 'none' }}
      items={FULL_MENU_ITEMS.map(item =>
        getMenuItem(
          item.key,
          t(item.label),
          item.route,
          item.icon,
          item.params,
        ),
      )}
    />
  );
};

export default SettingsPopover;
interface IMenuItem {
  key: string;
  label: string;
  route: string;
  icon: any;
  params?: any;
}

const MENU_ITEMS: IMenuItem[] = [
  {
    key: 'password',
    label: 'changePassword',
    route: '/change-password',
    icon: <KeySvg {...iconProps} />,
  },
  {
    key: 'terms',
    label: 'termsOfService',
    route: '/terms-conditions',
    icon: <TermsSvg {...iconProps} />,
  },
  {
    key: 'privacy',
    label: 'privacyPolicy',
    route: '/privacy-policy',
    icon: <PrivacyPolicySvg {...iconProps} />,
  },
  {
    key: 'verifyAccount',
    label: 'verifyAccount',
    route: '/verify-account',
    icon: <VerifiedSettingsSvg {...iconProps} />,
  },
  {
    key: 'emailSettings',
    label: 'emailSettings',
    route: '/email-settings',
    icon: <VerifyEmailSvg {...iconProps} />,
  },
  {
    key: 'accountSettings',
    label: 'accountSettings',
    route: '/account-settings',
    icon: <AccountSettingsSvg {...iconProps} />,
  },
  {
    key: 'accountDeletionGuide',
    label: 'accountDeletionGuide',
    route: '/account-deletion-guide',
    icon: <AccountDeleteGuideSvg {...iconProps} />,
  },
];

const JOBSEEKER_MENU_ITEMS: IMenuItem[] = [
  {
    key: 'personal',
    label: 'personalSettings',
    route: '/profile/detail',
    icon: <PersonSvg {...iconProps} />,
  },
  ...MENU_ITEMS,
];

const COMPANY_MENU_ITEMS: IMenuItem[] = [
  {
    key: 'generalInfo',
    label: 'generalInfo',
    route: '/profile/detail',
    icon: <GeneralSettingsSvg {...iconProps} />,
    params: { isEdit: true },
  },
  ...MENU_ITEMS,
];
