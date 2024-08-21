import React from 'react';

import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

// Components
import { Title } from '@components/common';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';
import { useShowMessage } from '@customHooks/useShowMessage';

// Others
import {
  IConfirmMatchingRequestParams,
  IFavoriteMatchingParams,
  IRequestResponse,
} from '@redux/services/matchingApi';
import { AppLogo } from '@assets/images';
import { calculateAge } from '@utils/dateUtils';
import { CloseCircleWhite, LocationBold } from '@assets/icons';

interface Props {
  data: IRequestResponse;
  isFavoritesTab?: boolean;
  onRemoveFavorite?: (params: IFavoriteMatchingParams) => void;
  onConfirmRequest?: (params: IConfirmMatchingRequestParams) => void;
  isVisible: boolean;
}

const RequestCard = ({
  data,
  isFavoritesTab,
  onRemoveFavorite,
  onConfirmRequest,
  isVisible,
}: Props) => {
  const { t } = useTranslation(['messages']);
  const navigate = useNavigate();
  const { showWarning } = useShowMessage();

  const { isJobSeeker } = useUserProfile();
  const { company, jobseeker, job } = data;
  let image,
    name,
    age,
    location = '';

  // If jobseeker, need to show company details
  if (isJobSeeker) {
    if (!company) {
      return null;
    }
    const { companyName, address, logo } = company;
    name = companyName;
    location = address;
    image = logo;
  } else {
    if (!jobseeker) {
      return null;
    }
    const { firstName, country, birthday, profileImg } = jobseeker;
    name = `${firstName}`;
    location = country;
    age = calculateAge(birthday);
    image = profileImg;
  }

  const handleOpenDetail = () => {
    if (isJobSeeker) {
      if (!job?.[0]?.company) {
        return showWarning(t('job.noDetail'));
      }
      return navigate('/home/jobs/details', {
        state: {
          data: job[0],
          hideBookmarkButton: true,
        },
      });
    }
    navigate('/home/jobseeker/details', {
      state: {
        data: jobseeker,
        hideBookmarkButton: true,
      },
    });
  };

  const handleReject = () => {
    // For favorites (bookmark tab), remove from favorites
    if (isFavoritesTab) {
      const params: IFavoriteMatchingParams = { favourite: 0 };
      if (isJobSeeker) {
        params.job_id = job?.[0]?.id;
      } else {
        params.job_seeker_id = jobseeker?.id;
      }
      onRemoveFavorite && onRemoveFavorite(params);
      return;
    }
    // For request (notification tab), reject request
    const rejectParams: IConfirmMatchingRequestParams = {
      requestId: data.id,
      type: 'refuse',
    };
    onConfirmRequest && onConfirmRequest(rejectParams);
  };

  return (
    <button
      onClick={() => handleOpenDetail()}
      disabled={!isVisible}
      style={{
        backgroundImage: `url(${image ? image : AppLogo})`,
        backgroundSize: 'cover',
      }}
      className="hover:shadow-lg rounded-3xl object-cover py-4 px-5 flex flex-col justify-between bg-WHITE_E0E2E4 h-[250px] w-[168px]">
      {isVisible && (
        <>
          <div>
            <Title
              type="caption1"
              className={'text-white line-clamp-2 text-shadow-black text-left'}>
              {name}
              {age ? `, ${age}` : ''}
            </Title>
            {location && (
              <div className={'flex'}>
                <LocationBold width={16} height={16} />
                <Title
                  type="caption2"
                  className={'text-white text-shadow-black'}>
                  {location}
                </Title>
              </div>
            )}
          </div>
          <div className="flex w-full gap-4 justify-around items-center">
            <CloseCircleWhite
              onClick={e => {
                e.stopPropagation();
                handleReject();
              }}
            />
          </div>
        </>
      )}
    </button>
  );
};

export default RequestCard;
