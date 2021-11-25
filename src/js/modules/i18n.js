import translations from '../../../i18n'
import { get } from 'lodash'

var translator
function Language(lang) {
  if (!(lang in translations)) {
    lang = 'chs'
  }

  this.getStr = function (str) {
    return get(translations[lang], str, str)
  }
}

window.translation = {
  get: function (str) {
    if (!translator) {
      translator = new Language(cookie.get('lang'))
    }
    return translator.getStr(str)
  },
  format: function () {
    var s = arguments[0]
    for (var i = 0; i < arguments.length - 1; i++) {
      var reg = new RegExp('\\{' + i + '\\}', 'gm')
      s = s.replace(reg, arguments[i + 1])
    }
    return s
  },
}
