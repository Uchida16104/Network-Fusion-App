export function strlen(value) {
  return String(value ?? '').length
}

export function strpos(haystack, needle) {
  return String(haystack).indexOf(String(needle))
}

export function json_encode(value) {
  return JSON.stringify(value)
}

export function array_merge(...arrays) {
  return arrays.flat()
}
