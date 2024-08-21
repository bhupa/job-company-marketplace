import React from 'react';

// Components
import { InputLabel, VideoUpload } from '@components/common';

interface Props {
  label?: string;
  videoUrl?: string | null;
  setVideoFile: (
    file: File | null,
    video: string | null,
    isDeleted: boolean,
  ) => void;
}

const SelfIntroductionVideo = ({ label, videoUrl, setVideoFile }: Props) => {
  return (
    <div className="flex flex-col gap-1">
      {label && <InputLabel label={label} />}
      <VideoUpload
        video={videoUrl}
        onVideoSelect={(video, file, isDeleted) =>
          setVideoFile(file, video, isDeleted)
        }
      />
    </div>
  );
};

export default SelfIntroductionVideo;
