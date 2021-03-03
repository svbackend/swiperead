export function scrollToEl(el: HTMLElement|null) {
  if (el) {
    window.scrollTo({top: el.offsetTop, behavior: "smooth"})
  }
}
