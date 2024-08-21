import React from 'react';

// Components
import { Title } from '@components/common';

interface Props {
  title: string;
  icon?: any;
}
const TagLabel = ({ title, icon }: Props) => {
  const Icon = icon;
  return (
    <div
      className={
        'flex items-center gap-1 self-start px-2 py-1 rounded-lg bg-BLUE_004D801A'
      }>
      {icon && <Icon width={14} height={14} className={'text-BLUE_004D80'} />}
      <Title type="caption2" className={'text-BLACK_1E2022'}>
        {title}
      </Title>
    </div>
  );
};

export default TagLabel;
