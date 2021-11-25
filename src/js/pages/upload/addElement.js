import { validateMediainfo, registerValidation } from './validation'

export function addElement({ target, appendTo, transform }) {
  const cloned = document.querySelector(target).cloneNode(true)
  document.querySelector(appendTo).appendChild(cloned)
  if (transform) {
    transform(cloned)
  }
}

export function removeElement({ target }) {
  const targets = Array.from(document.querySelectorAll(target))
  if (targets.length <= 1) {
    return
  }
  targets[targets.length - 1].remove()
}

export function addMediainfoTextarea(e) {
  e.preventDefault()
  addElement({
    target: '#mediainfo .error-container',
    appendTo: '#mediainfo .items',
    transform(node) {
      node.classList.remove('form-invalid')
      const errorMessage = node.querySelector('.error-message')
      if (errorMessage) {
        errorMessage.innerHTML = ''
      }

      const textarea = node.querySelector('textarea')
      textarea.value = ''

      const previewHtml = node.querySelector('.bbcode-preview-html')
      previewHtml.innerHTML = 'MEDIAINFO'

      registerValidation()
    },
  })
}

export function removeMediainfoTextarea(e) {
  e.preventDefault()
  removeElement({
    target: '#mediainfo .error-container',
  })
}
