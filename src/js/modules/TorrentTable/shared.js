export function toggleRowBySlot({ row, filteredSlots }) {
  if (
    filteredSlots.length === 0 ||
    filteredSlots.includes(row.getAttribute('data-slot'))
  ) {
    row.classList.remove('hidden')
  } else {
    row.classList.add('hidden')
  }
}
