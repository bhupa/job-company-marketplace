export interface IDropdownItem {
  label: string;
  value: string;
  label_ja?: string;
}

export interface ISectionDropdownItem extends IDropdownItem {
  sections?: IDropdownItem[];
}

const EXPERIENCE_DATA: IDropdownItem[] = [
  { label: 'None', label_ja: '希望なし', value: '0' },
  { label: 'Less than 1 year', label_ja: '1年未満', value: '1' },
  { label: 'Less than 2 years', label_ja: '2年未満', value: '2' },
  { label: 'Less than 3 years', label_ja: '3年未満', value: '3' },
  { label: '3 years or more', label_ja: '3年以上', value: '4' },
];

const JAPANESE_LEVEL: IDropdownItem[] = [
  { label: 'None', label_ja: '希望なし', value: '0' },
  { label: 'N1', label_ja: 'N1', value: '1' },
  { label: 'N2', label_ja: 'N2', value: '2' },
  { label: 'N3', label_ja: 'N3', value: '3' },
  { label: 'N4', label_ja: 'N4', value: '4' },
  { label: 'N5', label_ja: 'N5', value: '5' },
];

const GENDER_DATA: IDropdownItem[] = [
  { label: 'Male', label_ja: '男性', value: '1' },
  { label: 'Female', label_ja: '女性', value: '2' },
  // { label: 'Other', label_ja: '他の', value: '3' },
];

const ANY_GENDER: IDropdownItem[] = [
  { label: 'Any', label_ja: '希望なし	', value: '4' },
];

// !!IMPORTANT: ARRANGED IN ORDER DO NOT CHANGE RANDOMLY
// Job type = employment type
const JOB_TYPES: IDropdownItem[] = [
  { label: 'Part time', label_ja: 'アルバイト・パート', value: '1' },
  { label: 'Full time', label_ja: '正社員', value: '2' },
  { label: 'Contract employee', label_ja: '契約社員', value: '3' },
  { label: 'Internship', label_ja: 'インターンシップ', value: '4' },
  {
    label: 'SSW (Specified Skilled Worker)',
    label_ja: '特定技能',
    value: '5',
  },
  {
    label:
      'Skilled Worker (Engineer/Specialist in Humanities/International Services)',
    label_ja: '技術・人文知識・国際業務',
    value: '6',
  },
  { label: 'Outsourcing', label_ja: '業務委託', value: '8' },
  { label: 'Other', label_ja: 'その他', value: '7' },
];

const LANGUAGES: IDropdownItem[] = [
  { label: 'English', label_ja: 'English', value: 'en' },
  { label: '日本語', label_ja: '日本語', value: 'ja' },
];

const SALARY_PAY_TYPE: IDropdownItem[] = [
  {
    label: 'per hour',
    label_ja: '時給',
    value: 'hour',
  },
  {
    label: 'per day',
    label_ja: '日当',
    value: 'day',
  },
  {
    label: 'per month',
    label_ja: '月給',
    value: 'month',
  },
  {
    label: 'per year',
    label_ja: '年収',
    value: 'year',
  },
  {
    label: 'Outsourcing',
    label_ja: '業務委託',
    value: 'outsourcing',
  },
];

export {
  EXPERIENCE_DATA,
  JAPANESE_LEVEL,
  GENDER_DATA,
  JOB_TYPES,
  LANGUAGES,
  ANY_GENDER,
  SALARY_PAY_TYPE,
};
