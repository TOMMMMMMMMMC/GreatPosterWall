import ScreenshotComparison from './ScreenshotComparison'

function screenshotCompare(columnNames, images) {
  new ScreenshotComparison().open(columnNames, images)
}

window.screenshotCompare = screenshotCompare
