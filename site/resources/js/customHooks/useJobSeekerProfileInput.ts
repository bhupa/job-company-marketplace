import {
  IJobSeekerProfileInputData,
  setJobSeekerProfileInputData,
} from '@redux/reducers/jobSeeker';
import { useAppDispatch, useAppSelector } from '@redux/hook';
import { useCreateJobSeekerProfileMutation } from '@redux/services/jobSeekerApi';

// Utils

const useJobSeekerProfileInput = () => {
  const dispatch = useAppDispatch();
  const [createProfile, { isLoading, isSuccess, data }] =
    useCreateJobSeekerProfileMutation();

  const { profileInput } = useAppSelector(state => state.jobSeeker);

  // Save input data to redux store
  const handleSetProfileData = (values: IJobSeekerProfileInputData) => {
    dispatch(
      setJobSeekerProfileInputData({
        ...values,
      }),
    );
  };

  // Create user profile
  const handleCreateProfile = async () => {
    const formData = new FormData();
    const {
      firstName,
      lastName,
      birthday,
      gender,
      country,
      currentCountry,
      occupation,
      experience,
      japaneseLevel,
      about,
      aboutJa,
      image,
      profileImg,
      isLivingInJapan,
      jobType,
      tags,
      startDate,
      introVideo,
    } = profileInput;
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('birthday', birthday);
    formData.append('gender', gender);
    formData.append('start_when', startDate);
    if (introVideo) {
      formData.append('intro_video', introVideo);
    }
    if (profileImg) {
      formData.append('profile_img', profileImg);
    }
    image?.forEach(imageItem => {
      formData.append('image[]', imageItem);
    });
    if (tags) {
      tags.forEach(keyValue => {
        formData.append('tags[]', keyValue);
      });
    }
    formData.append('living_japan', isLivingInJapan ? '1' : '0');
    if (country) {
      formData.append('country', country);
    }
    if (currentCountry) {
      formData.append('current_country', currentCountry);
    }
    if (occupation) {
      formData.append('occupation', occupation);
    }
    if (experience) {
      formData.append('experience', experience);
    }
    if (japaneseLevel) {
      formData.append('japanese_level', japaneseLevel);
    }
    if (about) {
      formData.append('about', about);
    }
    if (aboutJa) {
      formData.append('about_ja', aboutJa);
    }
    if (jobType) {
      formData.append('job_type', jobType);
    }
    createProfile(formData);
  };

  return {
    handleCreateProfile,
    handleSetProfileData,
    isLoading,
    isSuccess,
    data,
    profileInput,
  };
};

export default useJobSeekerProfileInput;
