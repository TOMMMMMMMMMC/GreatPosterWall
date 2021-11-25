document.querySelector('.cmp-dropdown-menu').addEventListener('click', (e) => {
  e.target.closest('.cmp-dropdown-menu').classList.toggle('is-open')
})
