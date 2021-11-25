/* screenshot-comparison.css */
import 'forked/mousetrap.min'

export default class ScreenshotComparison {
  constructor() {
    this.names = null
    this.label = null
    this.lightbox = null
    this.selectedColumn = 0
    this.selectedRow = null
    this.ignoreMouseEvents = 0
  }

  open(columnNames, images) {
    const instance = this

    // Disable background scrolling. http://stackoverflow.com/a/13891717
    if ($(document).height() > $(window).height()) {
      this.ScrollTop = $('html').scrollTop()
        ? $('html').scrollTop()
        : $('body').scrollTop()
    }
    // Works for Chrome, Firefox, IE...
    else {
      this.ScrollTop = 0
    }

    this.lightbox = $("<div class='screenshot-comparison'>")

    this.label = $("<div class='label'></div>")
    this.label.appendTo(this.lightbox)

    this.createControl(columnNames, images)

    this.lightbox.removeClass('hidden')
    this.lightbox.append(
      $(
        `<div class='help'>${translation.get(
          'screenshot_comparison.help'
        )} </div> `
      )
    )
    this.lightbox.on('scroll', function () {
      instance.updateLabel()
    })
    this.lightbox.click(function (event) {
      instance.close()
    })

    $('#wrapper').addClass('hidden')
    this.lightbox.appendTo($('body'))

    this.selectedRow = $('.screenshot-comparison .js-row').first()
    this.updateLabel()

    this.bindHotkey('up', function () {
      instance.scrollToNextRow(false)
    })
    this.bindHotkey('down', function () {
      instance.scrollToNextRow(true)
    })
    this.bindHotkey('left', function () {
      instance.showNextColumn(false)
    })
    this.bindHotkey('right', function () {
      instance.showNextColumn(true)
    })
    this.bindHotkey('escape', function () {
      instance.close()
    })

    function bindNumericHotkey(number) {
      instance.bindHotkey(String(number + 1), function () {
        instance.showColumn(number)
      })
    }

    for (let i = 0; i < columnNames.length; ++i) {
      bindNumericHotkey(i)
    }
  }

  close() {
    // Unbind doesn't work
    // https://github.com/ccampbell/mousetrap/issues/306

    this.bindHotkey('up', function () {})
    this.bindHotkey('down', function () {})
    this.bindHotkey('left', function () {})
    this.bindHotkey('right', function () {})
    this.bindHotkey('escape', function () {})

    for (let i = 0; i < this.names.length; ++i) {
      this.bindHotkey(String(i + 1), () => {})
    }

    $('#wrapper').removeClass('hidden')
    this.lightbox.remove()

    $('html,body').scrollTop(this.ScrollTop)

    this.names = null
    this.label = null
    this.lightbox = null
    this.selectedRow = null
  }

  createControl(columnNames, images1d) {
    const instance = this

    const columnCount = columnNames.length
    for (let i = 0; i < columnCount; ++i) {
      columnNames[i] = $.trim(columnNames[i])
    }

    this.names = columnNames

    const images = []
    const imageCount = images1d.length
    for (let index = 0; index < imageCount; ++index) {
      const column = index % columnCount
      if (column === 0) images.push([])
      images[images.length - 1].push(images1d[index])
    }

    const dynamicContainer = $('<div>')

    const rowCount = images.length
    for (let rowIndex = 0; rowIndex < rowCount; ++rowIndex) {
      const rowDiv = $("<div class='row js-row'>")
      rowDiv.attr('data-current_column', 0)
      rowDiv.on('mousemove mouseenter mouseleave', function (event) {
        instance.handleMouseMove(event)
      })

      let imageDiv = $('<div>')
      let image = $("<img style='visibility: hidden'>")
      image.attr('src', images[rowIndex][0])
      image.appendTo(imageDiv)
      imageDiv.appendTo(rowDiv)

      for (let columnIndex = 0; columnIndex < columnCount; ++columnIndex) {
        imageDiv = $("<div class='image-container'>")
        if (columnIndex === 0) {
          image = $("<img class='image js-image'>")
        } else {
          image = $("<img class='image js-image' style='visibility: hidden;'>")
        }
        image.attr('src', images[rowIndex][columnIndex])
        image.appendTo(imageDiv)
        imageDiv.appendTo(rowDiv)
      }
      rowDiv.appendTo(dynamicContainer)
    }

    dynamicContainer.appendTo(this.lightbox)
  }

  updateLabel() {
    if (this.selectedRow == null) return
    this.label.text(this.names[this.selectedColumn])
  }

  showColumn(column) {
    const currentColumn = this.selectedRow.attr('data-current_column')
    const images = $('.js-image', this.selectedRow)
    images.eq(currentColumn).css('visibility', 'hidden')
    images.eq(column).css('visibility', 'visible')

    this.selectedRow.attr('data-current_column', column)

    this.selectedColumn = column
    this.updateLabel()
  }

  handleMouseMove(event) {
    if (this.ignoreMouseEvents > 0) {
      --this.ignoreMouseEvents
      return
    }

    const rowDiv = $(event.currentTarget)
    let hoverColumn = 0
    const parentOffset = rowDiv.offset()
    const x = event.pageX - parentOffset.left - this.lightbox.scrollLeft()
    const visibleWidth = Math.min(rowDiv.width(), this.lightbox.width())

    if (x >= 0 && visibleWidth > 0) {
      const columnCount = this.names.length
      hoverColumn = Math.floor((x * columnCount) / visibleWidth)
      if (hoverColumn >= columnCount) {
        hoverColumn = 0
      }
      this.selectedRow = rowDiv
    }

    this.showColumn(hoverColumn)
  }

  bindHotkey(hotkey, callback) {
    Mousetrap.bindGlobal(hotkey, callback, 'keydown')
  }

  scrollToNextRow(scrollToNext) {
    const sibling = scrollToNext
      ? this.selectedRow.next('.js-row')
      : this.selectedRow.prev('.js-row')
    if (sibling.length > 0) {
      // If the mouse is over an image then we'll get unwanted mouse
      // events and selected row could change to a different row, so
      // we have to ignore the messages.
      // Firefox: mouseleave, mouseenter. Chrome: mouseleave, mouseenter, mousemove.
      this.ignoreMouseEvents = 3

      const offset = sibling.position()
      this.lightbox.scrollTop(this.lightbox.scrollTop() + offset.top)
      this.selectedRow = sibling
      this.updateLabel()
    }
  }

  showNextColumn(showNext) {
    let column = showNext ? this.selectedColumn + 1 : this.selectedColumn - 1
    if (column >= this.names.length) {
      column = 0
    } else if (column < 0) {
      column = this.names.length - 1
    }

    this.showColumn(column)
  }
}
