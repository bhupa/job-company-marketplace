import { ISectionDropdownItem } from '@constants/dropdownData';

/**
 * Converts value to boolean value
 * @param boolValue number
 * @returns boolean value
 */
const getBooleanData = (boolValue: number | boolean) => {
  return boolValue ? 'Yes' : 'No';
};

/**
 * Remove duplicates from array
 * @param temp initial array
 * @returns unique array
 */
const filterDuplicates = <T extends { id: string }>(temp: T[]) => {
  return Array.from(new Set(temp.map(value => value.id)))
    .map(uniqueId => temp.find(unique => unique.id === uniqueId))
    .filter((value): value is T => value !== undefined);
};

/**
 * Add comma separation to amount value
 * @param amount
 * @returns comma separated value
 */
const formatAmountWithCommas = (amount: string | number) => {
  return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
};

/**
 * Allow only 8 digits for salary
 * @param salary
 * @returns
 */
const validateSalary = (salary: string) => {
  const regex = /^$|^\d{0,8}(\.\d{0,2})?$/;
  if (regex.test(salary)) {
    return salary.replace(/^0+(?=\d)/, '');
  }
};

const getSectionSelectedItem = (
  value: string | null | undefined,
  data: ISectionDropdownItem[],
) => {
  if (!value) {
    return null;
  }
  for (let i = 0; i < data.length; i++) {
    if (data[i].value === value) {
      return data[i];
    } else if (data[i].sections) {
      const sectionData = data[i].sections ?? [];
      const sectionLength = sectionData.length;
      for (let j = 0; j < sectionLength; j++) {
        if (sectionData[j].value === value) {
          return sectionData[j];
        }
      }
    }
  }
  return null; // Return null if no matching value is found
};

const searchSectionItems = (
  searchKey: string,
  items: ISectionDropdownItem[],
  isJapanese: boolean,
) => {
  if (!searchKey) {
    return items;
  }
  let filteredItems: ISectionDropdownItem[] = [];
  for (let i = 0; i < items.length; i++) {
    const item = items[i];
    const labelText = !isJapanese ? item.label : item.label_ja || item.label;
    if (labelText.toLowerCase().includes(searchKey)) {
      filteredItems = [...filteredItems, item]; // Return the whole section
    } else if (item.sections) {
      const section = item.sections.find(sectionItem =>
        sectionItem.label.toLowerCase().includes(searchKey),
      );
      if (section) {
        filteredItems = [...filteredItems, section]; // Return single item of section
      }
    }
  }
  return filteredItems;
};

export {
  getBooleanData,
  filterDuplicates,
  formatAmountWithCommas,
  validateSalary,
  getSectionSelectedItem,
  searchSectionItems,
};
