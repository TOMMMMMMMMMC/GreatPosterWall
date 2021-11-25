import { toggleRowBySlot } from './shared'

/*
.cmp-torrent-table   # .torrent_table for now
  .groupid_1 .edition
    a .is-collapsed + -
  .groupid_1 .edition_1

- click: 折叠所有edition_1
- cmd-click 折叠本group下所有edition_x
*/
export default function toggleEdition(event, groupId, editionId) {
  event.preventDefault()
  const target = event.target
  const table = target.closest('.torrent_table')
  const isAllEdition = event.ctrlKey || event.metaKey
  const isHidden = target.classList.contains('is-collapsed')
  const filteredSlots = table.filteredSlots || []

  let rows, buttons
  if (isAllEdition) {
    rows = [
      ...table.querySelectorAll(`.torrent_row.groupid_${groupId}`),
    ].filter((v) => !v.classList.contains('edition'))
    buttons = [
      ...table.querySelectorAll(`.group_torrent.groupid_${groupId}.edition a`),
    ]
  } else {
    rows = [
      ...table.querySelectorAll(
        `.torrent_row.groupid_${groupId}.edition_${editionId}`
      ),
    ]
    buttons = [target]
  }

  for (const row of rows) {
    if (isHidden) {
      toggleRowBySlot({ row, filteredSlots })
      row.classList.remove('is-hidden-by-toggle-edition')
    } else {
      row.classList.add('hidden', 'is-hidden-by-toggle-edition')
    }
  }

  for (const row of table.querySelectorAll('.torrentdetails:not(.hidden)')) {
    row.classList.add('hidden')
  }

  for (const button of buttons) {
    if (isHidden) {
      button.innerHTML = '&minus;'
      button.classList.remove('is-collapsed')
      $(button).updateTooltip(translation.get('torrent_table.collapse_edition'))
    } else {
      button.innerHTML = '+'
      button.classList.add('is-collapsed')
      $(button).updateTooltip(translation.get('torrent_table.expand_edition'))
    }
  }
}
