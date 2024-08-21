import React, { useState } from 'react';

// Components

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Redux
import { IJobData } from '@redux/services/jobsApi';

// Others
import { IJobSeekerProfile } from '@redux/services/jobSeekerApi';

interface Props {
  data: IJobData | IJobSeekerProfile;
}

const SwiperCard = (props: Props) => {
  const { data } = props;
  return (
    <div className="flex flex-col h-full">
      <SwiperContainer key={data.id} {...props} item={data} />
    </div>
  );
};

/**
 * Image container for swiper
 * @param props
 * @returns
 */
const SwiperContainer = (
  props: Props & { item: IJobData | IJobSeekerProfile },
) => {
  const { item } = props;

  const { isJobSeeker } = useUserProfile();

  const [imageIndex, setImageIndex] = useState<number>(0);

  let totalImages = 0,
    imageUrl = '';

  if (isJobSeeker) {
    // Each job has 1 image
    totalImages = 1;
    imageUrl = (item as IJobData).jobImage;
  } else {
    const jobseekerItem = item as IJobSeekerProfile;
    const imageCount = jobseekerItem.image?.length ?? 0;
    // imageCount is count of images excluding profile image
    totalImages = imageCount + 1;
    imageUrl =
      imageIndex === 0
        ? jobseekerItem.profileImg
        : jobseekerItem.image?.[imageIndex - 1]?.image;
  }

  return (
    <div
      className="flex flex-col justify-center items-center absolute top-0 left-0 right-0 h-[calc(100%-80px)]"
      key={item.id}>
      <div
        className="rounded-[48px] relative w-full h-full"
        onClick={() => {
          if (imageIndex < totalImages - 1) {
            setImageIndex(imageIndex + 1);
          } else {
            setImageIndex(0);
          }
        }}>
        <img
          src={imageUrl}
          className={'object-cover bg-white rounded-3xl w-full h-full'}
          alt={item.id + 'image'}
          loading="lazy"
        />
        {totalImages > 1 && (
          <div className="absolute top-2 flex w-full justify-between mt-2 px-2 h-40">
            {Array(totalImages)
              .fill(0)
              .map((_, index) => {
                return (
                  <div
                    key={index.toString()}
                    style={{
                      height: 4,
                      width: `${(100 - totalImages) / totalImages}%`,
                      borderRadius: 20,
                    }}
                    className={`${imageIndex === index ? 'bg-WHITE_E0E2E4' : 'bg-GRAY_545454'}`}
                  />
                );
              })}
          </div>
        )}
      </div>
    </div>
  );
};

export default SwiperCard;
