/**
 * Copyright (C) 2010-2012 Graham Breach
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * jQuery.tagcanvas 1.17.1
 * For more information, please contact <graham@goat1000.com>
 */
;(function (I) {
  var R,
    Q,
    G = Math.abs,
    r = Math.sin,
    h = Math.cos,
    z = Math.max,
    V = Math.min,
    B = {},
    C = {},
    D = {
      0: '0,',
      1: '17,',
      2: '34,',
      3: '51,',
      4: '68,',
      5: '85,',
      6: '102,',
      7: '119,',
      8: '136,',
      9: '153,',
      a: '170,',
      A: '170,',
      b: '187,',
      B: '187,',
      c: '204,',
      C: '204,',
      d: '221,',
      D: '221,',
      e: '238,',
      E: '238,',
      f: '255,',
      F: '255,',
    },
    e,
    J,
    d,
    l = document,
    y,
    c = {}
  for (R = 0; R < 256; ++R) {
    Q = R.toString(16)
    if (R < 16) {
      Q = '0' + Q
    }
    C[Q] = C[Q.toUpperCase()] = R.toString() + ','
  }
  function N(i) {
    return typeof i != 'undefined'
  }
  function H(j) {
    var Y = j.length - 1,
      X,
      Z
    while (Y) {
      Z = ~~(Math.random() * Y)
      X = j[Y]
      j[Y] = j[Z]
      j[Z] = X
      --Y
    }
  }
  function n(Y, aa, af, ac) {
    var ab,
      ae,
      j,
      ad,
      ag = [],
      Z = Math.PI * (3 - Math.sqrt(5)),
      X = 2 / Y
    for (ab = 0; ab < Y; ++ab) {
      ae = ab * X - 1 + X / 2
      j = Math.sqrt(1 - ae * ae)
      ad = ab * Z
      ag.push([h(ad) * j * aa, ae * af, r(ad) * j * ac])
    }
    return ag
  }
  function U(Z, X, ac, ai, ah, af, ae, ad, ab) {
    var ag,
      aj = [],
      aa = Math.PI * (3 - Math.sqrt(5)),
      Y = 2 / Z
    for (af = 0; af < Z; ++af) {
      ae = af * Y - 1 + Y / 2
      ag = af * aa
      ad = h(ag)
      ab = r(ag)
      aj.push(X ? [ae * ac, ad * ai, ab * ah] : [ad * ac, ae * ai, ab * ah])
    }
    return aj
  }
  function w(Y, i, j, X) {
    return U(Y, 0, i, j, X)
  }
  function F(Y, i, j, X) {
    return U(Y, 1, i, j, X)
  }
  function p(aa, i) {
    var Z = aa,
      Y,
      X,
      j = (i * 1).toPrecision(3) + ')'
    if (aa[0] === '#') {
      if (!B[aa]) {
        if (aa.length === 4) {
          B[aa] = 'rgba(' + D[aa[1]] + D[aa[2]] + D[aa[3]]
        } else {
          B[aa] =
            'rgba(' +
            C[aa.substr(1, 2)] +
            C[aa.substr(3, 2)] +
            C[aa.substr(5, 2)]
        }
      }
      Z = B[aa] + j
    } else {
      if (aa.substr(0, 4) === 'rgb(' || aa.substr(0, 4) === 'hsl(') {
        Z = aa.replace('(', 'a(').replace(')', ',' + j)
      } else {
        if (aa.substr(0, 5) === 'rgba(' || aa.substr(0, 5) === 'hsla(') {
          ;(Y = aa.lastIndexOf(',') + 1), (X = aa.indexOf(')'))
          i *= parseFloat(aa.substring(Y, X))
          Z = aa.substr(0, Y) + i.toPrecision(3) + ')'
        }
      }
    }
    return Z
  }
  function g(i, j) {
    if (window.G_vmlCanvasManager) {
      return null
    }
    var X = l.createElement('canvas')
    X.width = i
    X.height = j
    return X
  }
  function v() {
    var j = g(3, 3),
      Y,
      X
    if (!j) {
      return false
    }
    Y = j.getContext('2d')
    Y.strokeStyle = '#000'
    Y.shadowColor = '#fff'
    Y.shadowBlur = 3
    Y.globalAlpha = 0
    Y.strokeRect(2, 2, 2, 2)
    Y.globalAlpha = 1
    X = Y.getImageData(2, 2, 1, 1)
    j = null
    return X.data[0] > 0
  }
  function W(ae, j) {
    var X = 1024,
      aa = ae.weightGradient,
      Z,
      ac,
      Y,
      ad,
      ab
    if (ae.gCanvas) {
      ac = ae.gCanvas.getContext('2d')
    } else {
      ae.gCanvas = Z = g(X, 1)
      if (!Z) {
        return null
      }
      ac = Z.getContext('2d')
      ad = ac.createLinearGradient(0, 0, X, 0)
      for (Y in aa) {
        ad.addColorStop(1 - Y, aa[Y])
      }
      ac.fillStyle = ad
      ac.fillRect(0, 0, X, 1)
    }
    ab = ac.getImageData(~~((X - 1) * j), 0, 1, 1).data
    return 'rgba(' + ab[0] + ',' + ab[1] + ',' + ab[2] + ',' + ab[3] / 255 + ')'
  }
  function u(aa, Z, X, ad, ab, ac, j) {
    var Y = (ac || 0) + (j && j[0] < 0 ? G(j[0]) : 0),
      i = (ac || 0) + (j && j[1] < 0 ? G(j[1]) : 0)
    aa.font = Z
    aa.textBaseline = 'top'
    aa.fillStyle = X
    ab && (aa.shadowColor = ab)
    ac && (aa.shadowBlur = ac)
    j && ((aa.shadowOffsetX = j[0]), (aa.shadowOffsetY = j[1]))
    aa.fillText(ad, Y, i)
  }
  function m(aj, ab, af, ah, aa, X, ad, ae, j, ai, ag) {
    var Y = ah + G(j[0]) + ae + ae,
      i = aa + G(j[1]) + ae + ae,
      Z,
      ac
    Z = g(Y + ai, i + ag)
    if (!Z) {
      return null
    }
    ac = Z.getContext('2d')
    u(ac, ab, X, aj, ad, ae, j)
    return Z
  }
  function P(ab, ae, af, Y) {
    var Z = ab.width + G(Y[0]) + af + af,
      j = ab.height + G(Y[1]) + af + af,
      ac,
      ad,
      aa = (af || 0) + (Y && Y[0] < 0 ? G(Y[0]) : 0),
      X = (af || 0) + (Y && Y[1] < 0 ? G(Y[1]) : 0)
    ac = g(Z, j)
    if (!ac) {
      return null
    }
    ad = ac.getContext('2d')
    ae && (ad.shadowColor = ae)
    af && (ad.shadowBlur = af)
    Y && ((ad.shadowOffsetX = Y[0]), (ad.shadowOffsetY = Y[1]))
    ad.drawImage(ab, aa, X)
    return ac
  }
  function K(aj, ab, ah) {
    var ai = parseInt(aj.length * ah),
      aa = parseInt(ah * 2),
      Y = g(ai, aa),
      ae,
      j,
      Z,
      ad,
      ag,
      af,
      X,
      ac
    if (!Y) {
      return null
    }
    ae = Y.getContext('2d')
    ae.fillStyle = '#000'
    ae.fillRect(0, 0, ai, aa)
    u(ae, ah + 'px ' + ab, '#fff', aj)
    j = ae.getImageData(0, 0, ai, aa)
    Z = j.width
    ad = j.height
    ac = { min: { x: Z, y: ad }, max: { x: -1, y: -1 } }
    for (af = 0; af < ad; ++af) {
      for (ag = 0; ag < Z; ++ag) {
        X = (af * Z + ag) * 4
        if (j.data[X + 1] > 0) {
          if (ag < ac.min.x) {
            ac.min.x = ag
          }
          if (ag > ac.max.x) {
            ac.max.x = ag
          }
          if (af < ac.min.y) {
            ac.min.y = af
          }
          if (af > ac.max.y) {
            ac.max.y = af
          }
        }
      }
    }
    if (Z != ai) {
      ac.min.x *= ai / Z
      ac.max.x *= ai / Z
    }
    if (ad != aa) {
      ac.min.y *= ai / ad
      ac.max.y *= ai / ad
    }
    Y = null
    return ac
  }
  function t(i) {
    return "'" + i.replace(/(\'|\")/g, '').replace(/\s*,\s*/g, "', '") + "'"
  }
  function A(i, j, X) {
    X = X || l
    if (X.addEventListener) {
      X.addEventListener(i, j, false)
    } else {
      X.attachEvent('on' + i, j)
    }
  }
  function O(Z, ab, Y, j) {
    var X = j.taglist,
      aa = j.imageScale
    if (aa && !(ab.width && ab.height)) {
      A(
        'load',
        function () {
          O(Z, ab, Y, j)
        },
        window
      )
      return
    }
    if (!Z.complete) {
      A(
        'load',
        function () {
          O(Z, ab, Y, j)
        },
        Z
      )
      return
    }
    ab.width = ab.width
    ab.height = ab.height
    if (aa) {
      Z.width = ab.width * aa
      Z.height = ab.height * aa
    }
    Y.w = Z.width
    Y.h = Z.height
    X.push(Y)
  }
  function M(Y, X) {
    var j = l.defaultView,
      i = X.replace(/\-([a-z])/g, function (Z) {
        return Z.charAt(1).toUpperCase()
      })
    return (
      (j &&
        j.getComputedStyle &&
        j.getComputedStyle(Y, null).getPropertyValue(X)) ||
      (Y.currentStyle && Y.currentStyle[i])
    )
  }
  function x(X, j) {
    var i = 1,
      Y
    if (X.weightFrom) {
      i = 1 * (j.getAttribute(X.weightFrom) || X.textHeight)
    } else {
      if ((Y = M(j, 'font-size'))) {
        i =
          (Y.indexOf('px') > -1 && Y.replace('px', '') * 1) ||
          (Y.indexOf('pt') > -1 && Y.replace('pt', '') * 1.25) ||
          Y * 3.3
      } else {
        X.weight = false
      }
    }
    return i
  }
  function k(X) {
    L(X)
    var j = X.target || X.fromElement.parentNode,
      i = s.tc[j.id]
    i && (i.mx = i.my = -1)
  }
  function L(Z) {
    var Y,
      X,
      j = l.documentElement,
      aa
    for (Y in s.tc) {
      X = s.tc[Y]
      if (X.tttimer) {
        clearTimeout(X.tttimer)
        X.tttimer = null
      }
      aa = I(X.canvas).offset()
      if (Z.pageX) {
        X.mx = Z.pageX - aa.left
        X.my = Z.pageY - aa.top
      } else {
        X.mx = Z.clientX + (j.scrollLeft || l.body.scrollLeft) - aa.left
        X.my = Z.clientY + (j.scrollTop || l.body.scrollTop) - aa.top
      }
    }
  }
  function q(Y) {
    var j = s,
      i = l.addEventListener ? 0 : 1,
      X = Y.target && N(Y.target.id) ? Y.target.id : Y.srcElement.parentNode.id
    if (X && Y.button == i && j.tc[X]) {
      L(Y)
      j.tc[X].Clicked(Y)
    }
  }
  function T(X) {
    var i = s,
      j = X.target && N(X.target.id) ? X.target.id : X.srcElement.parentNode.id
    if (j && i.tc[j]) {
      X.cancelBubble = true
      X.returnValue = false
      X.preventDefault && X.preventDefault()
      i.tc[j].Wheel((X.wheelDelta || X.detail) > 0)
    }
  }
  function o() {
    var X = s.tc,
      j
    for (j in X) {
      X[j].Draw()
    }
  }
  function b(X, i) {
    var j = r(i),
      Y = h(i)
    return { x: X.x, y: X.y * Y + X.z * j, z: X.y * -j + X.z * Y }
  }
  function a(X, i) {
    var j = r(i),
      Y = h(i)
    return { x: X.x * Y + X.z * -j, y: X.y, z: X.x * j + X.z * Y }
  }
  function S(X, ae, ad, Z, ac, aa) {
    var i,
      Y,
      ab,
      j = X.z1 / (X.z1 + X.z2 + ae.z)
    i = ae.y * j * aa
    Y = ae.x * j * ac
    ab = X.z2 + ae.z
    return { x: Y, y: i, z: ab }
  }
  function f(i) {
    this.ts = new Date().valueOf()
    this.tc = i
    this.x = this.y = this.w = this.h = this.sc = 1
    this.z = 0
    this.Draw =
      i.pulsateTo < 1 && i.outlineMethod != 'colour'
        ? this.DrawPulsate
        : this.DrawSimple
    this.SetMethod(i.outlineMethod)
  }
  e = f.prototype
  e.SetMethod = function (X) {
    var j = {
        block: ['PreDraw', 'DrawBlock'],
        colour: ['PreDraw', 'DrawColour'],
        outline: ['PostDraw', 'DrawOutline'],
        classic: ['LastDraw', 'DrawOutline'],
        none: ['LastDraw'],
      },
      i = j[X] || j.outline
    if (X == 'none') {
      this.Draw = function () {
        return 1
      }
    } else {
      this.drawFunc = this[i[1]]
    }
    this[i[0]] = this.Draw
  }
  e.Update = function (ad, ac, ae, aa, ab, j, Z, i) {
    var X = this.tc.outlineOffset,
      Y = 2 * X
    this.x = ab * ad + Z - X
    this.y = ab * ac + i - X
    this.w = ab * ae + Y
    this.h = ab * aa + Y
    this.sc = ab
    this.z = j.z
  }
  e.DrawOutline = function (aa, i, Z, j, X, Y) {
    aa.strokeStyle = Y
    aa.strokeRect(i, Z, j, X)
  }
  e.DrawColour = function (Y, ab, Z, ac, X, i, ad, j, aa) {
    return this[ad.image ? 'DrawColourImage' : 'DrawColourText'](
      Y,
      ab,
      Z,
      ac,
      X,
      i,
      ad,
      j,
      aa
    )
  }
  e.DrawColourText = function (Z, ac, aa, ad, X, i, ae, j, ab) {
    var Y = ae.colour
    ae.colour = i
    ae.Draw(Z, j, ab)
    ae.colour = Y
    return 1
  }
  e.DrawColourImage = function (ac, af, ad, ag, ab, i, aj, j, ae) {
    var ah = ac.canvas,
      Z = ~~z(af, 0),
      Y = ~~z(ad, 0),
      aa = (V(ah.width - Z, ag) + 0.5) | 0,
      ai = (V(ah.height - Y, ab) + 0.5) | 0,
      X
    if (y) {
      ;(y.width = aa), (y.height = ai)
    } else {
      y = g(aa, ai)
    }
    if (!y) {
      return this.SetMethod('outline')
    }
    X = y.getContext('2d')
    X.drawImage(ah, Z, Y, aa, ai, 0, 0, aa, ai)
    ac.clearRect(Z, Y, aa, ai)
    aj.Draw(ac, j, ae)
    ac.setTransform(1, 0, 0, 1, 0, 0)
    ac.save()
    ac.beginPath()
    ac.rect(Z, Y, aa, ai)
    ac.clip()
    ac.globalCompositeOperation = 'source-in'
    ac.fillStyle = i
    ac.fillRect(Z, Y, aa, ai)
    ac.restore()
    ac.globalCompositeOperation = 'destination-over'
    ac.drawImage(y, 0, 0, aa, ai, Z, Y, aa, ai)
    ac.globalCompositeOperation = 'source-over'
    return 1
  }
  e.DrawBlock = function (aa, i, Z, j, X, Y) {
    aa.fillStyle = Y
    aa.fillRect(i, Z, j, X)
  }
  e.DrawSimple = function (Z, i, j, Y) {
    var X = this.tc
    Z.setTransform(1, 0, 0, 1, 0, 0)
    Z.strokeStyle = X.outlineColour
    Z.lineWidth = X.outlineThickness
    Z.shadowBlur = Z.shadowOffsetX = Z.shadowOffsetY = 0
    Z.globalAlpha = 1
    return this.drawFunc(
      Z,
      this.x,
      this.y,
      this.w,
      this.h,
      X.outlineColour,
      i,
      j,
      Y
    )
  }
  e.DrawPulsate = function (aa, i, j, Y) {
    var Z = new Date().valueOf() - this.ts,
      X = this.tc
    aa.setTransform(1, 0, 0, 1, 0, 0)
    aa.strokeStyle = X.outlineColour
    aa.lineWidth = X.outlineThickness
    aa.shadowBlur = aa.shadowOffsetX = aa.shadowOffsetY = 0
    aa.globalAlpha =
      X.pulsateTo +
      (1 - X.pulsateTo) *
        (0.5 + h((2 * Math.PI * Z) / (1000 * X.pulsateTime)) / 2)
    return this.drawFunc(
      aa,
      this.x,
      this.y,
      this.w,
      this.h,
      X.outlineColour,
      i,
      j,
      Y
    )
  }
  e.Active = function (X, i, j) {
    return (
      i >= this.x && j >= this.y && i <= this.x + this.w && j <= this.y + this.h
    )
  }
  e.PreDraw = e.PostDraw = e.LastDraw = function () {}
  function E(Z, j, ad, af, ae, ab, X, Y) {
    var ac = Z.ctxt,
      aa
    this.tc = Z
    this.image = j.src ? j : null
    this.name = j.src ? '' : j
    this.title = ad.title || null
    this.a = ad
    this.p3d = {
      x: af[0] * Z.radius * 1.1,
      y: af[1] * Z.radius * 1.1,
      z: af[2] * Z.radius * 1.1,
    }
    this.x = this.y = 0
    this.w = ae
    this.h = ab
    this.colour = X || Z.textColour
    this.textFont = Y || Z.textFont
    this.weight = this.sc = this.alpha = 1
    this.weighted = !Z.weight
    this.outline = new f(Z)
    if (this.image) {
      if (Z.txtOpt && Z.shadow) {
        aa = P(this.image, Z.shadow, Z.shadowBlur, Z.shadowOffset)
        if (aa) {
          this.image = aa
          this.w = aa.width
          this.h = aa.height
        }
      }
    } else {
      this.textHeight = Z.textHeight
      this.extents = K(this.name, this.textFont, this.textHeight)
      this.Measure(ac, Z)
    }
    this.SetShadowColour = Z.shadowAlpha
      ? this.SetShadowColourAlpha
      : this.SetShadowColourFixed
    this.SetDraw(Z)
  }
  J = E.prototype
  J.SetDraw = function (i) {
    this.Draw = this.image
      ? i.ie > 7
        ? this.DrawImageIE
        : this.DrawImage
      : this.DrawText
    i.noSelect && (this.CheckActive = function () {})
  }
  J.Measure = function (ab, j) {
    this.h = this.extents
      ? this.extents.max.y + this.extents.min.y
      : this.textHeight
    ab.font = this.font = this.textHeight + 'px ' + this.textFont
    this.w = ab.measureText(this.name).width
    if (j.txtOpt) {
      var Y = j.txtScale,
        Z = Y * this.textHeight,
        aa = Z + 'px ' + this.textFont,
        X = [Y * j.shadowOffset[0], Y * j.shadowOffset[1]],
        i
      ab.font = aa
      i = ab.measureText(this.name).width
      this.image = m(
        this.name,
        aa,
        Z,
        i,
        Y * this.h,
        this.colour,
        j.shadow,
        Y * j.shadowBlur,
        X,
        Y,
        Y
      )
      if (this.image) {
        this.w = this.image.width / Y
        this.h = this.image.height / Y
      }
      this.SetDraw(j)
      j.txtOpt = this.image
    }
  }
  J.SetWeight = function (i) {
    if (!this.name.length) {
      return
    }
    this.weight = i
    this.Weight(this.tc.ctxt, this.tc)
    this.Measure(this.tc.ctxt, this.tc)
  }
  J.Weight = function (Y, X) {
    var j = this.weight,
      i = X.weightMode
    this.weighted = true
    if (i == 'colour' || i == 'both') {
      this.colour = W(X, (j - X.min_weight) / (X.max_weight - X.min_weight))
    }
    if (i == 'size' || i == 'both') {
      this.textHeight = j * X.weightSize
    }
    this.extents = K(this.name, this.textFont, this.textHeight)
  }
  J.SetShadowColourFixed = function (X, j, i) {
    X.shadowColor = j
  }
  J.SetShadowColourAlpha = function (X, j, i) {
    X.shadowColor = p(j, i)
  }
  J.DrawText = function (X, ab, j) {
    var ac = this.tc,
      Z = this.x,
      Y = this.y,
      aa,
      i,
      ad = this.sc
    X.globalAlpha = this.alpha
    X.setTransform(ad, 0, 0, ad, 0, 0)
    X.fillStyle = this.colour
    ac.shadow && this.SetShadowColour(X, ac.shadow, this.alpha)
    X.font = this.font
    aa = this.w
    i = this.h
    Z += ab / ad - aa / 2
    Y += j / ad - i / 2
    X.fillText(this.name, Z, Y)
  }
  J.DrawImage = function (Z, af, Y) {
    var ag = this.tc,
      ac = this.x,
      aa = this.y,
      ah = this.sc,
      j = this.image,
      ad = this.w,
      X = this.h,
      ab = this.alpha,
      ae = this.shadow
    Z.globalAlpha = ab
    Z.setTransform(ah, 0, 0, ah, 0, 0)
    Z.fillStyle = this.colour
    ae && this.SetShadowColour(Z, ae, ab)
    ac += af / ah - ad / 2
    aa += Y / ah - X / 2
    Z.drawImage(j, ac, aa, ad, X)
  }
  J.DrawImageIE = function (Z, ad, Y) {
    var j = this.image,
      ae = this.sc,
      ac = (j.width = this.w * ae),
      X = (j.height = this.h * ae),
      ab = this.x * ae + ad - ac / 2,
      aa = this.y * ae + Y - X / 2
    Z.setTransform(1, 0, 0, 1, 0, 0)
    Z.globalAlpha = this.alpha
    Z.drawImage(j, ab, aa)
  }
  J.Calc = function (Z, Y) {
    var i = a(this.p3d, Z),
      j = this.tc,
      aa = j.minBrightness,
      X = j.radius
    this.p3d = b(i, Y)
    i = S(j, this.p3d, this.w, this.h, j.stretchX, j.stretchY)
    this.x = i.x
    this.y = i.y
    this.sc = (j.z1 + j.z2 - i.z) / j.z2
    this.alpha = z(aa, V(1, aa + 1 - (i.z - j.z2 + X) / (2 * X)))
  }
  J.CheckActive = function (Y, ac, X) {
    var ad = this.tc,
      i = this.outline,
      ab = this.w,
      j = this.h,
      aa = this.x - ab / 2,
      Z = this.y - j / 2
    i.Update(aa, Z, ab, j, this.sc, this.p3d, ac, X)
    return i.Active(Y, ad.mx, ad.my) ? i : null
  }
  J.Clicked = function (aa) {
    var j = this.a,
      X = j.target,
      Y = j.href,
      i
    if (X != '' && X != '_self') {
      if (self.frames[X]) {
        self.frames[X] = Y
      } else {
        try {
          if (top.frames[X]) {
            top.frames[X] = Y
            return
          }
        } catch (Z) {}
        window.open(Y, X)
      }
      return
    }
    if (l.createEvent) {
      i = l.createEvent('MouseEvents')
      i.initMouseEvent(
        'click',
        1,
        1,
        window,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        0,
        null
      )
      if (!j.dispatchEvent(i)) {
        return
      }
    } else {
      if (j.fireEvent) {
        if (!j.fireEvent('onclick')) {
          return
        }
      }
    }
    l.location = Y
  }
  function s() {
    var j,
      X = {
        mx: -1,
        my: -1,
        z1: 20000,
        z2: 20000,
        z0: 0.0002,
        freezeActive: false,
        activeCursor: 'pointer',
        pulsateTo: 1,
        pulsateTime: 3,
        reverse: false,
        depth: 0.5,
        maxSpeed: 0.05,
        minSpeed: 0,
        decel: 0.95,
        interval: 20,
        initial: null,
        hideTags: true,
        minBrightness: 0.1,
        outlineColour: '#ffff99',
        outlineThickness: 2,
        outlineOffset: 5,
        outlineMethod: 'outline',
        textColour: '#ff99ff',
        textHeight: 15,
        textFont: 'Helvetica, Arial, sans-serif',
        shadow: '#000',
        shadowBlur: 0,
        shadowOffset: [0, 0],
        zoom: 1,
        weight: false,
        weightMode: 'size',
        weightFrom: null,
        weightSize: 1,
        weightGradient: { 0: '#f00', 0.33: '#ff0', 0.66: '#0f0', 1: '#00f' },
        txtOpt: true,
        txtScale: 2,
        frontSelect: false,
        wheelZoom: true,
        zoomMin: 0.3,
        zoomMax: 3,
        zoomStep: 0.05,
        shape: 'sphere',
        lock: null,
        tooltip: null,
        tooltipDelay: 300,
        tooltipClass: 'tctooltip',
        radiusX: 1,
        radiusY: 1,
        radiusZ: 1,
        stretchX: 1,
        stretchY: 1,
        shuffleTags: false,
        noSelect: false,
        noMouse: false,
        imageScale: 1,
      }
    for (j in X) {
      this[j] = X[j]
    }
    this.max_weight = 0
    this.min_weight = 200
  }
  d = s.prototype
  d.Draw = function () {
    var ag = this.canvas,
      ae = ag.width,
      X = ag.height,
      j = 0,
      ad = this.yaw,
      Y = this.pitch,
      Z = ae / 2,
      aj = X / 2,
      ah = this.ctxt,
      ab,
      ai,
      af,
      ac = -1,
      al = this.taglist,
      aa = al.length,
      ak = this.frontSelect
    if (ad == 0 && Y == 0 && this.drawn) {
      return this.Animate(ae, X)
    }
    ah.setTransform(1, 0, 0, 1, 0, 0)
    this.active = null
    for (af = 0; af < aa; ++af) {
      al[af].Calc(ad, Y)
    }
    al = al.sort(function (am, i) {
      return am.sc - i.sc
    })
    for (af = 0; af < aa; ++af) {
      ai = al[af].CheckActive(ah, Z, aj)
      ai = this.mx >= 0 && this.my >= 0 && al[af].CheckActive(ah, Z, aj)
      if (ai && ai.sc > j && (!ak || ai.z <= 0)) {
        ab = ai
        ab.index = ac = af
        j = ai.sc
      }
    }
    this.active = ab
    if (!this.txtOpt && this.shadow) {
      ah.shadowBlur = this.shadowBlur
      ah.shadowOffsetX = this.shadowOffset[0]
      ah.shadowOffsetY = this.shadowOffset[1]
    }
    ah.clearRect(0, 0, ae, X)
    for (af = 0; af < aa; ++af) {
      if (!(ac == af && ab.PreDraw(ah, al[af], Z, aj))) {
        al[af].Draw(ah, Z, aj)
      }
      ac == af && ab.PostDraw(ah)
    }
    if (this.freezeActive && ab) {
      this.yaw = this.pitch = this.drawn = 0
    } else {
      this.Animate(ae, X)
      this.drawn = aa == this.listLength
    }
    ab && ab.LastDraw(ah)
    ag.style.cursor = ab ? this.activeCursor : ''
    this.Tooltip(ab, al[ac])
  }
  d.TooltipNone = function () {}
  d.TooltipNative = function (j, i) {
    this.canvas.title = j && i.title ? i.title : ''
  }
  d.TooltipDiv = function (Y, j) {
    var i = this,
      X = i.ttdiv.style,
      Z = i.canvas.id
    if (Y && j.title) {
      i.ttdiv.innerHTML = j.title
      if (X.display == 'none' && !i.tttimer) {
        i.tttimer = setTimeout(function () {
          var aa = I(i.canvas).offset()
          X.display = 'block'
          X.left = aa.left + i.mx + 'px'
          X.top = aa.top + i.my + 24 + 'px'
          i.tttimer = null
        }, i.tooltipDelay)
      }
    } else {
      X.display = 'none'
    }
  }
  d.Animate = function (ac, Z) {
    var X = this,
      ab = X.mx,
      aa = X.my,
      j = X.lock,
      ae,
      ad,
      Y,
      i
    if (ab >= 0 && aa >= 0 && ab < ac && aa < Z) {
      ;(ae = X.maxSpeed), (i = X.reverse ? -1 : 1)
      if (j != 'x') {
        this.yaw = i * ((ae * 2 * ab) / ac - ae)
      }
      if (j != 'y') {
        this.pitch = i * -((ae * 2 * aa) / Z - ae)
      }
      this.initial = null
    } else {
      if (!X.initial) {
        ;(ae = X.minSpeed), (ad = G(X.yaw)), (Y = G(X.pitch))
        if (j != 'x' && ad > ae) {
          this.yaw = ad > X.z0 ? X.yaw * X.decel : 0
        }
        if (j != 'y' && Y > ae) {
          this.pitch = Y > X.z0 ? X.pitch * X.decel : 0
        }
      }
    }
  }
  d.Zoom = function (i) {
    this.z2 = this.z1 * (1 / i)
    this.drawn = 0
  }
  d.Clicked = function (Y) {
    var X = this.taglist,
      i = this.active
    try {
      if (i && X[i.index]) {
        X[i.index].Clicked(Y)
      }
    } catch (j) {}
  }
  d.Wheel = function (j) {
    var X = this.zoom + this.zoomStep * (j ? 1 : -1)
    this.zoom = V(this.zoomMax, z(this.zoomMin, X))
    this.Zoom(this.zoom)
  }
  s.tc = {}
  jQuery.fn.tagcanvas = function (X, j) {
    var i,
      Y = j ? jQuery('#' + j) : this
    if (l.all && !j) {
      return false
    }
    i = Y.find('a')
    if (N(window.G_vmlCanvasManager)) {
      this.each(function () {
        I(this)[0] = window.G_vmlCanvasManager.initElement(I(this)[0])
      })
      X.ie = parseFloat(navigator.appVersion.split('MSIE')[1])
    }
    if (
      !i.length ||
      !this[0].getContext ||
      !this[0].getContext('2d').fillText
    ) {
      return false
    }
    this.each(function () {
      var ab,
        Z,
        ad,
        ag,
        ah,
        ac,
        af,
        ae = [],
        aa = { sphere: n, vcylinder: w, hcylinder: F }
      j || (i = I(this).find('a'))
      ac = new s()
      for (ab in X) {
        ac[ab] = X[ab]
      }
      ac.z1 =
        19800 / (Math.exp(ac.depth) * (1 - 1 / Math.E)) +
        20000 -
        19800 / (1 - 1 / Math.E)
      ac.z2 = ac.z1 * (1 / ac.zoom)
      ac.radius =
        ((this.height > this.width ? this.width : this.height) *
          0.33 *
          (ac.z2 + ac.z1)) /
        ac.z1
      ac.yaw = ac.initial ? ac.initial[0] * ac.maxSpeed : 0
      ac.pitch = ac.initial ? ac.initial[1] * ac.maxSpeed : 0
      ac.canvas = I(this)[0]
      ac.ctxt = ac.canvas.getContext('2d')
      ac.textFont = ac.textFont && t(ac.textFont)
      ac.pulsateTo *= 1
      ac.textHeight *= 1
      ac.minBrightness *= 1
      ac.ctxt.textBaseline = 'top'
      if (ac.shadowBlur || ac.shadowOffset[0] || ac.shadowOffset[1]) {
        ac.ctxt.shadowColor = ac.shadow
        ac.shadow = ac.ctxt.shadowColor
        ac.shadowAlpha = v()
      } else {
        delete ac.shadow
      }
      ac.taglist = []
      ac.shape = aa[ac.shape] || aa.sphere
      Z = ac.shape(i.length, ac.radiusX, ac.radiusY, ac.radiusZ)
      ac.shuffleTags && H(Z)
      ac.listLength = i.length
      for (ab = 0; ab < i.length; ++ab) {
        ad = i[ab].getElementsByTagName('img')
        if (ad.length) {
          ag = new Image()
          ag.src = ad[0].src
          ah = new E(ac, ag, i[ab], Z[ab], 1, 1)
          O(ag, ad[0], ah, ac)
        } else {
          ac.taglist.push(
            new E(
              ac,
              i[ab].innerText || i[ab].textContent,
              i[ab],
              Z[ab],
              2,
              ac.textHeight + 2,
              ac.textColour || M(i[ab], 'color'),
              ac.textFont || t(M(i[ab], 'font-family'))
            )
          )
        }
        if (ac.weight) {
          af = x(ac, i[ab])
          if (af > ac.max_weight) {
            ac.max_weight = af
          }
          if (af < ac.min_weight) {
            ac.min_weight = af
          }
          ae.push(af)
        }
      }
      if ((ac.weight = ac.max_weight > ac.min_weight)) {
        for (ab = 0; ab < ac.taglist.length; ++ab) {
          ac.taglist[ab].SetWeight(ae[ab])
        }
      }
      s.tc[I(this)[0].id] = ac
      ac.Tooltip =
        ac.tooltip == 'native'
          ? ac.TooltipNative
          : ac.tooltip
          ? ac.TooltipDiv
          : ac.TooltipNone
      if (ac.tooltip) {
        if (ac.tooltip == 'native') {
          ac.Tooltip = ac.TooltipNative
        } else {
          ac.Tooltip = ac.TooltipDiv
          if (!ac.ttdiv) {
            ac.ttdiv = l.createElement('div')
            ac.ttdiv.className = ac.tooltipClass
            ac.ttdiv.style.position = 'absolute'
            ac.ttdiv.style.zIndex = ac.canvas.style.zIndex + 1
            A(
              'mouseover',
              function (ai) {
                ai.target.style.display = 'none'
              },
              ac.ttdiv
            )
            l.body.appendChild(ac.ttdiv)
          }
        }
      } else {
        ac.Tooltip = ac.TooltipNone
      }
      if (!ac.noMouse && !c[I(this)[0].id]) {
        A('mousemove', L, this)
        A('mouseout', k, this)
        A('mouseup', q, this)
        if (ac.wheelZoom) {
          A('mousewheel', T, this)
          A('DOMMouseScroll', T, this)
        }
        c[I(this)[0].id] = 1
      }
      if (j && ac.hideTags) {
        if (s.loaded) {
          I(Y).hide()
        } else {
          A(
            'load',
            function () {
              I(Y).hide()
            },
            window
          )
        }
      }
      X.interval = X.interval || ac.interval
    })
    return !!(s.started || (s.started = setInterval(o, X.interval)))
  }
  A(
    'load',
    function () {
      s.loaded = 1
    },
    window
  )
})(jQuery)
