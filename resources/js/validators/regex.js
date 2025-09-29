// Basic email validation (not full RFC compliance, but good enough for forms)
export const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

// Dutch postal code: 1000 AA (with optional space, case-insensitive)
export const postalNlRegex = /^[1-9][0-9]{3}\s?[A-Z]{2}$/i

// Belgian postal code: 1000 - 9999 (exactly 4 digits, cannot start with 0)
export const postalBeRegex = /^[1-9][0-9]{3}$/

// Generic postal code: supports both NL and BE
export const postalRegex = new RegExp(
  `(${postalNlRegex.source})|(${postalBeRegex.source})`,
  'i'
)

// Dutch phone number (mobile or landline, with or without +31 / 0031)
export const phoneNlRegex = /^(?:\+31|0031|0)(?:6\d{8}|[1-9]\d{8})$/

// Belgian phone number (mobile or landline, with or without +32 / 0032)
export const phoneBeRegex = /^(?:\+32|0032|0)(?:4\d{8}|[1-9]\d{7,8})$/

// Generic phone number: supports both NL and BE
export const phoneRegex = new RegExp(
  `(${phoneNlRegex.source})|(${phoneBeRegex.source})`
)
