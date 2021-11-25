/*
.cmp-torrent-table   <rootSelector>
  <selector>; display: 'none' | ''
*/

export default function toggleAny(
  event,
  selector,
  { rootSelector = '.cmp-torrent-table' } = {}
) {
  event.preventDefault()
  const button = event.target
  const root = button.closest(rootSelector)
  const target = root.querySelector(selector)
  const isHidden = target.style.display === 'none'
  const nextDisplay = isHidden ? '' : 'none'
  target.style.display = nextDisplay
}
