import React from 'react';

import { useTranslation } from 'react-i18next';

// Components
import { Title } from '@components/common';

// Others
import { Audio, CheckMark } from '@assets/icons';
import { IStripePlan } from '@redux/services/subscriptionApi';

interface Props {
  isSelected?: boolean;
  data: IStripePlan;
  onPress: () => void;
}

const PlanCard = (props: Props) => {
  const {
    isSelected,
    onPress,
    data: { name, price, features, timePeriod },
  } = props;

  const { i18n } = useTranslation();
  const languageKey = i18n.language;
  const priceValue = price?.[languageKey];

  const AudioFile = Audio;

  return (
    <div
      className={`rounded-xl cursor-pointer border py-5 px-6 flex flex-col justify-between gap-8 min-w-[240px] max-w-[240px] hover:shadow-md ${isSelected ? 'bg-white border-BLUE_004D80' : 'bg-WHITE_F6F6F6 border-WHITE_F6F6F6'}`}
      onClick={() => onPress && onPress()}>
      <div className="flex flex-col gap-5">
        <AudioFile
          width={40}
          height={40}
          className={`${isSelected ? 'text-BLUE_004D80' : 'text-GRAY_5E5E5E'} self-center`}
        />
        <Title type="heading1" className="text-center break-words">
          {name?.[languageKey] ?? ''}
        </Title>
        <div className="flex flex-col gap-2">
          {features?.[languageKey]?.map((feature, index) => {
            return (
              <div key={index.toString()} className="flex gap-2">
                <CheckMark className="text-GRAY_807C83" />
                <div className="flex-1 break-all">
                  <Title
                    type="body2"
                    className={'text-GRAY_807C83 break-words'}>
                    {feature}
                  </Title>
                </div>
              </div>
            );
          })}
        </div>
      </div>
      <div className="flex justify-center">
        <Title type="heading2">
          {priceValue?.symbol}
          {priceValue?.price ?? 0}
        </Title>
        <Title type="body2">
          &nbsp;/&nbsp;{timePeriod?.[languageKey] ?? ''}
        </Title>
      </div>
    </div>
  );
};

export default PlanCard;
