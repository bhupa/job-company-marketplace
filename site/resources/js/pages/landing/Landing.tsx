import React from 'react';

import { AppLogo } from '@components/common';
import { AuthWrapper } from '@templates';

const Landing = () => {
  return (
    <AuthWrapper>
      <AppLogo type="secondary" />
    </AuthWrapper>
  );
};

export default Landing;
