// Count for OTP input box
const CELL_COUNT = 6;

// Timer value for OTP in seconds
const TIME_OUT = 180; // 3 minutes

const MIN_SALARY = 0;
const MAX_SALARY = 100000000;

const MIN_AGE = 18;
const MAX_AGE = 70;

const MAX_UPLOAD_IMAGES = 6; // jobseeker
const MAX_ACCOUNT_VERIFICATION_IMAGES = 3;
const MAX_COMPANY_IMAGES = 1; // company

const FIREBASE_TOKEN = 'firebaseToken';

const MAX_VISIBLE_REQUESTS = 10;

// Per page value for home screen data
const HOME_PER_PAGE_COUNT = 5;

const SWIPE_CARD_HEIGHT = 440;
const SWIPE_CARD_WIDTH = 380;

const MAX_TAGS_COUNT = 3;

const PROFILE_COMPLETION_PERCENT = 70;

// Max video length is 2 minutes = 120 seconds
const MAX_VIDEO_LENGTH = 120;
const MAX_VIDEO_SIZE = 26214400; // 25 MB

const JOBSEEKER_TAGS = [{ key: 'isLivingInJapan', label: 'livingInJapan' }];

export {
  CELL_COUNT,
  TIME_OUT,
  MAX_SALARY,
  MIN_SALARY,
  MIN_AGE,
  MAX_AGE,
  MAX_UPLOAD_IMAGES,
  FIREBASE_TOKEN,
  MAX_VISIBLE_REQUESTS,
  MAX_ACCOUNT_VERIFICATION_IMAGES,
  HOME_PER_PAGE_COUNT,
  SWIPE_CARD_HEIGHT,
  SWIPE_CARD_WIDTH,
  MAX_TAGS_COUNT,
  PROFILE_COMPLETION_PERCENT,
  JOBSEEKER_TAGS,
  MAX_COMPANY_IMAGES,
  MAX_VIDEO_LENGTH,
  MAX_VIDEO_SIZE,
};
