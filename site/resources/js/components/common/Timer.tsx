import React, { FC, useEffect, useState } from 'react';

// Components
import Title from './Title';

// Utils
import { TIME_OUT } from '@utils/constants';
import { getDateInFuture, getMinSecValue, getTimeDiff } from '@utils/dateUtils';

interface TimerProps {
  resetTimer: boolean;
  setTimerReset: React.Dispatch<React.SetStateAction<boolean>>;
}

const Timer: FC<TimerProps> = props => {
  const { resetTimer, setTimerReset } = props;

  const [timer, setTimer] = useState<number>(TIME_OUT - 1);
  const [expireDate, setExpireDate] = useState<Date>(getDateInFuture(TIME_OUT));

  useEffect(() => {
    if (resetTimer) {
      setTimerReset(false);
      setTimer(TIME_OUT - 1);
      setExpireDate(getDateInFuture(TIME_OUT));
    }
  }, [resetTimer, setTimerReset]);

  useEffect(() => {
    let timeOut: any = null;
    if (timer > 0) {
      timeOut = setTimeout(() => {
        setTimer(getTimeDiff(expireDate));
      }, 1000);
    }
    if (timer < 1) {
      clearTimeout(timeOut);
    }
    return () => {
      clearTimeout(timeOut);
    };
  });

  return (
    <Title type="heading1" className="text-center text-4xl" bold>
      {getMinSecValue(timer)}
    </Title>
  );
};

export default React.memo(Timer);
