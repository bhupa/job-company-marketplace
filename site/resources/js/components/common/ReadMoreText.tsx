import React, { useState } from 'react';

import { useTranslation } from 'react-i18next';
import Paragraph from 'antd/es/typography/Paragraph';

// Components
import CustomButton from './CustomButton';

const ReadMoreText = ({ text, color }: { text: string; color?: string }) => {
  const { t } = useTranslation(['common']);
  const [isExpanded, setExpanded] = useState<boolean>(false);
  const [textKey, setTextKey] = useState<string>(Date.now().toString());

  const toggleEllipsis = () => {
    setExpanded(!isExpanded);
    if (isExpanded) {
      setTextKey(Date.now().toString());
    }
  };

  return (
    <Paragraph
      key={textKey}
      ellipsis={{
        rows: 2,
        expandable: true,
        symbol: t('readMore'),
        onExpand: () => toggleEllipsis(),
      }}
      className={`text-base leading-6 whitespace-break-spaces ${color ? color : 'text-black'}`}>
      {text}
      {isExpanded && (
        <CustomButton
          type="link"
          className="py-0 m-0 h-6"
          title={!isExpanded ? '' : t('seeLess')}
          onClick={() => toggleEllipsis()}
        />
      )}
    </Paragraph>
  );
};

export default ReadMoreText;
