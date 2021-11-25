import mediainfoAutofill from './mediainfoAutofill'
import { addMediainfoTextarea, removeMediainfoTextarea } from './addElement'

document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('#imdb')?.value) {
    document.querySelector('#imdb_button').click()
  }

  document
    .querySelector('[name="mediainfo[]"]')
    .addEventListener('change', (e) => {
      mediainfoAutofill(e.target.value)
    })

  document
    .querySelector('#add-mediainfo')
    .addEventListener('click', addMediainfoTextarea)
  document
    .querySelector('#remove-mediainfo')
    .addEventListener('click', removeMediainfoTextarea)

  if (window.location.href.match(/upload\.php\?groupid=\d+/)) {
    window.artistsShowMore({ hide: true })
  }
})
