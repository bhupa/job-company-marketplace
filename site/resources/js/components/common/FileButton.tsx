import React from 'react';

// Components
import Title from './Title';

// Others
import { Attachment, Delete, Download } from '@assets/icons';

interface Props {
  fileName: string;
  url: string;
  hideDeleteButton?: boolean;
  onDeletePressed?: () => void;
}

const FileButton = ({
  fileName,
  url,
  hideDeleteButton,
  onDeletePressed,
}: Props) => {
  const handleDownloadFile = async () => {
    try {
      window.open(url, '_blank');
    } catch (e) {
      console.log('error in file open', e);
    }
  };

  return (
    <div className="flex items-center gap-2">
      <div className="flex items-center self-start border border-WHITE_E0E2E4 rounded-md px-4 gap-6">
        <Attachment className={'text-BLUE_004D80'} />
        <Title type="body1">{fileName}</Title>
        <button onClick={() => handleDownloadFile()} className="p-2">
          <Download className={'text-BLUE_004D80'} />
        </button>
      </div>
      {!hideDeleteButton && (
        <button
          onClick={() => onDeletePressed && onDeletePressed()}
          className="p-2">
          <Delete className={'text-RED_FF4D4D'} />
        </button>
      )}
    </div>
  );
};

export default FileButton;
