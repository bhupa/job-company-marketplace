import React, { useState } from 'react';

import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';

// Components
import {
  CustomButton,
  HeaderWithBackButton,
  RadioButton,
} from '@components/common';
import { AuthProfileWrapper } from '@templates';

// Hooks
import useJobSeekerProfileInput from '@customHooks/useJobSeekerProfileInput';

// Others
import { GENDER_DATA } from '@constants/dropdownData';

const Gender = () => {
  const navigate = useNavigate();
  const { t, i18n } = useTranslation(['jobseeker', 'messages']);

  const isJapanese = i18n.language === 'ja';
  const languageKey = isJapanese ? 'label_ja' : 'label';

  const { handleSetProfileData, profileInput } = useJobSeekerProfileInput();

  const [selectedItem, setSelectedItem] = useState<string>(profileInput.gender);

  const handleNext = () => {
    handleSetProfileData({
      gender: selectedItem,
    });
    navigate('/continue');
  };

  return (
    <AuthProfileWrapper>
      <div className="flex flex-col gap-4 mt-4 max-w-md w-full">
        <HeaderWithBackButton title={t('gender')} hasBackButton={false} />
        {GENDER_DATA.map(item => {
          return (
            <RadioButton
              key={item.value}
              title={item?.[languageKey] ?? item.label}
              isSelected={selectedItem === item.value}
              onClick={() => setSelectedItem(item.value)}
            />
          );
        })}
        <CustomButton
          title={t('next', { ns: 'common' })}
          onClick={() => handleNext()}
        />
      </div>
    </AuthProfileWrapper>
  );
};

export default Gender;
