import React, { useState } from 'react';

import 'react-phone-number-input/style.css';

// Components
import { AuthWrapper } from '@templates';
import { OTPScreen, RegisterForm } from './forms';

const Signup = () => {
  const [step, setStep] = useState<number>(1);
  const [confirmationObj, setConfirmationObj] = useState<any>();
  const [phoneNumber, setPhoneNumber] = useState<string>('');

  const renderComponent = () => {
    switch (step) {
      case 1:
        return (
          <RegisterForm
            setConfirmationObj={(confirmObj, phone) => {
              setConfirmationObj(confirmObj);
              setPhoneNumber(phone);
            }}
            goToNextStep={() => setStep(2)}
          />
        );
      case 2:
        return (
          <OTPScreen
            type="signup"
            phoneNumber={phoneNumber}
            confirmation={confirmationObj}
          />
        );
      default:
        return null;
    }
  };

  return (
    <AuthWrapper>
      <div className="flex flex-col gap-2 bg-white shadow-lg border-GRAY_ACACAC rounded-md p-4 sm:w-3/4 md:w-1/2 lg:w-2/5">
        {renderComponent()}
      </div>
    </AuthWrapper>
  );
};

export default Signup;
