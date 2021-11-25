import { pullAll } from 'lodash'
import { toggleRowBySlot } from './shared'

/*
table.filteredSlots = ['cn_quantity']

.cmp-torrent-table
  <buttons>
    .slot_filter_button.is-active <i>
    .slot_filter_button.type-clear <i>
  .torrent_row data-slot=<slotName>
*/

export default function filterSlot(event, slotNames) {
  event.preventDefault()
  const target = event.target.parentElement
  const table = target.closest('.cmp-torrent-table')
  const isActive = target.classList.contains('is-active')
  const clearBtn = table.querySelector('.slot_filter_button.type-clear')
  const isClear = slotNames.length === 0
  let filteredSlots = table.filteredSlots || []

  if (isClear) {
    filteredSlots = []
  } else if (isActive) {
    pullAll(filteredSlots, slotNames)
  } else {
    filteredSlots.push(...slotNames)
  }
  table.filteredSlots = filteredSlots

  // <button>.is-active
  for (const filterBtn of table.querySelectorAll(
    '.slot_filter_button:not(.type-clear)'
  )) {
    if (filteredSlots.includes(filterBtn.getAttribute('data-slot'))) {
      filterBtn.classList.add('is-active')
    } else {
      filterBtn.classList.remove('is-active')
    }
  }

  // <clearButton>
  if (filteredSlots.length > 0) {
    clearBtn.style.visibility = 'visible'
  } else {
    clearBtn.style.visibility = 'hidden'
  }

  for (const row of table.querySelectorAll(
    '.torrent_row:not(.is-hidden-by-toggle-edition)'
  )) {
    toggleRowBySlot({ row, filteredSlots })
  }

  for (const row of table.querySelectorAll('.torrentdetails:not(.hidden)')) {
    row.classList.add('hidden')
  }
}
