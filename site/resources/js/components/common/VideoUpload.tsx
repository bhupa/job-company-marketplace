import React, { useEffect, useState } from 'react';

import { useTranslation } from 'react-i18next';

// Hooks
import { useShowMessage } from '@customHooks/useShowMessage';

// Others
import { AddCircle, Close, Edit } from '@assets/icons';
import { MAX_VIDEO_LENGTH, MAX_VIDEO_SIZE } from '@utils/constants';

interface Props {
  hidden?: boolean;
  uploadRef?: any;
  video?: string | null;
  onVideoSelect: (
    video: string | null,
    file: File | null,
    isDeleted: boolean,
  ) => void;
}

const VideoUpload = ({
  onVideoSelect,
  video: initialVideo,
  uploadRef,
  hidden,
}: Props) => {
  const { t } = useTranslation(['messages']);
  const { showError } = useShowMessage();
  const [selectedVideoUrl, setSelectedVideoUrl] = useState<
    string | null | undefined
  >(initialVideo || null);

  useEffect(() => {
    setSelectedVideoUrl(initialVideo);
  }, [initialVideo]);

  const handleVideoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0] ?? null;
    if (file) {
      if (file.size > MAX_VIDEO_SIZE) {
        return showError(t('introductionVideo.size'));
      }
      const video = document.createElement('video');
      video.preload = 'metadata';

      const videoUrl = URL.createObjectURL(file);
      video.onloadedmetadata = () => {
        if (video.duration <= MAX_VIDEO_LENGTH) {
          setSelectedVideoUrl(videoUrl);
          file && onVideoSelect(videoUrl, file, false);
        } else {
          showError(t('introductionVideo.duration'));
        }
      };

      video.src = videoUrl;
    }
  };

  const handleRemoveVideo = () => {
    setSelectedVideoUrl(null);
    onVideoSelect(null, null, true);
  };

  return (
    <div
      className={`${hidden ? 'hidden' : 'flex'} relative justify-center items-center w-[320px] h-[320px] border-dotted rounded-md border border-GRAY_A6A6A6`}>
      <input
        ref={uploadRef}
        type="file"
        accept="video/*"
        className="absolute w-full h-full opacity-0 cursor-pointer"
        onChange={handleVideoChange}
      />
      <div className="flex justify-center items-center w-full h-full ">
        {selectedVideoUrl ? (
          <video
            src={selectedVideoUrl}
            width={400}
            height={400}
            controls
            className="aspect-video"
          />
        ) : (
          <>
            <AddCircle className="text-BLUE_004D80" />
          </>
        )}
      </div>
      <div
        className="absolute cursor-pointer -bottom-1 -right-2 bg-RED_FF4D4D rounded-full h-7 w-7 flex justify-center items-center"
        onClick={() => selectedVideoUrl && handleRemoveVideo()}>
        {selectedVideoUrl ? (
          <Close className={'text-white'} />
        ) : (
          <Edit className={'text-white'} width={14} />
        )}
      </div>
    </div>
  );
};

export default VideoUpload;
