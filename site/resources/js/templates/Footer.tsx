import React from 'react';

import Link from 'antd/es/typography/Link';
import { useTranslation } from 'react-i18next';

// Components
import { Title } from '@components/common';

const Footer = () => {
  const { t } = useTranslation(['profile']);
  return (
    <footer className="sm:sticky sm:bottom-0 px-6 py-4 flex flex-col items-center justify-center gap-2 bg-white shadow-md border-t">
      <div className="flex flex-wrap gap-x-3 gap-y-1 sm:gap-4 items-center justify-center">
        <Link href="/terms-conditions">{t('termsOfService')}</Link>
        <Link href="/privacy-policy">{t('privacyPolicy')}</Link>
        <Link href="/account-deletion-guide">
          {t('accountDeletionGuide', { ns: 'auth' })}
        </Link>
      </div>
      <Title type="caption2" bold>
        &copy; Copyright 2024
      </Title>
    </footer>
  );
};

export default Footer;
