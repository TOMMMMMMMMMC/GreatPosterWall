import './ScreenshotComparison'
import BBCodeToolbar from './BBCodeToolbar'
import BBCodePreview from './BBCodePreview'

/*
.bbcode-editor
  .bbcode-toolbar
  .bbcode-textarea
*/

document.addEventListener('DOMContentLoaded', () => {
  registerBBCodeToolbar()
  registerMediainfoToggle()
  BBCodePreview.register()
})

function registerBBCodeToolbar() {
  const editors = Array.from(document.querySelectorAll('.bbcode-editor'))
  for (const editor of editors) {
    const toolbar = editor.querySelector('.bbcode-toolbar')
    const textarea = editor.nextElementSibling.querySelector('.bbcode-textarea')
    new BBCodeToolbar({ textarea, toolbar }).register()
  }
}

function registerMediainfoToggle() {
  document.addEventListener('click', (e) => {
    if (e.target.getAttribute('data-action') !== 'toggle-mediainfo') {
      return
    }
    e.preventDefault()
    e.target.nextElementSibling.classList.toggle('hidden')
    e.target.nextElementSibling.nextElementSibling.classList.toggle('hidden')
  })
}
