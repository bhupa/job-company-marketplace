import i18n from '../lang';

/**
 * Get date-time in future after adding given seconds to current time
 * @returns future date
 */
const getDateInFuture = (timeInSec: number) => {
  const currentDate = new Date();
  return new Date(currentDate.getTime() + timeInSec * 1000);
};

/**
 * Get time difference
 * @param {*} endDate
 * @returns difference between dates in seconds
 */
const getTimeDiff = (endDate: Date) => {
  const startDate = new Date();
  const remainingSeconds = Number(
    (endDate.getTime() - startDate.getTime()) / 1000,
  );
  return remainingSeconds;
};

/**
 * Calculate minutes and seconds value
 * @param {*} timeInSec
 * @returns time in min:sec format
 */
const getMinSecValue = (timeInSec: number) => {
  const minutes = Math.floor(timeInSec / 60);
  const seconds = Math.floor(timeInSec % 60);
  return (
    minutes.toString().padStart(2, '0') +
    ':' +
    seconds.toString().padStart(2, '0')
  );
};

/**
 * Get date in YYYY-MM-DD format
 * @param date
 * @param separator
 * @returns
 */
const getFormatDateInYYYYMMDD = (date: Date, separator: string = '-') => {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}${separator}${month}${separator}${day}`;
};

/**
 * Get date in May 20, 2023 format
 * @param date
 * @returns string
 */
const getDateInYearMonthDateFormat = (date: Date | string) => {
  const userLanguage = i18n.language === 'ja' ? 'ja-JP' : 'en-US';
  const inputDate = new Date(date);
  const formattedDate = inputDate.toLocaleDateString(userLanguage, {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
  return formattedDate;
};

const calculateAge = (dateString: Date | string): number => {
  const birthdate = new Date(dateString);
  const currentDate = new Date();

  const ageInMilliseconds: number = currentDate.getTime() - birthdate.getTime();
  const ageInYears: number = ageInMilliseconds / (365 * 24 * 60 * 60 * 1000);

  return Math.floor(ageInYears);
};

/**
 * Get date in 10:20 AM, Nov 8 format
 * @param date
 * @returns string
 */
const getDateInTimeMonthDayFormat = (date: Date | string) => {
  const userLanguage = i18n.language === 'ja' ? 'ja-JP' : 'en-US';
  const inputDate = new Date(date);
  const formattedDate = inputDate.toLocaleDateString(userLanguage, {
    month: 'short',
    day: 'numeric',
    hour12: true,
    hour: '2-digit',
    minute: '2-digit',
  });
  return `${formattedDate}`;
};

/**
 * Get date in 10:20 AM, Nov 8 format
 * @param date
 * @returns string
 */
const getTimeInHoursMinutes = (date: Date | string) => {
  const userLanguage = i18n.language === 'ja' ? 'ja-JP' : 'en-US';
  const inputDate = new Date(date);
  const formattedDate = inputDate.toLocaleTimeString(userLanguage, {
    hour12: true,
    hour: '2-digit',
    minute: '2-digit',
  });
  return `${formattedDate}`;
};

export {
  getDateInFuture,
  getTimeDiff,
  getMinSecValue,
  getFormatDateInYYYYMMDD,
  getDateInYearMonthDateFormat,
  calculateAge,
  getDateInTimeMonthDayFormat,
  getTimeInHoursMinutes,
};
