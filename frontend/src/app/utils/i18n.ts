export interface Locale {
  code: string;
  label: string;
}

export const LOCALES: Locale[] = [
  {code: 'uk', label: 'Українська'},
  {code: 'ru', label: 'Русский'},
  {code: 'en', label: 'English'},
];

// This is not just en-US. It will be replaced by real current locale during compile time
export const CURRENT_LOCALE = $localize`en-US`; // dirty hack but what else to do without runtime access =(
export const DEFAULT_LOCALE = `uk-UA`;
