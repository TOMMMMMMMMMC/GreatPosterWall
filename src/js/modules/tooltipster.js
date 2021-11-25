/*
- text tooltip: <div class="tooltip" title="tip">
- html tooltip: <div class="tooltip-html">Hello <div class="tooltip-content hidden">tip
*/

$.tooltipster.setDefaults({
  delay: 500,
  updateAnimation: false,
  maxWidth: 400,
})

$.fn.extend({
  updateTooltip(tooltip) {
    $(this).tooltipster('content', tooltip)
  },
})

$('.tooltip').tooltipster()

$('.tooltip-html').tooltipster({
  functionInit(instance, helper) {
    const content = $(helper.origin)
      .find('.tooltip-content')
      .removeClass('hidden')
      .detach()
    instance.content(content)
  },
})

$('.tooltip_interactive').tooltipster({
  interactive: true,
  interactiveTolerance: 500,
})

$('.tooltip_image').tooltipster({
  fixedWidth: 252,
})

$('.tooltip_gold').tooltipster({
  theme: '.tooltipster-default gold_theme',
})

$.tooltipster.group('grouped')
