import React, { useState } from 'react';

// Components
import {
  PreferenceOptions,
  SelectionPreference,
  WhatYouWantToHire,
  WorkInformation,
} from './forms';
import { AuthProfileWrapper, DashboardWrapper } from '@templates';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

const CreateJob = () => {
  const { isProfileComplete } = useUserProfile();

  const [step, setStep] = useState<number>(1);

  const AppLayout = isProfileComplete ? DashboardWrapper : AuthProfileWrapper;

  const renderComponent = () => {
    switch (step) {
      case 1:
        return <WorkInformation onNextPressed={() => setStep(2)} />;
      case 2:
        return (
          <SelectionPreference
            onNextPressed={() => setStep(3)}
            onBackPressed={() => setStep(1)}
          />
        );
      case 3:
        return (
          <PreferenceOptions
            onNextPressed={() => setStep(4)}
            onBackPressed={() => setStep(2)}
          />
        );
      case 4:
        return <WhatYouWantToHire onBackPressed={() => setStep(3)} />;
      default:
        return null;
    }
  };

  return (
    <AppLayout>
      <div className="flex flex-col gap-4 mb-4 w-full max-w-sm lg:max-w-xl mx-auto">
        {renderComponent()}
      </div>
    </AppLayout>
  );
};

export default CreateJob;
