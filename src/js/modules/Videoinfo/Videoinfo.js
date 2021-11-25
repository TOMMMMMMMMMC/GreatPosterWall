import MediainfoParser from './Mediainfo/MediainfoParser'
import MediainfoConverter from './Mediainfo/MediainfoConverter'
import BdinfoParser from './Bdinfo/BdinfoParser'
import BdinfoConverter from './Bdinfo/BdinfoConverter'
import { removeMediainfoTag } from './utils'

const SPECIAL_CHARS = [
  8194, // space
  8205, // zero width joiner
]

export default class Videoinfo {
  static getType(text) {
    return text.match(/Disc (Title|Label)\s*:/i)
      ? 'bdinfo'
      : text.match(/Complete name\s*:/i)
      ? 'mediainfo'
      : null
  }

  static convertBBCode(rawText) {
    const text = removeMediainfoTag(rawText)
    const type = this.getType(text)
    switch (type) {
      case 'mediainfo': {
        const info = new MediainfoParser().parse(text)
        if (!info) {
          return
        }
        const fields = new MediainfoConverter().convert(info)
        console.log('mediainfo', { info, fields })
        return fields
      }
      case 'bdinfo': {
        const info = new BdinfoParser().parse(text)
        if (!info) {
          return
        }
        return new BdinfoConverter().convert(info)
      }
      default:
        console.error(
          'mediainfo unknown type, no Disc Title/Label or Complete name'
        )
        return null
    }
  }
}

export function hasUnsupportedChars(text) {
  return SPECIAL_CHARS.some((v) => text.match(String.fromCharCode(v)))
}
