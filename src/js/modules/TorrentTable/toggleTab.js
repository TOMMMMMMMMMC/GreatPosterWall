/*
.cmp-table-tab <selector>
.cmp-table-tab <selector>
*/
export default function toggleTab(event, selector) {
  const target = event.target
  const table1 = target.closest('.cmp-table-tab')
  const table2 = table1.parentElement.querySelector(`:scope > ${selector}`)

  event.preventDefault()
  table1.style.display = 'none'
  table2.style.display = ''
}
