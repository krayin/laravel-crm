/**
 * chartjs-chart-funnel
 * https://github.com/sgratzl/chartjs-chart-funnel
 *
 * Copyright (c) 2021 Samuel Gratzl <samu@sgratzl.com>
 */

(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('chart.js'), require('chart.js/helpers')) :
	typeof define === 'function' && define.amd ? define(['exports', 'chart.js', 'chart.js/helpers'], factory) :
	(global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.ChartFunnel = {}, global.Chart, global.Chart.helpers));
})(this, (function (exports, chart_js, helpers) { 'use strict';

	var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

	function getDefaultExportFromCjs (x) {
		return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
	}

	var chroma$1 = {exports: {}};

	/**
	 * chroma.js - JavaScript library for color conversions
	 *
	 * Copyright (c) 2011-2019, Gregor Aisch
	 * All rights reserved.
	 *
	 * Redistribution and use in source and binary forms, with or without
	 * modification, are permitted provided that the following conditions are met:
	 *
	 * 1. Redistributions of source code must retain the above copyright notice, this
	 * list of conditions and the following disclaimer.
	 *
	 * 2. Redistributions in binary form must reproduce the above copyright notice,
	 * this list of conditions and the following disclaimer in the documentation
	 * and/or other materials provided with the distribution.
	 *
	 * 3. The name Gregor Aisch may not be used to endorse or promote products
	 * derived from this software without specific prior written permission.
	 *
	 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
	 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
	 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	 * DISCLAIMED. IN NO EVENT SHALL GREGOR AISCH OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
	 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
	 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
	 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
	 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
	 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
	 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	 *
	 * -------------------------------------------------------
	 *
	 * chroma.js includes colors from colorbrewer2.org, which are released under
	 * the following license:
	 *
	 * Copyright (c) 2002 Cynthia Brewer, Mark Harrower,
	 * and The Pennsylvania State University.
	 *
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 * http://www.apache.org/licenses/LICENSE-2.0
	 *
	 * Unless required by applicable law or agreed to in writing,
	 * software distributed under the License is distributed on an
	 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
	 * either express or implied. See the License for the specific
	 * language governing permissions and limitations under the License.
	 *
	 * ------------------------------------------------------
	 *
	 * Named colors are taken from X11 Color Names.
	 * http://www.w3.org/TR/css3-color/#svg-color
	 *
	 * @preserve
	 */

	(function (module, exports) {
		(function (global, factory) {
		    module.exports = factory() ;
		})(commonjsGlobal, (function () {
		    var limit$2 = function (x, min, max) {
		        if ( min === void 0 ) min=0;
		        if ( max === void 0 ) max=1;

		        return x < min ? min : x > max ? max : x;
		    };

		    var limit$1 = limit$2;

		    var clip_rgb$3 = function (rgb) {
		        rgb._clipped = false;
		        rgb._unclipped = rgb.slice(0);
		        for (var i=0; i<=3; i++) {
		            if (i < 3) {
		                if (rgb[i] < 0 || rgb[i] > 255) { rgb._clipped = true; }
		                rgb[i] = limit$1(rgb[i], 0, 255);
		            } else if (i === 3) {
		                rgb[i] = limit$1(rgb[i], 0, 1);
		            }
		        }
		        return rgb;
		    };

		    // ported from jQuery's $.type
		    var classToType = {};
		    for (var i$1 = 0, list$1 = ['Boolean', 'Number', 'String', 'Function', 'Array', 'Date', 'RegExp', 'Undefined', 'Null']; i$1 < list$1.length; i$1 += 1) {
		        var name = list$1[i$1];

		        classToType[("[object " + name + "]")] = name.toLowerCase();
		    }
		    var type$p = function(obj) {
		        return classToType[Object.prototype.toString.call(obj)] || "object";
		    };

		    var type$o = type$p;

		    var unpack$B = function (args, keyOrder) {
		        if ( keyOrder === void 0 ) keyOrder=null;

		    	// if called with more than 3 arguments, we return the arguments
		        if (args.length >= 3) { return Array.prototype.slice.call(args); }
		        // with less than 3 args we check if first arg is object
		        // and use the keyOrder string to extract and sort properties
		    	if (type$o(args[0]) == 'object' && keyOrder) {
		    		return keyOrder.split('')
		    			.filter(function (k) { return args[0][k] !== undefined; })
		    			.map(function (k) { return args[0][k]; });
		    	}
		    	// otherwise we just return the first argument
		    	// (which we suppose is an array of args)
		        return args[0];
		    };

		    var type$n = type$p;

		    var last$4 = function (args) {
		        if (args.length < 2) { return null; }
		        var l = args.length-1;
		        if (type$n(args[l]) == 'string') { return args[l].toLowerCase(); }
		        return null;
		    };

		    var PI$2 = Math.PI;

		    var utils = {
		    	clip_rgb: clip_rgb$3,
		    	limit: limit$2,
		    	type: type$p,
		    	unpack: unpack$B,
		    	last: last$4,
		    	PI: PI$2,
		    	TWOPI: PI$2*2,
		    	PITHIRD: PI$2/3,
		    	DEG2RAD: PI$2 / 180,
		    	RAD2DEG: 180 / PI$2
		    };

		    var input$h = {
		    	format: {},
		    	autodetect: []
		    };

		    var last$3 = utils.last;
		    var clip_rgb$2 = utils.clip_rgb;
		    var type$m = utils.type;
		    var _input = input$h;

		    var Color$D = function Color() {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var me = this;
		        if (type$m(args[0]) === 'object' &&
		            args[0].constructor &&
		            args[0].constructor === this.constructor) {
		            // the argument is already a Color instance
		            return args[0];
		        }

		        // last argument could be the mode
		        var mode = last$3(args);
		        var autodetect = false;

		        if (!mode) {
		            autodetect = true;
		            if (!_input.sorted) {
		                _input.autodetect = _input.autodetect.sort(function (a,b) { return b.p - a.p; });
		                _input.sorted = true;
		            }
		            // auto-detect format
		            for (var i = 0, list = _input.autodetect; i < list.length; i += 1) {
		                var chk = list[i];

		                mode = chk.test.apply(chk, args);
		                if (mode) { break; }
		            }
		        }

		        if (_input.format[mode]) {
		            var rgb = _input.format[mode].apply(null, autodetect ? args : args.slice(0,-1));
		            me._rgb = clip_rgb$2(rgb);
		        } else {
		            throw new Error('unknown format: '+args);
		        }

		        // add alpha channel
		        if (me._rgb.length === 3) { me._rgb.push(1); }
		    };

		    Color$D.prototype.toString = function toString () {
		        if (type$m(this.hex) == 'function') { return this.hex(); }
		        return ("[" + (this._rgb.join(',')) + "]");
		    };

		    var Color_1 = Color$D;

		    var chroma$k = function () {
		    	var args = [], len = arguments.length;
		    	while ( len-- ) args[ len ] = arguments[ len ];

		    	return new (Function.prototype.bind.apply( chroma$k.Color, [ null ].concat( args) ));
		    };

		    chroma$k.Color = Color_1;
		    chroma$k.version = '2.4.2';

		    var chroma_1 = chroma$k;

		    var unpack$A = utils.unpack;
		    var max$2 = Math.max;

		    var rgb2cmyk$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var ref = unpack$A(args, 'rgb');
		        var r = ref[0];
		        var g = ref[1];
		        var b = ref[2];
		        r = r / 255;
		        g = g / 255;
		        b = b / 255;
		        var k = 1 - max$2(r,max$2(g,b));
		        var f = k < 1 ? 1 / (1-k) : 0;
		        var c = (1-r-k) * f;
		        var m = (1-g-k) * f;
		        var y = (1-b-k) * f;
		        return [c,m,y,k];
		    };

		    var rgb2cmyk_1 = rgb2cmyk$1;

		    var unpack$z = utils.unpack;

		    var cmyk2rgb = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        args = unpack$z(args, 'cmyk');
		        var c = args[0];
		        var m = args[1];
		        var y = args[2];
		        var k = args[3];
		        var alpha = args.length > 4 ? args[4] : 1;
		        if (k === 1) { return [0,0,0,alpha]; }
		        return [
		            c >= 1 ? 0 : 255 * (1-c) * (1-k), // r
		            m >= 1 ? 0 : 255 * (1-m) * (1-k), // g
		            y >= 1 ? 0 : 255 * (1-y) * (1-k), // b
		            alpha
		        ];
		    };

		    var cmyk2rgb_1 = cmyk2rgb;

		    var chroma$j = chroma_1;
		    var Color$C = Color_1;
		    var input$g = input$h;
		    var unpack$y = utils.unpack;
		    var type$l = utils.type;

		    var rgb2cmyk = rgb2cmyk_1;

		    Color$C.prototype.cmyk = function() {
		        return rgb2cmyk(this._rgb);
		    };

		    chroma$j.cmyk = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$C, [ null ].concat( args, ['cmyk']) ));
		    };

		    input$g.format.cmyk = cmyk2rgb_1;

		    input$g.autodetect.push({
		        p: 2,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            args = unpack$y(args, 'cmyk');
		            if (type$l(args) === 'array' && args.length === 4) {
		                return 'cmyk';
		            }
		        }
		    });

		    var unpack$x = utils.unpack;
		    var last$2 = utils.last;
		    var rnd = function (a) { return Math.round(a*100)/100; };

		    /*
		     * supported arguments:
		     * - hsl2css(h,s,l)
		     * - hsl2css(h,s,l,a)
		     * - hsl2css([h,s,l], mode)
		     * - hsl2css([h,s,l,a], mode)
		     * - hsl2css({h,s,l,a}, mode)
		     */
		    var hsl2css$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var hsla = unpack$x(args, 'hsla');
		        var mode = last$2(args) || 'lsa';
		        hsla[0] = rnd(hsla[0] || 0);
		        hsla[1] = rnd(hsla[1]*100) + '%';
		        hsla[2] = rnd(hsla[2]*100) + '%';
		        if (mode === 'hsla' || (hsla.length > 3 && hsla[3]<1)) {
		            hsla[3] = hsla.length > 3 ? hsla[3] : 1;
		            mode = 'hsla';
		        } else {
		            hsla.length = 3;
		        }
		        return (mode + "(" + (hsla.join(',')) + ")");
		    };

		    var hsl2css_1 = hsl2css$1;

		    var unpack$w = utils.unpack;

		    /*
		     * supported arguments:
		     * - rgb2hsl(r,g,b)
		     * - rgb2hsl(r,g,b,a)
		     * - rgb2hsl([r,g,b])
		     * - rgb2hsl([r,g,b,a])
		     * - rgb2hsl({r,g,b,a})
		     */
		    var rgb2hsl$3 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        args = unpack$w(args, 'rgba');
		        var r = args[0];
		        var g = args[1];
		        var b = args[2];

		        r /= 255;
		        g /= 255;
		        b /= 255;

		        var min = Math.min(r, g, b);
		        var max = Math.max(r, g, b);

		        var l = (max + min) / 2;
		        var s, h;

		        if (max === min){
		            s = 0;
		            h = Number.NaN;
		        } else {
		            s = l < 0.5 ? (max - min) / (max + min) : (max - min) / (2 - max - min);
		        }

		        if (r == max) { h = (g - b) / (max - min); }
		        else if (g == max) { h = 2 + (b - r) / (max - min); }
		        else if (b == max) { h = 4 + (r - g) / (max - min); }

		        h *= 60;
		        if (h < 0) { h += 360; }
		        if (args.length>3 && args[3]!==undefined) { return [h,s,l,args[3]]; }
		        return [h,s,l];
		    };

		    var rgb2hsl_1 = rgb2hsl$3;

		    var unpack$v = utils.unpack;
		    var last$1 = utils.last;
		    var hsl2css = hsl2css_1;
		    var rgb2hsl$2 = rgb2hsl_1;
		    var round$6 = Math.round;

		    /*
		     * supported arguments:
		     * - rgb2css(r,g,b)
		     * - rgb2css(r,g,b,a)
		     * - rgb2css([r,g,b], mode)
		     * - rgb2css([r,g,b,a], mode)
		     * - rgb2css({r,g,b,a}, mode)
		     */
		    var rgb2css$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var rgba = unpack$v(args, 'rgba');
		        var mode = last$1(args) || 'rgb';
		        if (mode.substr(0,3) == 'hsl') {
		            return hsl2css(rgb2hsl$2(rgba), mode);
		        }
		        rgba[0] = round$6(rgba[0]);
		        rgba[1] = round$6(rgba[1]);
		        rgba[2] = round$6(rgba[2]);
		        if (mode === 'rgba' || (rgba.length > 3 && rgba[3]<1)) {
		            rgba[3] = rgba.length > 3 ? rgba[3] : 1;
		            mode = 'rgba';
		        }
		        return (mode + "(" + (rgba.slice(0,mode==='rgb'?3:4).join(',')) + ")");
		    };

		    var rgb2css_1 = rgb2css$1;

		    var unpack$u = utils.unpack;
		    var round$5 = Math.round;

		    var hsl2rgb$1 = function () {
		        var assign;

		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];
		        args = unpack$u(args, 'hsl');
		        var h = args[0];
		        var s = args[1];
		        var l = args[2];
		        var r,g,b;
		        if (s === 0) {
		            r = g = b = l*255;
		        } else {
		            var t3 = [0,0,0];
		            var c = [0,0,0];
		            var t2 = l < 0.5 ? l * (1+s) : l+s-l*s;
		            var t1 = 2 * l - t2;
		            var h_ = h / 360;
		            t3[0] = h_ + 1/3;
		            t3[1] = h_;
		            t3[2] = h_ - 1/3;
		            for (var i=0; i<3; i++) {
		                if (t3[i] < 0) { t3[i] += 1; }
		                if (t3[i] > 1) { t3[i] -= 1; }
		                if (6 * t3[i] < 1)
		                    { c[i] = t1 + (t2 - t1) * 6 * t3[i]; }
		                else if (2 * t3[i] < 1)
		                    { c[i] = t2; }
		                else if (3 * t3[i] < 2)
		                    { c[i] = t1 + (t2 - t1) * ((2 / 3) - t3[i]) * 6; }
		                else
		                    { c[i] = t1; }
		            }
		            (assign = [round$5(c[0]*255),round$5(c[1]*255),round$5(c[2]*255)], r = assign[0], g = assign[1], b = assign[2]);
		        }
		        if (args.length > 3) {
		            // keep alpha channel
		            return [r,g,b,args[3]];
		        }
		        return [r,g,b,1];
		    };

		    var hsl2rgb_1 = hsl2rgb$1;

		    var hsl2rgb = hsl2rgb_1;
		    var input$f = input$h;

		    var RE_RGB = /^rgb\(\s*(-?\d+),\s*(-?\d+)\s*,\s*(-?\d+)\s*\)$/;
		    var RE_RGBA = /^rgba\(\s*(-?\d+),\s*(-?\d+)\s*,\s*(-?\d+)\s*,\s*([01]|[01]?\.\d+)\)$/;
		    var RE_RGB_PCT = /^rgb\(\s*(-?\d+(?:\.\d+)?)%,\s*(-?\d+(?:\.\d+)?)%\s*,\s*(-?\d+(?:\.\d+)?)%\s*\)$/;
		    var RE_RGBA_PCT = /^rgba\(\s*(-?\d+(?:\.\d+)?)%,\s*(-?\d+(?:\.\d+)?)%\s*,\s*(-?\d+(?:\.\d+)?)%\s*,\s*([01]|[01]?\.\d+)\)$/;
		    var RE_HSL = /^hsl\(\s*(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)%\s*,\s*(-?\d+(?:\.\d+)?)%\s*\)$/;
		    var RE_HSLA = /^hsla\(\s*(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)%\s*,\s*(-?\d+(?:\.\d+)?)%\s*,\s*([01]|[01]?\.\d+)\)$/;

		    var round$4 = Math.round;

		    var css2rgb$1 = function (css) {
		        css = css.toLowerCase().trim();
		        var m;

		        if (input$f.format.named) {
		            try {
		                return input$f.format.named(css);
		            } catch (e) {
		                // eslint-disable-next-line
		            }
		        }

		        // rgb(250,20,0)
		        if ((m = css.match(RE_RGB))) {
		            var rgb = m.slice(1,4);
		            for (var i=0; i<3; i++) {
		                rgb[i] = +rgb[i];
		            }
		            rgb[3] = 1;  // default alpha
		            return rgb;
		        }

		        // rgba(250,20,0,0.4)
		        if ((m = css.match(RE_RGBA))) {
		            var rgb$1 = m.slice(1,5);
		            for (var i$1=0; i$1<4; i$1++) {
		                rgb$1[i$1] = +rgb$1[i$1];
		            }
		            return rgb$1;
		        }

		        // rgb(100%,0%,0%)
		        if ((m = css.match(RE_RGB_PCT))) {
		            var rgb$2 = m.slice(1,4);
		            for (var i$2=0; i$2<3; i$2++) {
		                rgb$2[i$2] = round$4(rgb$2[i$2] * 2.55);
		            }
		            rgb$2[3] = 1;  // default alpha
		            return rgb$2;
		        }

		        // rgba(100%,0%,0%,0.4)
		        if ((m = css.match(RE_RGBA_PCT))) {
		            var rgb$3 = m.slice(1,5);
		            for (var i$3=0; i$3<3; i$3++) {
		                rgb$3[i$3] = round$4(rgb$3[i$3] * 2.55);
		            }
		            rgb$3[3] = +rgb$3[3];
		            return rgb$3;
		        }

		        // hsl(0,100%,50%)
		        if ((m = css.match(RE_HSL))) {
		            var hsl = m.slice(1,4);
		            hsl[1] *= 0.01;
		            hsl[2] *= 0.01;
		            var rgb$4 = hsl2rgb(hsl);
		            rgb$4[3] = 1;
		            return rgb$4;
		        }

		        // hsla(0,100%,50%,0.5)
		        if ((m = css.match(RE_HSLA))) {
		            var hsl$1 = m.slice(1,4);
		            hsl$1[1] *= 0.01;
		            hsl$1[2] *= 0.01;
		            var rgb$5 = hsl2rgb(hsl$1);
		            rgb$5[3] = +m[4];  // default alpha = 1
		            return rgb$5;
		        }
		    };

		    css2rgb$1.test = function (s) {
		        return RE_RGB.test(s) ||
		            RE_RGBA.test(s) ||
		            RE_RGB_PCT.test(s) ||
		            RE_RGBA_PCT.test(s) ||
		            RE_HSL.test(s) ||
		            RE_HSLA.test(s);
		    };

		    var css2rgb_1 = css2rgb$1;

		    var chroma$i = chroma_1;
		    var Color$B = Color_1;
		    var input$e = input$h;
		    var type$k = utils.type;

		    var rgb2css = rgb2css_1;
		    var css2rgb = css2rgb_1;

		    Color$B.prototype.css = function(mode) {
		        return rgb2css(this._rgb, mode);
		    };

		    chroma$i.css = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$B, [ null ].concat( args, ['css']) ));
		    };

		    input$e.format.css = css2rgb;

		    input$e.autodetect.push({
		        p: 5,
		        test: function (h) {
		            var rest = [], len = arguments.length - 1;
		            while ( len-- > 0 ) rest[ len ] = arguments[ len + 1 ];

		            if (!rest.length && type$k(h) === 'string' && css2rgb.test(h)) {
		                return 'css';
		            }
		        }
		    });

		    var Color$A = Color_1;
		    var chroma$h = chroma_1;
		    var input$d = input$h;
		    var unpack$t = utils.unpack;

		    input$d.format.gl = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var rgb = unpack$t(args, 'rgba');
		        rgb[0] *= 255;
		        rgb[1] *= 255;
		        rgb[2] *= 255;
		        return rgb;
		    };

		    chroma$h.gl = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$A, [ null ].concat( args, ['gl']) ));
		    };

		    Color$A.prototype.gl = function() {
		        var rgb = this._rgb;
		        return [rgb[0]/255, rgb[1]/255, rgb[2]/255, rgb[3]];
		    };

		    var unpack$s = utils.unpack;

		    var rgb2hcg$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var ref = unpack$s(args, 'rgb');
		        var r = ref[0];
		        var g = ref[1];
		        var b = ref[2];
		        var min = Math.min(r, g, b);
		        var max = Math.max(r, g, b);
		        var delta = max - min;
		        var c = delta * 100 / 255;
		        var _g = min / (255 - delta) * 100;
		        var h;
		        if (delta === 0) {
		            h = Number.NaN;
		        } else {
		            if (r === max) { h = (g - b) / delta; }
		            if (g === max) { h = 2+(b - r) / delta; }
		            if (b === max) { h = 4+(r - g) / delta; }
		            h *= 60;
		            if (h < 0) { h += 360; }
		        }
		        return [h, c, _g];
		    };

		    var rgb2hcg_1 = rgb2hcg$1;

		    var unpack$r = utils.unpack;
		    var floor$3 = Math.floor;

		    /*
		     * this is basically just HSV with some minor tweaks
		     *
		     * hue.. [0..360]
		     * chroma .. [0..1]
		     * grayness .. [0..1]
		     */

		    var hcg2rgb = function () {
		        var assign, assign$1, assign$2, assign$3, assign$4, assign$5;

		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];
		        args = unpack$r(args, 'hcg');
		        var h = args[0];
		        var c = args[1];
		        var _g = args[2];
		        var r,g,b;
		        _g = _g * 255;
		        var _c = c * 255;
		        if (c === 0) {
		            r = g = b = _g;
		        } else {
		            if (h === 360) { h = 0; }
		            if (h > 360) { h -= 360; }
		            if (h < 0) { h += 360; }
		            h /= 60;
		            var i = floor$3(h);
		            var f = h - i;
		            var p = _g * (1 - c);
		            var q = p + _c * (1 - f);
		            var t = p + _c * f;
		            var v = p + _c;
		            switch (i) {
		                case 0: (assign = [v, t, p], r = assign[0], g = assign[1], b = assign[2]); break
		                case 1: (assign$1 = [q, v, p], r = assign$1[0], g = assign$1[1], b = assign$1[2]); break
		                case 2: (assign$2 = [p, v, t], r = assign$2[0], g = assign$2[1], b = assign$2[2]); break
		                case 3: (assign$3 = [p, q, v], r = assign$3[0], g = assign$3[1], b = assign$3[2]); break
		                case 4: (assign$4 = [t, p, v], r = assign$4[0], g = assign$4[1], b = assign$4[2]); break
		                case 5: (assign$5 = [v, p, q], r = assign$5[0], g = assign$5[1], b = assign$5[2]); break
		            }
		        }
		        return [r, g, b, args.length > 3 ? args[3] : 1];
		    };

		    var hcg2rgb_1 = hcg2rgb;

		    var unpack$q = utils.unpack;
		    var type$j = utils.type;
		    var chroma$g = chroma_1;
		    var Color$z = Color_1;
		    var input$c = input$h;

		    var rgb2hcg = rgb2hcg_1;

		    Color$z.prototype.hcg = function() {
		        return rgb2hcg(this._rgb);
		    };

		    chroma$g.hcg = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$z, [ null ].concat( args, ['hcg']) ));
		    };

		    input$c.format.hcg = hcg2rgb_1;

		    input$c.autodetect.push({
		        p: 1,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            args = unpack$q(args, 'hcg');
		            if (type$j(args) === 'array' && args.length === 3) {
		                return 'hcg';
		            }
		        }
		    });

		    var unpack$p = utils.unpack;
		    var last = utils.last;
		    var round$3 = Math.round;

		    var rgb2hex$2 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var ref = unpack$p(args, 'rgba');
		        var r = ref[0];
		        var g = ref[1];
		        var b = ref[2];
		        var a = ref[3];
		        var mode = last(args) || 'auto';
		        if (a === undefined) { a = 1; }
		        if (mode === 'auto') {
		            mode = a < 1 ? 'rgba' : 'rgb';
		        }
		        r = round$3(r);
		        g = round$3(g);
		        b = round$3(b);
		        var u = r << 16 | g << 8 | b;
		        var str = "000000" + u.toString(16); //#.toUpperCase();
		        str = str.substr(str.length - 6);
		        var hxa = '0' + round$3(a * 255).toString(16);
		        hxa = hxa.substr(hxa.length - 2);
		        switch (mode.toLowerCase()) {
		            case 'rgba': return ("#" + str + hxa);
		            case 'argb': return ("#" + hxa + str);
		            default: return ("#" + str);
		        }
		    };

		    var rgb2hex_1 = rgb2hex$2;

		    var RE_HEX = /^#?([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
		    var RE_HEXA = /^#?([A-Fa-f0-9]{8}|[A-Fa-f0-9]{4})$/;

		    var hex2rgb$1 = function (hex) {
		        if (hex.match(RE_HEX)) {
		            // remove optional leading #
		            if (hex.length === 4 || hex.length === 7) {
		                hex = hex.substr(1);
		            }
		            // expand short-notation to full six-digit
		            if (hex.length === 3) {
		                hex = hex.split('');
		                hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
		            }
		            var u = parseInt(hex, 16);
		            var r = u >> 16;
		            var g = u >> 8 & 0xFF;
		            var b = u & 0xFF;
		            return [r,g,b,1];
		        }

		        // match rgba hex format, eg #FF000077
		        if (hex.match(RE_HEXA)) {
		            if (hex.length === 5 || hex.length === 9) {
		                // remove optional leading #
		                hex = hex.substr(1);
		            }
		            // expand short-notation to full eight-digit
		            if (hex.length === 4) {
		                hex = hex.split('');
		                hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2]+hex[3]+hex[3];
		            }
		            var u$1 = parseInt(hex, 16);
		            var r$1 = u$1 >> 24 & 0xFF;
		            var g$1 = u$1 >> 16 & 0xFF;
		            var b$1 = u$1 >> 8 & 0xFF;
		            var a = Math.round((u$1 & 0xFF) / 0xFF * 100) / 100;
		            return [r$1,g$1,b$1,a];
		        }

		        // we used to check for css colors here
		        // if _input.css? and rgb = _input.css hex
		        //     return rgb

		        throw new Error(("unknown hex color: " + hex));
		    };

		    var hex2rgb_1 = hex2rgb$1;

		    var chroma$f = chroma_1;
		    var Color$y = Color_1;
		    var type$i = utils.type;
		    var input$b = input$h;

		    var rgb2hex$1 = rgb2hex_1;

		    Color$y.prototype.hex = function(mode) {
		        return rgb2hex$1(this._rgb, mode);
		    };

		    chroma$f.hex = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$y, [ null ].concat( args, ['hex']) ));
		    };

		    input$b.format.hex = hex2rgb_1;
		    input$b.autodetect.push({
		        p: 4,
		        test: function (h) {
		            var rest = [], len = arguments.length - 1;
		            while ( len-- > 0 ) rest[ len ] = arguments[ len + 1 ];

		            if (!rest.length && type$i(h) === 'string' && [3,4,5,6,7,8,9].indexOf(h.length) >= 0) {
		                return 'hex';
		            }
		        }
		    });

		    var unpack$o = utils.unpack;
		    var TWOPI$2 = utils.TWOPI;
		    var min$2 = Math.min;
		    var sqrt$4 = Math.sqrt;
		    var acos = Math.acos;

		    var rgb2hsi$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        /*
		        borrowed from here:
		        http://hummer.stanford.edu/museinfo/doc/examples/humdrum/keyscape2/rgb2hsi.cpp
		        */
		        var ref = unpack$o(args, 'rgb');
		        var r = ref[0];
		        var g = ref[1];
		        var b = ref[2];
		        r /= 255;
		        g /= 255;
		        b /= 255;
		        var h;
		        var min_ = min$2(r,g,b);
		        var i = (r+g+b) / 3;
		        var s = i > 0 ? 1 - min_/i : 0;
		        if (s === 0) {
		            h = NaN;
		        } else {
		            h = ((r-g)+(r-b)) / 2;
		            h /= sqrt$4((r-g)*(r-g) + (r-b)*(g-b));
		            h = acos(h);
		            if (b > g) {
		                h = TWOPI$2 - h;
		            }
		            h /= TWOPI$2;
		        }
		        return [h*360,s,i];
		    };

		    var rgb2hsi_1 = rgb2hsi$1;

		    var unpack$n = utils.unpack;
		    var limit = utils.limit;
		    var TWOPI$1 = utils.TWOPI;
		    var PITHIRD = utils.PITHIRD;
		    var cos$4 = Math.cos;

		    /*
		     * hue [0..360]
		     * saturation [0..1]
		     * intensity [0..1]
		     */
		    var hsi2rgb = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        /*
		        borrowed from here:
		        http://hummer.stanford.edu/museinfo/doc/examples/humdrum/keyscape2/hsi2rgb.cpp
		        */
		        args = unpack$n(args, 'hsi');
		        var h = args[0];
		        var s = args[1];
		        var i = args[2];
		        var r,g,b;

		        if (isNaN(h)) { h = 0; }
		        if (isNaN(s)) { s = 0; }
		        // normalize hue
		        if (h > 360) { h -= 360; }
		        if (h < 0) { h += 360; }
		        h /= 360;
		        if (h < 1/3) {
		            b = (1-s)/3;
		            r = (1+s*cos$4(TWOPI$1*h)/cos$4(PITHIRD-TWOPI$1*h))/3;
		            g = 1 - (b+r);
		        } else if (h < 2/3) {
		            h -= 1/3;
		            r = (1-s)/3;
		            g = (1+s*cos$4(TWOPI$1*h)/cos$4(PITHIRD-TWOPI$1*h))/3;
		            b = 1 - (r+g);
		        } else {
		            h -= 2/3;
		            g = (1-s)/3;
		            b = (1+s*cos$4(TWOPI$1*h)/cos$4(PITHIRD-TWOPI$1*h))/3;
		            r = 1 - (g+b);
		        }
		        r = limit(i*r*3);
		        g = limit(i*g*3);
		        b = limit(i*b*3);
		        return [r*255, g*255, b*255, args.length > 3 ? args[3] : 1];
		    };

		    var hsi2rgb_1 = hsi2rgb;

		    var unpack$m = utils.unpack;
		    var type$h = utils.type;
		    var chroma$e = chroma_1;
		    var Color$x = Color_1;
		    var input$a = input$h;

		    var rgb2hsi = rgb2hsi_1;

		    Color$x.prototype.hsi = function() {
		        return rgb2hsi(this._rgb);
		    };

		    chroma$e.hsi = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$x, [ null ].concat( args, ['hsi']) ));
		    };

		    input$a.format.hsi = hsi2rgb_1;

		    input$a.autodetect.push({
		        p: 2,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            args = unpack$m(args, 'hsi');
		            if (type$h(args) === 'array' && args.length === 3) {
		                return 'hsi';
		            }
		        }
		    });

		    var unpack$l = utils.unpack;
		    var type$g = utils.type;
		    var chroma$d = chroma_1;
		    var Color$w = Color_1;
		    var input$9 = input$h;

		    var rgb2hsl$1 = rgb2hsl_1;

		    Color$w.prototype.hsl = function() {
		        return rgb2hsl$1(this._rgb);
		    };

		    chroma$d.hsl = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$w, [ null ].concat( args, ['hsl']) ));
		    };

		    input$9.format.hsl = hsl2rgb_1;

		    input$9.autodetect.push({
		        p: 2,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            args = unpack$l(args, 'hsl');
		            if (type$g(args) === 'array' && args.length === 3) {
		                return 'hsl';
		            }
		        }
		    });

		    var unpack$k = utils.unpack;
		    var min$1 = Math.min;
		    var max$1 = Math.max;

		    /*
		     * supported arguments:
		     * - rgb2hsv(r,g,b)
		     * - rgb2hsv([r,g,b])
		     * - rgb2hsv({r,g,b})
		     */
		    var rgb2hsl = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        args = unpack$k(args, 'rgb');
		        var r = args[0];
		        var g = args[1];
		        var b = args[2];
		        var min_ = min$1(r, g, b);
		        var max_ = max$1(r, g, b);
		        var delta = max_ - min_;
		        var h,s,v;
		        v = max_ / 255.0;
		        if (max_ === 0) {
		            h = Number.NaN;
		            s = 0;
		        } else {
		            s = delta / max_;
		            if (r === max_) { h = (g - b) / delta; }
		            if (g === max_) { h = 2+(b - r) / delta; }
		            if (b === max_) { h = 4+(r - g) / delta; }
		            h *= 60;
		            if (h < 0) { h += 360; }
		        }
		        return [h, s, v]
		    };

		    var rgb2hsv$1 = rgb2hsl;

		    var unpack$j = utils.unpack;
		    var floor$2 = Math.floor;

		    var hsv2rgb = function () {
		        var assign, assign$1, assign$2, assign$3, assign$4, assign$5;

		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];
		        args = unpack$j(args, 'hsv');
		        var h = args[0];
		        var s = args[1];
		        var v = args[2];
		        var r,g,b;
		        v *= 255;
		        if (s === 0) {
		            r = g = b = v;
		        } else {
		            if (h === 360) { h = 0; }
		            if (h > 360) { h -= 360; }
		            if (h < 0) { h += 360; }
		            h /= 60;

		            var i = floor$2(h);
		            var f = h - i;
		            var p = v * (1 - s);
		            var q = v * (1 - s * f);
		            var t = v * (1 - s * (1 - f));

		            switch (i) {
		                case 0: (assign = [v, t, p], r = assign[0], g = assign[1], b = assign[2]); break
		                case 1: (assign$1 = [q, v, p], r = assign$1[0], g = assign$1[1], b = assign$1[2]); break
		                case 2: (assign$2 = [p, v, t], r = assign$2[0], g = assign$2[1], b = assign$2[2]); break
		                case 3: (assign$3 = [p, q, v], r = assign$3[0], g = assign$3[1], b = assign$3[2]); break
		                case 4: (assign$4 = [t, p, v], r = assign$4[0], g = assign$4[1], b = assign$4[2]); break
		                case 5: (assign$5 = [v, p, q], r = assign$5[0], g = assign$5[1], b = assign$5[2]); break
		            }
		        }
		        return [r,g,b,args.length > 3?args[3]:1];
		    };

		    var hsv2rgb_1 = hsv2rgb;

		    var unpack$i = utils.unpack;
		    var type$f = utils.type;
		    var chroma$c = chroma_1;
		    var Color$v = Color_1;
		    var input$8 = input$h;

		    var rgb2hsv = rgb2hsv$1;

		    Color$v.prototype.hsv = function() {
		        return rgb2hsv(this._rgb);
		    };

		    chroma$c.hsv = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$v, [ null ].concat( args, ['hsv']) ));
		    };

		    input$8.format.hsv = hsv2rgb_1;

		    input$8.autodetect.push({
		        p: 2,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            args = unpack$i(args, 'hsv');
		            if (type$f(args) === 'array' && args.length === 3) {
		                return 'hsv';
		            }
		        }
		    });

		    var labConstants = {
		        // Corresponds roughly to RGB brighter/darker
		        Kn: 18,

		        // D65 standard referent
		        Xn: 0.950470,
		        Yn: 1,
		        Zn: 1.088830,

		        t0: 0.137931034,  // 4 / 29
		        t1: 0.206896552,  // 6 / 29
		        t2: 0.12841855,   // 3 * t1 * t1
		        t3: 0.008856452,  // t1 * t1 * t1
		    };

		    var LAB_CONSTANTS$3 = labConstants;
		    var unpack$h = utils.unpack;
		    var pow$a = Math.pow;

		    var rgb2lab$2 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var ref = unpack$h(args, 'rgb');
		        var r = ref[0];
		        var g = ref[1];
		        var b = ref[2];
		        var ref$1 = rgb2xyz(r,g,b);
		        var x = ref$1[0];
		        var y = ref$1[1];
		        var z = ref$1[2];
		        var l = 116 * y - 16;
		        return [l < 0 ? 0 : l, 500 * (x - y), 200 * (y - z)];
		    };

		    var rgb_xyz = function (r) {
		        if ((r /= 255) <= 0.04045) { return r / 12.92; }
		        return pow$a((r + 0.055) / 1.055, 2.4);
		    };

		    var xyz_lab = function (t) {
		        if (t > LAB_CONSTANTS$3.t3) { return pow$a(t, 1 / 3); }
		        return t / LAB_CONSTANTS$3.t2 + LAB_CONSTANTS$3.t0;
		    };

		    var rgb2xyz = function (r,g,b) {
		        r = rgb_xyz(r);
		        g = rgb_xyz(g);
		        b = rgb_xyz(b);
		        var x = xyz_lab((0.4124564 * r + 0.3575761 * g + 0.1804375 * b) / LAB_CONSTANTS$3.Xn);
		        var y = xyz_lab((0.2126729 * r + 0.7151522 * g + 0.0721750 * b) / LAB_CONSTANTS$3.Yn);
		        var z = xyz_lab((0.0193339 * r + 0.1191920 * g + 0.9503041 * b) / LAB_CONSTANTS$3.Zn);
		        return [x,y,z];
		    };

		    var rgb2lab_1 = rgb2lab$2;

		    var LAB_CONSTANTS$2 = labConstants;
		    var unpack$g = utils.unpack;
		    var pow$9 = Math.pow;

		    /*
		     * L* [0..100]
		     * a [-100..100]
		     * b [-100..100]
		     */
		    var lab2rgb$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        args = unpack$g(args, 'lab');
		        var l = args[0];
		        var a = args[1];
		        var b = args[2];
		        var x,y,z, r,g,b_;

		        y = (l + 16) / 116;
		        x = isNaN(a) ? y : y + a / 500;
		        z = isNaN(b) ? y : y - b / 200;

		        y = LAB_CONSTANTS$2.Yn * lab_xyz(y);
		        x = LAB_CONSTANTS$2.Xn * lab_xyz(x);
		        z = LAB_CONSTANTS$2.Zn * lab_xyz(z);

		        r = xyz_rgb(3.2404542 * x - 1.5371385 * y - 0.4985314 * z);  // D65 -> sRGB
		        g = xyz_rgb(-0.9692660 * x + 1.8760108 * y + 0.0415560 * z);
		        b_ = xyz_rgb(0.0556434 * x - 0.2040259 * y + 1.0572252 * z);

		        return [r,g,b_,args.length > 3 ? args[3] : 1];
		    };

		    var xyz_rgb = function (r) {
		        return 255 * (r <= 0.00304 ? 12.92 * r : 1.055 * pow$9(r, 1 / 2.4) - 0.055)
		    };

		    var lab_xyz = function (t) {
		        return t > LAB_CONSTANTS$2.t1 ? t * t * t : LAB_CONSTANTS$2.t2 * (t - LAB_CONSTANTS$2.t0)
		    };

		    var lab2rgb_1 = lab2rgb$1;

		    var unpack$f = utils.unpack;
		    var type$e = utils.type;
		    var chroma$b = chroma_1;
		    var Color$u = Color_1;
		    var input$7 = input$h;

		    var rgb2lab$1 = rgb2lab_1;

		    Color$u.prototype.lab = function() {
		        return rgb2lab$1(this._rgb);
		    };

		    chroma$b.lab = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$u, [ null ].concat( args, ['lab']) ));
		    };

		    input$7.format.lab = lab2rgb_1;

		    input$7.autodetect.push({
		        p: 2,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            args = unpack$f(args, 'lab');
		            if (type$e(args) === 'array' && args.length === 3) {
		                return 'lab';
		            }
		        }
		    });

		    var unpack$e = utils.unpack;
		    var RAD2DEG = utils.RAD2DEG;
		    var sqrt$3 = Math.sqrt;
		    var atan2$2 = Math.atan2;
		    var round$2 = Math.round;

		    var lab2lch$2 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var ref = unpack$e(args, 'lab');
		        var l = ref[0];
		        var a = ref[1];
		        var b = ref[2];
		        var c = sqrt$3(a * a + b * b);
		        var h = (atan2$2(b, a) * RAD2DEG + 360) % 360;
		        if (round$2(c*10000) === 0) { h = Number.NaN; }
		        return [l, c, h];
		    };

		    var lab2lch_1 = lab2lch$2;

		    var unpack$d = utils.unpack;
		    var rgb2lab = rgb2lab_1;
		    var lab2lch$1 = lab2lch_1;

		    var rgb2lch$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var ref = unpack$d(args, 'rgb');
		        var r = ref[0];
		        var g = ref[1];
		        var b = ref[2];
		        var ref$1 = rgb2lab(r,g,b);
		        var l = ref$1[0];
		        var a = ref$1[1];
		        var b_ = ref$1[2];
		        return lab2lch$1(l,a,b_);
		    };

		    var rgb2lch_1 = rgb2lch$1;

		    var unpack$c = utils.unpack;
		    var DEG2RAD = utils.DEG2RAD;
		    var sin$3 = Math.sin;
		    var cos$3 = Math.cos;

		    var lch2lab$2 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        /*
		        Convert from a qualitative parameter h and a quantitative parameter l to a 24-bit pixel.
		        These formulas were invented by David Dalrymple to obtain maximum contrast without going
		        out of gamut if the parameters are in the range 0-1.

		        A saturation multiplier was added by Gregor Aisch
		        */
		        var ref = unpack$c(args, 'lch');
		        var l = ref[0];
		        var c = ref[1];
		        var h = ref[2];
		        if (isNaN(h)) { h = 0; }
		        h = h * DEG2RAD;
		        return [l, cos$3(h) * c, sin$3(h) * c]
		    };

		    var lch2lab_1 = lch2lab$2;

		    var unpack$b = utils.unpack;
		    var lch2lab$1 = lch2lab_1;
		    var lab2rgb = lab2rgb_1;

		    var lch2rgb$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        args = unpack$b(args, 'lch');
		        var l = args[0];
		        var c = args[1];
		        var h = args[2];
		        var ref = lch2lab$1 (l,c,h);
		        var L = ref[0];
		        var a = ref[1];
		        var b_ = ref[2];
		        var ref$1 = lab2rgb (L,a,b_);
		        var r = ref$1[0];
		        var g = ref$1[1];
		        var b = ref$1[2];
		        return [r, g, b, args.length > 3 ? args[3] : 1];
		    };

		    var lch2rgb_1 = lch2rgb$1;

		    var unpack$a = utils.unpack;
		    var lch2rgb = lch2rgb_1;

		    var hcl2rgb = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var hcl = unpack$a(args, 'hcl').reverse();
		        return lch2rgb.apply(void 0, hcl);
		    };

		    var hcl2rgb_1 = hcl2rgb;

		    var unpack$9 = utils.unpack;
		    var type$d = utils.type;
		    var chroma$a = chroma_1;
		    var Color$t = Color_1;
		    var input$6 = input$h;

		    var rgb2lch = rgb2lch_1;

		    Color$t.prototype.lch = function() { return rgb2lch(this._rgb); };
		    Color$t.prototype.hcl = function() { return rgb2lch(this._rgb).reverse(); };

		    chroma$a.lch = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$t, [ null ].concat( args, ['lch']) ));
		    };
		    chroma$a.hcl = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$t, [ null ].concat( args, ['hcl']) ));
		    };

		    input$6.format.lch = lch2rgb_1;
		    input$6.format.hcl = hcl2rgb_1;

		    ['lch','hcl'].forEach(function (m) { return input$6.autodetect.push({
		        p: 2,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            args = unpack$9(args, m);
		            if (type$d(args) === 'array' && args.length === 3) {
		                return m;
		            }
		        }
		    }); });

		    /**
		    	X11 color names

		    	http://www.w3.org/TR/css3-color/#svg-color
		    */

		    var w3cx11$1 = {
		        aliceblue: '#f0f8ff',
		        antiquewhite: '#faebd7',
		        aqua: '#00ffff',
		        aquamarine: '#7fffd4',
		        azure: '#f0ffff',
		        beige: '#f5f5dc',
		        bisque: '#ffe4c4',
		        black: '#000000',
		        blanchedalmond: '#ffebcd',
		        blue: '#0000ff',
		        blueviolet: '#8a2be2',
		        brown: '#a52a2a',
		        burlywood: '#deb887',
		        cadetblue: '#5f9ea0',
		        chartreuse: '#7fff00',
		        chocolate: '#d2691e',
		        coral: '#ff7f50',
		        cornflower: '#6495ed',
		        cornflowerblue: '#6495ed',
		        cornsilk: '#fff8dc',
		        crimson: '#dc143c',
		        cyan: '#00ffff',
		        darkblue: '#00008b',
		        darkcyan: '#008b8b',
		        darkgoldenrod: '#b8860b',
		        darkgray: '#a9a9a9',
		        darkgreen: '#006400',
		        darkgrey: '#a9a9a9',
		        darkkhaki: '#bdb76b',
		        darkmagenta: '#8b008b',
		        darkolivegreen: '#556b2f',
		        darkorange: '#ff8c00',
		        darkorchid: '#9932cc',
		        darkred: '#8b0000',
		        darksalmon: '#e9967a',
		        darkseagreen: '#8fbc8f',
		        darkslateblue: '#483d8b',
		        darkslategray: '#2f4f4f',
		        darkslategrey: '#2f4f4f',
		        darkturquoise: '#00ced1',
		        darkviolet: '#9400d3',
		        deeppink: '#ff1493',
		        deepskyblue: '#00bfff',
		        dimgray: '#696969',
		        dimgrey: '#696969',
		        dodgerblue: '#1e90ff',
		        firebrick: '#b22222',
		        floralwhite: '#fffaf0',
		        forestgreen: '#228b22',
		        fuchsia: '#ff00ff',
		        gainsboro: '#dcdcdc',
		        ghostwhite: '#f8f8ff',
		        gold: '#ffd700',
		        goldenrod: '#daa520',
		        gray: '#808080',
		        green: '#008000',
		        greenyellow: '#adff2f',
		        grey: '#808080',
		        honeydew: '#f0fff0',
		        hotpink: '#ff69b4',
		        indianred: '#cd5c5c',
		        indigo: '#4b0082',
		        ivory: '#fffff0',
		        khaki: '#f0e68c',
		        laserlemon: '#ffff54',
		        lavender: '#e6e6fa',
		        lavenderblush: '#fff0f5',
		        lawngreen: '#7cfc00',
		        lemonchiffon: '#fffacd',
		        lightblue: '#add8e6',
		        lightcoral: '#f08080',
		        lightcyan: '#e0ffff',
		        lightgoldenrod: '#fafad2',
		        lightgoldenrodyellow: '#fafad2',
		        lightgray: '#d3d3d3',
		        lightgreen: '#90ee90',
		        lightgrey: '#d3d3d3',
		        lightpink: '#ffb6c1',
		        lightsalmon: '#ffa07a',
		        lightseagreen: '#20b2aa',
		        lightskyblue: '#87cefa',
		        lightslategray: '#778899',
		        lightslategrey: '#778899',
		        lightsteelblue: '#b0c4de',
		        lightyellow: '#ffffe0',
		        lime: '#00ff00',
		        limegreen: '#32cd32',
		        linen: '#faf0e6',
		        magenta: '#ff00ff',
		        maroon: '#800000',
		        maroon2: '#7f0000',
		        maroon3: '#b03060',
		        mediumaquamarine: '#66cdaa',
		        mediumblue: '#0000cd',
		        mediumorchid: '#ba55d3',
		        mediumpurple: '#9370db',
		        mediumseagreen: '#3cb371',
		        mediumslateblue: '#7b68ee',
		        mediumspringgreen: '#00fa9a',
		        mediumturquoise: '#48d1cc',
		        mediumvioletred: '#c71585',
		        midnightblue: '#191970',
		        mintcream: '#f5fffa',
		        mistyrose: '#ffe4e1',
		        moccasin: '#ffe4b5',
		        navajowhite: '#ffdead',
		        navy: '#000080',
		        oldlace: '#fdf5e6',
		        olive: '#808000',
		        olivedrab: '#6b8e23',
		        orange: '#ffa500',
		        orangered: '#ff4500',
		        orchid: '#da70d6',
		        palegoldenrod: '#eee8aa',
		        palegreen: '#98fb98',
		        paleturquoise: '#afeeee',
		        palevioletred: '#db7093',
		        papayawhip: '#ffefd5',
		        peachpuff: '#ffdab9',
		        peru: '#cd853f',
		        pink: '#ffc0cb',
		        plum: '#dda0dd',
		        powderblue: '#b0e0e6',
		        purple: '#800080',
		        purple2: '#7f007f',
		        purple3: '#a020f0',
		        rebeccapurple: '#663399',
		        red: '#ff0000',
		        rosybrown: '#bc8f8f',
		        royalblue: '#4169e1',
		        saddlebrown: '#8b4513',
		        salmon: '#fa8072',
		        sandybrown: '#f4a460',
		        seagreen: '#2e8b57',
		        seashell: '#fff5ee',
		        sienna: '#a0522d',
		        silver: '#c0c0c0',
		        skyblue: '#87ceeb',
		        slateblue: '#6a5acd',
		        slategray: '#708090',
		        slategrey: '#708090',
		        snow: '#fffafa',
		        springgreen: '#00ff7f',
		        steelblue: '#4682b4',
		        tan: '#d2b48c',
		        teal: '#008080',
		        thistle: '#d8bfd8',
		        tomato: '#ff6347',
		        turquoise: '#40e0d0',
		        violet: '#ee82ee',
		        wheat: '#f5deb3',
		        white: '#ffffff',
		        whitesmoke: '#f5f5f5',
		        yellow: '#ffff00',
		        yellowgreen: '#9acd32'
		    };

		    var w3cx11_1 = w3cx11$1;

		    var Color$s = Color_1;
		    var input$5 = input$h;
		    var type$c = utils.type;

		    var w3cx11 = w3cx11_1;
		    var hex2rgb = hex2rgb_1;
		    var rgb2hex = rgb2hex_1;

		    Color$s.prototype.name = function() {
		        var hex = rgb2hex(this._rgb, 'rgb');
		        for (var i = 0, list = Object.keys(w3cx11); i < list.length; i += 1) {
		            var n = list[i];

		            if (w3cx11[n] === hex) { return n.toLowerCase(); }
		        }
		        return hex;
		    };

		    input$5.format.named = function (name) {
		        name = name.toLowerCase();
		        if (w3cx11[name]) { return hex2rgb(w3cx11[name]); }
		        throw new Error('unknown color name: '+name);
		    };

		    input$5.autodetect.push({
		        p: 5,
		        test: function (h) {
		            var rest = [], len = arguments.length - 1;
		            while ( len-- > 0 ) rest[ len ] = arguments[ len + 1 ];

		            if (!rest.length && type$c(h) === 'string' && w3cx11[h.toLowerCase()]) {
		                return 'named';
		            }
		        }
		    });

		    var unpack$8 = utils.unpack;

		    var rgb2num$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var ref = unpack$8(args, 'rgb');
		        var r = ref[0];
		        var g = ref[1];
		        var b = ref[2];
		        return (r << 16) + (g << 8) + b;
		    };

		    var rgb2num_1 = rgb2num$1;

		    var type$b = utils.type;

		    var num2rgb = function (num) {
		        if (type$b(num) == "number" && num >= 0 && num <= 0xFFFFFF) {
		            var r = num >> 16;
		            var g = (num >> 8) & 0xFF;
		            var b = num & 0xFF;
		            return [r,g,b,1];
		        }
		        throw new Error("unknown num color: "+num);
		    };

		    var num2rgb_1 = num2rgb;

		    var chroma$9 = chroma_1;
		    var Color$r = Color_1;
		    var input$4 = input$h;
		    var type$a = utils.type;

		    var rgb2num = rgb2num_1;

		    Color$r.prototype.num = function() {
		        return rgb2num(this._rgb);
		    };

		    chroma$9.num = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$r, [ null ].concat( args, ['num']) ));
		    };

		    input$4.format.num = num2rgb_1;

		    input$4.autodetect.push({
		        p: 5,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            if (args.length === 1 && type$a(args[0]) === 'number' && args[0] >= 0 && args[0] <= 0xFFFFFF) {
		                return 'num';
		            }
		        }
		    });

		    var chroma$8 = chroma_1;
		    var Color$q = Color_1;
		    var input$3 = input$h;
		    var unpack$7 = utils.unpack;
		    var type$9 = utils.type;
		    var round$1 = Math.round;

		    Color$q.prototype.rgb = function(rnd) {
		        if ( rnd === void 0 ) rnd=true;

		        if (rnd === false) { return this._rgb.slice(0,3); }
		        return this._rgb.slice(0,3).map(round$1);
		    };

		    Color$q.prototype.rgba = function(rnd) {
		        if ( rnd === void 0 ) rnd=true;

		        return this._rgb.slice(0,4).map(function (v,i) {
		            return i<3 ? (rnd === false ? v : round$1(v)) : v;
		        });
		    };

		    chroma$8.rgb = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$q, [ null ].concat( args, ['rgb']) ));
		    };

		    input$3.format.rgb = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var rgba = unpack$7(args, 'rgba');
		        if (rgba[3] === undefined) { rgba[3] = 1; }
		        return rgba;
		    };

		    input$3.autodetect.push({
		        p: 3,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            args = unpack$7(args, 'rgba');
		            if (type$9(args) === 'array' && (args.length === 3 ||
		                args.length === 4 && type$9(args[3]) == 'number' && args[3] >= 0 && args[3] <= 1)) {
		                return 'rgb';
		            }
		        }
		    });

		    /*
		     * Based on implementation by Neil Bartlett
		     * https://github.com/neilbartlett/color-temperature
		     */

		    var log$1 = Math.log;

		    var temperature2rgb$1 = function (kelvin) {
		        var temp = kelvin / 100;
		        var r,g,b;
		        if (temp < 66) {
		            r = 255;
		            g = temp < 6 ? 0 : -155.25485562709179 - 0.44596950469579133 * (g = temp-2) + 104.49216199393888 * log$1(g);
		            b = temp < 20 ? 0 : -254.76935184120902 + 0.8274096064007395 * (b = temp-10) + 115.67994401066147 * log$1(b);
		        } else {
		            r = 351.97690566805693 + 0.114206453784165 * (r = temp-55) - 40.25366309332127 * log$1(r);
		            g = 325.4494125711974 + 0.07943456536662342 * (g = temp-50) - 28.0852963507957 * log$1(g);
		            b = 255;
		        }
		        return [r,g,b,1];
		    };

		    var temperature2rgb_1 = temperature2rgb$1;

		    /*
		     * Based on implementation by Neil Bartlett
		     * https://github.com/neilbartlett/color-temperature
		     **/

		    var temperature2rgb = temperature2rgb_1;
		    var unpack$6 = utils.unpack;
		    var round = Math.round;

		    var rgb2temperature$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var rgb = unpack$6(args, 'rgb');
		        var r = rgb[0], b = rgb[2];
		        var minTemp = 1000;
		        var maxTemp = 40000;
		        var eps = 0.4;
		        var temp;
		        while (maxTemp - minTemp > eps) {
		            temp = (maxTemp + minTemp) * 0.5;
		            var rgb$1 = temperature2rgb(temp);
		            if ((rgb$1[2] / rgb$1[0]) >= (b / r)) {
		                maxTemp = temp;
		            } else {
		                minTemp = temp;
		            }
		        }
		        return round(temp);
		    };

		    var rgb2temperature_1 = rgb2temperature$1;

		    var chroma$7 = chroma_1;
		    var Color$p = Color_1;
		    var input$2 = input$h;

		    var rgb2temperature = rgb2temperature_1;

		    Color$p.prototype.temp =
		    Color$p.prototype.kelvin =
		    Color$p.prototype.temperature = function() {
		        return rgb2temperature(this._rgb);
		    };

		    chroma$7.temp =
		    chroma$7.kelvin =
		    chroma$7.temperature = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$p, [ null ].concat( args, ['temp']) ));
		    };

		    input$2.format.temp =
		    input$2.format.kelvin =
		    input$2.format.temperature = temperature2rgb_1;

		    var unpack$5 = utils.unpack;
		    var cbrt = Math.cbrt;
		    var pow$8 = Math.pow;
		    var sign$1 = Math.sign;

		    var rgb2oklab$2 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        // OKLab color space implementation taken from
		        // https://bottosson.github.io/posts/oklab/
		        var ref = unpack$5(args, 'rgb');
		        var r = ref[0];
		        var g = ref[1];
		        var b = ref[2];
		        var ref$1 = [rgb2lrgb(r / 255), rgb2lrgb(g / 255), rgb2lrgb(b / 255)];
		        var lr = ref$1[0];
		        var lg = ref$1[1];
		        var lb = ref$1[2];
		        var l = cbrt(0.4122214708 * lr + 0.5363325363 * lg + 0.0514459929 * lb);
		        var m = cbrt(0.2119034982 * lr + 0.6806995451 * lg + 0.1073969566 * lb);
		        var s = cbrt(0.0883024619 * lr + 0.2817188376 * lg + 0.6299787005 * lb);

		        return [
		            0.2104542553 * l + 0.793617785 * m - 0.0040720468 * s,
		            1.9779984951 * l - 2.428592205 * m + 0.4505937099 * s,
		            0.0259040371 * l + 0.7827717662 * m - 0.808675766 * s
		        ];
		    };

		    var rgb2oklab_1 = rgb2oklab$2;

		    function rgb2lrgb(c) {
		        var abs = Math.abs(c);
		        if (abs < 0.04045) {
		            return c / 12.92;
		        }
		        return (sign$1(c) || 1) * pow$8((abs + 0.055) / 1.055, 2.4);
		    }

		    var unpack$4 = utils.unpack;
		    var pow$7 = Math.pow;
		    var sign = Math.sign;

		    /*
		     * L* [0..100]
		     * a [-100..100]
		     * b [-100..100]
		     */
		    var oklab2rgb$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        args = unpack$4(args, 'lab');
		        var L = args[0];
		        var a = args[1];
		        var b = args[2];

		        var l = pow$7(L + 0.3963377774 * a + 0.2158037573 * b, 3);
		        var m = pow$7(L - 0.1055613458 * a - 0.0638541728 * b, 3);
		        var s = pow$7(L - 0.0894841775 * a - 1.291485548 * b, 3);

		        return [
		            255 * lrgb2rgb(+4.0767416621 * l - 3.3077115913 * m + 0.2309699292 * s),
		            255 * lrgb2rgb(-1.2684380046 * l + 2.6097574011 * m - 0.3413193965 * s),
		            255 * lrgb2rgb(-0.0041960863 * l - 0.7034186147 * m + 1.707614701 * s),
		            args.length > 3 ? args[3] : 1
		        ];
		    };

		    var oklab2rgb_1 = oklab2rgb$1;

		    function lrgb2rgb(c) {
		        var abs = Math.abs(c);
		        if (abs > 0.0031308) {
		            return (sign(c) || 1) * (1.055 * pow$7(abs, 1 / 2.4) - 0.055);
		        }
		        return c * 12.92;
		    }

		    var unpack$3 = utils.unpack;
		    var type$8 = utils.type;
		    var chroma$6 = chroma_1;
		    var Color$o = Color_1;
		    var input$1 = input$h;

		    var rgb2oklab$1 = rgb2oklab_1;

		    Color$o.prototype.oklab = function () {
		        return rgb2oklab$1(this._rgb);
		    };

		    chroma$6.oklab = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$o, [ null ].concat( args, ['oklab']) ));
		    };

		    input$1.format.oklab = oklab2rgb_1;

		    input$1.autodetect.push({
		        p: 3,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            args = unpack$3(args, 'oklab');
		            if (type$8(args) === 'array' && args.length === 3) {
		                return 'oklab';
		            }
		        }
		    });

		    var unpack$2 = utils.unpack;
		    var rgb2oklab = rgb2oklab_1;
		    var lab2lch = lab2lch_1;

		    var rgb2oklch$1 = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        var ref = unpack$2(args, 'rgb');
		        var r = ref[0];
		        var g = ref[1];
		        var b = ref[2];
		        var ref$1 = rgb2oklab(r, g, b);
		        var l = ref$1[0];
		        var a = ref$1[1];
		        var b_ = ref$1[2];
		        return lab2lch(l, a, b_);
		    };

		    var rgb2oklch_1 = rgb2oklch$1;

		    var unpack$1 = utils.unpack;
		    var lch2lab = lch2lab_1;
		    var oklab2rgb = oklab2rgb_1;

		    var oklch2rgb = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        args = unpack$1(args, 'lch');
		        var l = args[0];
		        var c = args[1];
		        var h = args[2];
		        var ref = lch2lab(l, c, h);
		        var L = ref[0];
		        var a = ref[1];
		        var b_ = ref[2];
		        var ref$1 = oklab2rgb(L, a, b_);
		        var r = ref$1[0];
		        var g = ref$1[1];
		        var b = ref$1[2];
		        return [r, g, b, args.length > 3 ? args[3] : 1];
		    };

		    var oklch2rgb_1 = oklch2rgb;

		    var unpack = utils.unpack;
		    var type$7 = utils.type;
		    var chroma$5 = chroma_1;
		    var Color$n = Color_1;
		    var input = input$h;

		    var rgb2oklch = rgb2oklch_1;

		    Color$n.prototype.oklch = function () {
		        return rgb2oklch(this._rgb);
		    };

		    chroma$5.oklch = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        return new (Function.prototype.bind.apply( Color$n, [ null ].concat( args, ['oklch']) ));
		    };

		    input.format.oklch = oklch2rgb_1;

		    input.autodetect.push({
		        p: 3,
		        test: function () {
		            var args = [], len = arguments.length;
		            while ( len-- ) args[ len ] = arguments[ len ];

		            args = unpack(args, 'oklch');
		            if (type$7(args) === 'array' && args.length === 3) {
		                return 'oklch';
		            }
		        }
		    });

		    var Color$m = Color_1;
		    var type$6 = utils.type;

		    Color$m.prototype.alpha = function(a, mutate) {
		        if ( mutate === void 0 ) mutate=false;

		        if (a !== undefined && type$6(a) === 'number') {
		            if (mutate) {
		                this._rgb[3] = a;
		                return this;
		            }
		            return new Color$m([this._rgb[0], this._rgb[1], this._rgb[2], a], 'rgb');
		        }
		        return this._rgb[3];
		    };

		    var Color$l = Color_1;

		    Color$l.prototype.clipped = function() {
		        return this._rgb._clipped || false;
		    };

		    var Color$k = Color_1;
		    var LAB_CONSTANTS$1 = labConstants;

		    Color$k.prototype.darken = function(amount) {
		    	if ( amount === void 0 ) amount=1;

		    	var me = this;
		    	var lab = me.lab();
		    	lab[0] -= LAB_CONSTANTS$1.Kn * amount;
		    	return new Color$k(lab, 'lab').alpha(me.alpha(), true);
		    };

		    Color$k.prototype.brighten = function(amount) {
		    	if ( amount === void 0 ) amount=1;

		    	return this.darken(-amount);
		    };

		    Color$k.prototype.darker = Color$k.prototype.darken;
		    Color$k.prototype.brighter = Color$k.prototype.brighten;

		    var Color$j = Color_1;

		    Color$j.prototype.get = function (mc) {
		        var ref = mc.split('.');
		        var mode = ref[0];
		        var channel = ref[1];
		        var src = this[mode]();
		        if (channel) {
		            var i = mode.indexOf(channel) - (mode.substr(0, 2) === 'ok' ? 2 : 0);
		            if (i > -1) { return src[i]; }
		            throw new Error(("unknown channel " + channel + " in mode " + mode));
		        } else {
		            return src;
		        }
		    };

		    var Color$i = Color_1;
		    var type$5 = utils.type;
		    var pow$6 = Math.pow;

		    var EPS = 1e-7;
		    var MAX_ITER = 20;

		    Color$i.prototype.luminance = function(lum) {
		        if (lum !== undefined && type$5(lum) === 'number') {
		            if (lum === 0) {
		                // return pure black
		                return new Color$i([0,0,0,this._rgb[3]], 'rgb');
		            }
		            if (lum === 1) {
		                // return pure white
		                return new Color$i([255,255,255,this._rgb[3]], 'rgb');
		            }
		            // compute new color using...
		            var cur_lum = this.luminance();
		            var mode = 'rgb';
		            var max_iter = MAX_ITER;

		            var test = function (low, high) {
		                var mid = low.interpolate(high, 0.5, mode);
		                var lm = mid.luminance();
		                if (Math.abs(lum - lm) < EPS || !max_iter--) {
		                    // close enough
		                    return mid;
		                }
		                return lm > lum ? test(low, mid) : test(mid, high);
		            };

		            var rgb = (cur_lum > lum ? test(new Color$i([0,0,0]), this) : test(this, new Color$i([255,255,255]))).rgb();
		            return new Color$i(rgb.concat( [this._rgb[3]]));
		        }
		        return rgb2luminance.apply(void 0, (this._rgb).slice(0,3));
		    };


		    var rgb2luminance = function (r,g,b) {
		        // relative luminance
		        // see http://www.w3.org/TR/2008/REC-WCAG20-20081211/#relativeluminancedef
		        r = luminance_x(r);
		        g = luminance_x(g);
		        b = luminance_x(b);
		        return 0.2126 * r + 0.7152 * g + 0.0722 * b;
		    };

		    var luminance_x = function (x) {
		        x /= 255;
		        return x <= 0.03928 ? x/12.92 : pow$6((x+0.055)/1.055, 2.4);
		    };

		    var interpolator$1 = {};

		    var Color$h = Color_1;
		    var type$4 = utils.type;
		    var interpolator = interpolator$1;

		    var mix$1 = function (col1, col2, f) {
		        if ( f === void 0 ) f=0.5;
		        var rest = [], len = arguments.length - 3;
		        while ( len-- > 0 ) rest[ len ] = arguments[ len + 3 ];

		        var mode = rest[0] || 'lrgb';
		        if (!interpolator[mode] && !rest.length) {
		            // fall back to the first supported mode
		            mode = Object.keys(interpolator)[0];
		        }
		        if (!interpolator[mode]) {
		            throw new Error(("interpolation mode " + mode + " is not defined"));
		        }
		        if (type$4(col1) !== 'object') { col1 = new Color$h(col1); }
		        if (type$4(col2) !== 'object') { col2 = new Color$h(col2); }
		        return interpolator[mode](col1, col2, f)
		            .alpha(col1.alpha() + f * (col2.alpha() - col1.alpha()));
		    };

		    var Color$g = Color_1;
		    var mix = mix$1;

		    Color$g.prototype.mix =
		    Color$g.prototype.interpolate = function(col2, f) {
		    	if ( f === void 0 ) f=0.5;
		    	var rest = [], len = arguments.length - 2;
		    	while ( len-- > 0 ) rest[ len ] = arguments[ len + 2 ];

		    	return mix.apply(void 0, [ this, col2, f ].concat( rest ));
		    };

		    var Color$f = Color_1;

		    Color$f.prototype.premultiply = function(mutate) {
		    	if ( mutate === void 0 ) mutate=false;

		    	var rgb = this._rgb;
		    	var a = rgb[3];
		    	if (mutate) {
		    		this._rgb = [rgb[0]*a, rgb[1]*a, rgb[2]*a, a];
		    		return this;
		    	} else {
		    		return new Color$f([rgb[0]*a, rgb[1]*a, rgb[2]*a, a], 'rgb');
		    	}
		    };

		    var Color$e = Color_1;
		    var LAB_CONSTANTS = labConstants;

		    Color$e.prototype.saturate = function(amount) {
		    	if ( amount === void 0 ) amount=1;

		    	var me = this;
		    	var lch = me.lch();
		    	lch[1] += LAB_CONSTANTS.Kn * amount;
		    	if (lch[1] < 0) { lch[1] = 0; }
		    	return new Color$e(lch, 'lch').alpha(me.alpha(), true);
		    };

		    Color$e.prototype.desaturate = function(amount) {
		    	if ( amount === void 0 ) amount=1;

		    	return this.saturate(-amount);
		    };

		    var Color$d = Color_1;
		    var type$3 = utils.type;

		    Color$d.prototype.set = function (mc, value, mutate) {
		        if ( mutate === void 0 ) mutate = false;

		        var ref = mc.split('.');
		        var mode = ref[0];
		        var channel = ref[1];
		        var src = this[mode]();
		        if (channel) {
		            var i = mode.indexOf(channel) - (mode.substr(0, 2) === 'ok' ? 2 : 0);
		            if (i > -1) {
		                if (type$3(value) == 'string') {
		                    switch (value.charAt(0)) {
		                        case '+':
		                            src[i] += +value;
		                            break;
		                        case '-':
		                            src[i] += +value;
		                            break;
		                        case '*':
		                            src[i] *= +value.substr(1);
		                            break;
		                        case '/':
		                            src[i] /= +value.substr(1);
		                            break;
		                        default:
		                            src[i] = +value;
		                    }
		                } else if (type$3(value) === 'number') {
		                    src[i] = value;
		                } else {
		                    throw new Error("unsupported value for Color.set");
		                }
		                var out = new Color$d(src, mode);
		                if (mutate) {
		                    this._rgb = out._rgb;
		                    return this;
		                }
		                return out;
		            }
		            throw new Error(("unknown channel " + channel + " in mode " + mode));
		        } else {
		            return src;
		        }
		    };

		    var Color$c = Color_1;

		    var rgb = function (col1, col2, f) {
		        var xyz0 = col1._rgb;
		        var xyz1 = col2._rgb;
		        return new Color$c(
		            xyz0[0] + f * (xyz1[0]-xyz0[0]),
		            xyz0[1] + f * (xyz1[1]-xyz0[1]),
		            xyz0[2] + f * (xyz1[2]-xyz0[2]),
		            'rgb'
		        )
		    };

		    // register interpolator
		    interpolator$1.rgb = rgb;

		    var Color$b = Color_1;
		    var sqrt$2 = Math.sqrt;
		    var pow$5 = Math.pow;

		    var lrgb = function (col1, col2, f) {
		        var ref = col1._rgb;
		        var x1 = ref[0];
		        var y1 = ref[1];
		        var z1 = ref[2];
		        var ref$1 = col2._rgb;
		        var x2 = ref$1[0];
		        var y2 = ref$1[1];
		        var z2 = ref$1[2];
		        return new Color$b(
		            sqrt$2(pow$5(x1,2) * (1-f) + pow$5(x2,2) * f),
		            sqrt$2(pow$5(y1,2) * (1-f) + pow$5(y2,2) * f),
		            sqrt$2(pow$5(z1,2) * (1-f) + pow$5(z2,2) * f),
		            'rgb'
		        )
		    };

		    // register interpolator
		    interpolator$1.lrgb = lrgb;

		    var Color$a = Color_1;

		    var lab = function (col1, col2, f) {
		        var xyz0 = col1.lab();
		        var xyz1 = col2.lab();
		        return new Color$a(
		            xyz0[0] + f * (xyz1[0]-xyz0[0]),
		            xyz0[1] + f * (xyz1[1]-xyz0[1]),
		            xyz0[2] + f * (xyz1[2]-xyz0[2]),
		            'lab'
		        )
		    };

		    // register interpolator
		    interpolator$1.lab = lab;

		    var Color$9 = Color_1;

		    var _hsx = function (col1, col2, f, m) {
		        var assign, assign$1;

		        var xyz0, xyz1;
		        if (m === 'hsl') {
		            xyz0 = col1.hsl();
		            xyz1 = col2.hsl();
		        } else if (m === 'hsv') {
		            xyz0 = col1.hsv();
		            xyz1 = col2.hsv();
		        } else if (m === 'hcg') {
		            xyz0 = col1.hcg();
		            xyz1 = col2.hcg();
		        } else if (m === 'hsi') {
		            xyz0 = col1.hsi();
		            xyz1 = col2.hsi();
		        } else if (m === 'lch' || m === 'hcl') {
		            m = 'hcl';
		            xyz0 = col1.hcl();
		            xyz1 = col2.hcl();
		        } else if (m === 'oklch') {
		            xyz0 = col1.oklch().reverse();
		            xyz1 = col2.oklch().reverse();
		        }

		        var hue0, hue1, sat0, sat1, lbv0, lbv1;
		        if (m.substr(0, 1) === 'h' || m === 'oklch') {
		            (assign = xyz0, hue0 = assign[0], sat0 = assign[1], lbv0 = assign[2]);
		            (assign$1 = xyz1, hue1 = assign$1[0], sat1 = assign$1[1], lbv1 = assign$1[2]);
		        }

		        var sat, hue, lbv, dh;

		        if (!isNaN(hue0) && !isNaN(hue1)) {
		            // both colors have hue
		            if (hue1 > hue0 && hue1 - hue0 > 180) {
		                dh = hue1 - (hue0 + 360);
		            } else if (hue1 < hue0 && hue0 - hue1 > 180) {
		                dh = hue1 + 360 - hue0;
		            } else {
		                dh = hue1 - hue0;
		            }
		            hue = hue0 + f * dh;
		        } else if (!isNaN(hue0)) {
		            hue = hue0;
		            if ((lbv1 == 1 || lbv1 == 0) && m != 'hsv') { sat = sat0; }
		        } else if (!isNaN(hue1)) {
		            hue = hue1;
		            if ((lbv0 == 1 || lbv0 == 0) && m != 'hsv') { sat = sat1; }
		        } else {
		            hue = Number.NaN;
		        }

		        if (sat === undefined) { sat = sat0 + f * (sat1 - sat0); }
		        lbv = lbv0 + f * (lbv1 - lbv0);
		        return m === 'oklch' ? new Color$9([lbv, sat, hue], m) : new Color$9([hue, sat, lbv], m);
		    };

		    var interpolate_hsx$5 = _hsx;

		    var lch = function (col1, col2, f) {
		    	return interpolate_hsx$5(col1, col2, f, 'lch');
		    };

		    // register interpolator
		    interpolator$1.lch = lch;
		    interpolator$1.hcl = lch;

		    var Color$8 = Color_1;

		    var num = function (col1, col2, f) {
		        var c1 = col1.num();
		        var c2 = col2.num();
		        return new Color$8(c1 + f * (c2-c1), 'num')
		    };

		    // register interpolator
		    interpolator$1.num = num;

		    var interpolate_hsx$4 = _hsx;

		    var hcg = function (col1, col2, f) {
		    	return interpolate_hsx$4(col1, col2, f, 'hcg');
		    };

		    // register interpolator
		    interpolator$1.hcg = hcg;

		    var interpolate_hsx$3 = _hsx;

		    var hsi = function (col1, col2, f) {
		    	return interpolate_hsx$3(col1, col2, f, 'hsi');
		    };

		    // register interpolator
		    interpolator$1.hsi = hsi;

		    var interpolate_hsx$2 = _hsx;

		    var hsl = function (col1, col2, f) {
		    	return interpolate_hsx$2(col1, col2, f, 'hsl');
		    };

		    // register interpolator
		    interpolator$1.hsl = hsl;

		    var interpolate_hsx$1 = _hsx;

		    var hsv = function (col1, col2, f) {
		    	return interpolate_hsx$1(col1, col2, f, 'hsv');
		    };

		    // register interpolator
		    interpolator$1.hsv = hsv;

		    var Color$7 = Color_1;

		    var oklab = function (col1, col2, f) {
		        var xyz0 = col1.oklab();
		        var xyz1 = col2.oklab();
		        return new Color$7(
		            xyz0[0] + f * (xyz1[0] - xyz0[0]),
		            xyz0[1] + f * (xyz1[1] - xyz0[1]),
		            xyz0[2] + f * (xyz1[2] - xyz0[2]),
		            'oklab'
		        );
		    };

		    // register interpolator
		    interpolator$1.oklab = oklab;

		    var interpolate_hsx = _hsx;

		    var oklch = function (col1, col2, f) {
		        return interpolate_hsx(col1, col2, f, 'oklch');
		    };

		    // register interpolator
		    interpolator$1.oklch = oklch;

		    var Color$6 = Color_1;
		    var clip_rgb$1 = utils.clip_rgb;
		    var pow$4 = Math.pow;
		    var sqrt$1 = Math.sqrt;
		    var PI$1 = Math.PI;
		    var cos$2 = Math.cos;
		    var sin$2 = Math.sin;
		    var atan2$1 = Math.atan2;

		    var average = function (colors, mode, weights) {
		        if ( mode === void 0 ) mode='lrgb';
		        if ( weights === void 0 ) weights=null;

		        var l = colors.length;
		        if (!weights) { weights = Array.from(new Array(l)).map(function () { return 1; }); }
		        // normalize weights
		        var k = l / weights.reduce(function(a, b) { return a + b; });
		        weights.forEach(function (w,i) { weights[i] *= k; });
		        // convert colors to Color objects
		        colors = colors.map(function (c) { return new Color$6(c); });
		        if (mode === 'lrgb') {
		            return _average_lrgb(colors, weights)
		        }
		        var first = colors.shift();
		        var xyz = first.get(mode);
		        var cnt = [];
		        var dx = 0;
		        var dy = 0;
		        // initial color
		        for (var i=0; i<xyz.length; i++) {
		            xyz[i] = (xyz[i] || 0) * weights[0];
		            cnt.push(isNaN(xyz[i]) ? 0 : weights[0]);
		            if (mode.charAt(i) === 'h' && !isNaN(xyz[i])) {
		                var A = xyz[i] / 180 * PI$1;
		                dx += cos$2(A) * weights[0];
		                dy += sin$2(A) * weights[0];
		            }
		        }

		        var alpha = first.alpha() * weights[0];
		        colors.forEach(function (c,ci) {
		            var xyz2 = c.get(mode);
		            alpha += c.alpha() * weights[ci+1];
		            for (var i=0; i<xyz.length; i++) {
		                if (!isNaN(xyz2[i])) {
		                    cnt[i] += weights[ci+1];
		                    if (mode.charAt(i) === 'h') {
		                        var A = xyz2[i] / 180 * PI$1;
		                        dx += cos$2(A) * weights[ci+1];
		                        dy += sin$2(A) * weights[ci+1];
		                    } else {
		                        xyz[i] += xyz2[i] * weights[ci+1];
		                    }
		                }
		            }
		        });

		        for (var i$1=0; i$1<xyz.length; i$1++) {
		            if (mode.charAt(i$1) === 'h') {
		                var A$1 = atan2$1(dy / cnt[i$1], dx / cnt[i$1]) / PI$1 * 180;
		                while (A$1 < 0) { A$1 += 360; }
		                while (A$1 >= 360) { A$1 -= 360; }
		                xyz[i$1] = A$1;
		            } else {
		                xyz[i$1] = xyz[i$1]/cnt[i$1];
		            }
		        }
		        alpha /= l;
		        return (new Color$6(xyz, mode)).alpha(alpha > 0.99999 ? 1 : alpha, true);
		    };


		    var _average_lrgb = function (colors, weights) {
		        var l = colors.length;
		        var xyz = [0,0,0,0];
		        for (var i=0; i < colors.length; i++) {
		            var col = colors[i];
		            var f = weights[i] / l;
		            var rgb = col._rgb;
		            xyz[0] += pow$4(rgb[0],2) * f;
		            xyz[1] += pow$4(rgb[1],2) * f;
		            xyz[2] += pow$4(rgb[2],2) * f;
		            xyz[3] += rgb[3] * f;
		        }
		        xyz[0] = sqrt$1(xyz[0]);
		        xyz[1] = sqrt$1(xyz[1]);
		        xyz[2] = sqrt$1(xyz[2]);
		        if (xyz[3] > 0.9999999) { xyz[3] = 1; }
		        return new Color$6(clip_rgb$1(xyz));
		    };

		    // minimal multi-purpose interface

		    // @requires utils color analyze

		    var chroma$4 = chroma_1;
		    var type$2 = utils.type;

		    var pow$3 = Math.pow;

		    var scale$2 = function(colors) {

		        // constructor
		        var _mode = 'rgb';
		        var _nacol = chroma$4('#ccc');
		        var _spread = 0;
		        // const _fixed = false;
		        var _domain = [0, 1];
		        var _pos = [];
		        var _padding = [0,0];
		        var _classes = false;
		        var _colors = [];
		        var _out = false;
		        var _min = 0;
		        var _max = 1;
		        var _correctLightness = false;
		        var _colorCache = {};
		        var _useCache = true;
		        var _gamma = 1;

		        // private methods

		        var setColors = function(colors) {
		            colors = colors || ['#fff', '#000'];
		            if (colors && type$2(colors) === 'string' && chroma$4.brewer &&
		                chroma$4.brewer[colors.toLowerCase()]) {
		                colors = chroma$4.brewer[colors.toLowerCase()];
		            }
		            if (type$2(colors) === 'array') {
		                // handle single color
		                if (colors.length === 1) {
		                    colors = [colors[0], colors[0]];
		                }
		                // make a copy of the colors
		                colors = colors.slice(0);
		                // convert to chroma classes
		                for (var c=0; c<colors.length; c++) {
		                    colors[c] = chroma$4(colors[c]);
		                }
		                // auto-fill color position
		                _pos.length = 0;
		                for (var c$1=0; c$1<colors.length; c$1++) {
		                    _pos.push(c$1/(colors.length-1));
		                }
		            }
		            resetCache();
		            return _colors = colors;
		        };

		        var getClass = function(value) {
		            if (_classes != null) {
		                var n = _classes.length-1;
		                var i = 0;
		                while (i < n && value >= _classes[i]) {
		                    i++;
		                }
		                return i-1;
		            }
		            return 0;
		        };

		        var tMapLightness = function (t) { return t; };
		        var tMapDomain = function (t) { return t; };

		        // const classifyValue = function(value) {
		        //     let val = value;
		        //     if (_classes.length > 2) {
		        //         const n = _classes.length-1;
		        //         const i = getClass(value);
		        //         const minc = _classes[0] + ((_classes[1]-_classes[0]) * (0 + (_spread * 0.5)));  // center of 1st class
		        //         const maxc = _classes[n-1] + ((_classes[n]-_classes[n-1]) * (1 - (_spread * 0.5)));  // center of last class
		        //         val = _min + ((((_classes[i] + ((_classes[i+1] - _classes[i]) * 0.5)) - minc) / (maxc-minc)) * (_max - _min));
		        //     }
		        //     return val;
		        // };

		        var getColor = function(val, bypassMap) {
		            var col, t;
		            if (bypassMap == null) { bypassMap = false; }
		            if (isNaN(val) || (val === null)) { return _nacol; }
		            if (!bypassMap) {
		                if (_classes && (_classes.length > 2)) {
		                    // find the class
		                    var c = getClass(val);
		                    t = c / (_classes.length-2);
		                } else if (_max !== _min) {
		                    // just interpolate between min/max
		                    t = (val - _min) / (_max - _min);
		                } else {
		                    t = 1;
		                }
		            } else {
		                t = val;
		            }

		            // domain map
		            t = tMapDomain(t);

		            if (!bypassMap) {
		                t = tMapLightness(t);  // lightness correction
		            }

		            if (_gamma !== 1) { t = pow$3(t, _gamma); }

		            t = _padding[0] + (t * (1 - _padding[0] - _padding[1]));

		            t = Math.min(1, Math.max(0, t));

		            var k = Math.floor(t * 10000);

		            if (_useCache && _colorCache[k]) {
		                col = _colorCache[k];
		            } else {
		                if (type$2(_colors) === 'array') {
		                    //for i in [0.._pos.length-1]
		                    for (var i=0; i<_pos.length; i++) {
		                        var p = _pos[i];
		                        if (t <= p) {
		                            col = _colors[i];
		                            break;
		                        }
		                        if ((t >= p) && (i === (_pos.length-1))) {
		                            col = _colors[i];
		                            break;
		                        }
		                        if (t > p && t < _pos[i+1]) {
		                            t = (t-p)/(_pos[i+1]-p);
		                            col = chroma$4.interpolate(_colors[i], _colors[i+1], t, _mode);
		                            break;
		                        }
		                    }
		                } else if (type$2(_colors) === 'function') {
		                    col = _colors(t);
		                }
		                if (_useCache) { _colorCache[k] = col; }
		            }
		            return col;
		        };

		        var resetCache = function () { return _colorCache = {}; };

		        setColors(colors);

		        // public interface

		        var f = function(v) {
		            var c = chroma$4(getColor(v));
		            if (_out && c[_out]) { return c[_out](); } else { return c; }
		        };

		        f.classes = function(classes) {
		            if (classes != null) {
		                if (type$2(classes) === 'array') {
		                    _classes = classes;
		                    _domain = [classes[0], classes[classes.length-1]];
		                } else {
		                    var d = chroma$4.analyze(_domain);
		                    if (classes === 0) {
		                        _classes = [d.min, d.max];
		                    } else {
		                        _classes = chroma$4.limits(d, 'e', classes);
		                    }
		                }
		                return f;
		            }
		            return _classes;
		        };


		        f.domain = function(domain) {
		            if (!arguments.length) {
		                return _domain;
		            }
		            _min = domain[0];
		            _max = domain[domain.length-1];
		            _pos = [];
		            var k = _colors.length;
		            if ((domain.length === k) && (_min !== _max)) {
		                // update positions
		                for (var i = 0, list = Array.from(domain); i < list.length; i += 1) {
		                    var d = list[i];

		                  _pos.push((d-_min) / (_max-_min));
		                }
		            } else {
		                for (var c=0; c<k; c++) {
		                    _pos.push(c/(k-1));
		                }
		                if (domain.length > 2) {
		                    // set domain map
		                    var tOut = domain.map(function (d,i) { return i/(domain.length-1); });
		                    var tBreaks = domain.map(function (d) { return (d - _min) / (_max - _min); });
		                    if (!tBreaks.every(function (val, i) { return tOut[i] === val; })) {
		                        tMapDomain = function (t) {
		                            if (t <= 0 || t >= 1) { return t; }
		                            var i = 0;
		                            while (t >= tBreaks[i+1]) { i++; }
		                            var f = (t - tBreaks[i]) / (tBreaks[i+1] - tBreaks[i]);
		                            var out = tOut[i] + f * (tOut[i+1] - tOut[i]);
		                            return out;
		                        };
		                    }

		                }
		            }
		            _domain = [_min, _max];
		            return f;
		        };

		        f.mode = function(_m) {
		            if (!arguments.length) {
		                return _mode;
		            }
		            _mode = _m;
		            resetCache();
		            return f;
		        };

		        f.range = function(colors, _pos) {
		            setColors(colors);
		            return f;
		        };

		        f.out = function(_o) {
		            _out = _o;
		            return f;
		        };

		        f.spread = function(val) {
		            if (!arguments.length) {
		                return _spread;
		            }
		            _spread = val;
		            return f;
		        };

		        f.correctLightness = function(v) {
		            if (v == null) { v = true; }
		            _correctLightness = v;
		            resetCache();
		            if (_correctLightness) {
		                tMapLightness = function(t) {
		                    var L0 = getColor(0, true).lab()[0];
		                    var L1 = getColor(1, true).lab()[0];
		                    var pol = L0 > L1;
		                    var L_actual = getColor(t, true).lab()[0];
		                    var L_ideal = L0 + ((L1 - L0) * t);
		                    var L_diff = L_actual - L_ideal;
		                    var t0 = 0;
		                    var t1 = 1;
		                    var max_iter = 20;
		                    while ((Math.abs(L_diff) > 1e-2) && (max_iter-- > 0)) {
		                        (function() {
		                            if (pol) { L_diff *= -1; }
		                            if (L_diff < 0) {
		                                t0 = t;
		                                t += (t1 - t) * 0.5;
		                            } else {
		                                t1 = t;
		                                t += (t0 - t) * 0.5;
		                            }
		                            L_actual = getColor(t, true).lab()[0];
		                            return L_diff = L_actual - L_ideal;
		                        })();
		                    }
		                    return t;
		                };
		            } else {
		                tMapLightness = function (t) { return t; };
		            }
		            return f;
		        };

		        f.padding = function(p) {
		            if (p != null) {
		                if (type$2(p) === 'number') {
		                    p = [p,p];
		                }
		                _padding = p;
		                return f;
		            } else {
		                return _padding;
		            }
		        };

		        f.colors = function(numColors, out) {
		            // If no arguments are given, return the original colors that were provided
		            if (arguments.length < 2) { out = 'hex'; }
		            var result = [];

		            if (arguments.length === 0) {
		                result = _colors.slice(0);

		            } else if (numColors === 1) {
		                result = [f(0.5)];

		            } else if (numColors > 1) {
		                var dm = _domain[0];
		                var dd = _domain[1] - dm;
		                result = __range__(0, numColors).map(function (i) { return f( dm + ((i/(numColors-1)) * dd) ); });

		            } else { // returns all colors based on the defined classes
		                colors = [];
		                var samples = [];
		                if (_classes && (_classes.length > 2)) {
		                    for (var i = 1, end = _classes.length, asc = 1 <= end; asc ? i < end : i > end; asc ? i++ : i--) {
		                        samples.push((_classes[i-1]+_classes[i])*0.5);
		                    }
		                } else {
		                    samples = _domain;
		                }
		                result = samples.map(function (v) { return f(v); });
		            }

		            if (chroma$4[out]) {
		                result = result.map(function (c) { return c[out](); });
		            }
		            return result;
		        };

		        f.cache = function(c) {
		            if (c != null) {
		                _useCache = c;
		                return f;
		            } else {
		                return _useCache;
		            }
		        };

		        f.gamma = function(g) {
		            if (g != null) {
		                _gamma = g;
		                return f;
		            } else {
		                return _gamma;
		            }
		        };

		        f.nodata = function(d) {
		            if (d != null) {
		                _nacol = chroma$4(d);
		                return f;
		            } else {
		                return _nacol;
		            }
		        };

		        return f;
		    };

		    function __range__(left, right, inclusive) {
		      var range = [];
		      var ascending = left < right;
		      var end = right ;
		      for (var i = left; ascending ? i < end : i > end; ascending ? i++ : i--) {
		        range.push(i);
		      }
		      return range;
		    }

		    //
		    // interpolates between a set of colors uzing a bezier spline
		    //

		    // @requires utils lab
		    var Color$5 = Color_1;

		    var scale$1 = scale$2;

		    // nth row of the pascal triangle
		    var binom_row = function(n) {
		        var row = [1, 1];
		        for (var i = 1; i < n; i++) {
		            var newrow = [1];
		            for (var j = 1; j <= row.length; j++) {
		                newrow[j] = (row[j] || 0) + row[j - 1];
		            }
		            row = newrow;
		        }
		        return row;
		    };

		    var bezier = function(colors) {
		        var assign, assign$1, assign$2;

		        var I, lab0, lab1, lab2;
		        colors = colors.map(function (c) { return new Color$5(c); });
		        if (colors.length === 2) {
		            // linear interpolation
		            (assign = colors.map(function (c) { return c.lab(); }), lab0 = assign[0], lab1 = assign[1]);
		            I = function(t) {
		                var lab = ([0, 1, 2].map(function (i) { return lab0[i] + (t * (lab1[i] - lab0[i])); }));
		                return new Color$5(lab, 'lab');
		            };
		        } else if (colors.length === 3) {
		            // quadratic bezier interpolation
		            (assign$1 = colors.map(function (c) { return c.lab(); }), lab0 = assign$1[0], lab1 = assign$1[1], lab2 = assign$1[2]);
		            I = function(t) {
		                var lab = ([0, 1, 2].map(function (i) { return ((1-t)*(1-t) * lab0[i]) + (2 * (1-t) * t * lab1[i]) + (t * t * lab2[i]); }));
		                return new Color$5(lab, 'lab');
		            };
		        } else if (colors.length === 4) {
		            // cubic bezier interpolation
		            var lab3;
		            (assign$2 = colors.map(function (c) { return c.lab(); }), lab0 = assign$2[0], lab1 = assign$2[1], lab2 = assign$2[2], lab3 = assign$2[3]);
		            I = function(t) {
		                var lab = ([0, 1, 2].map(function (i) { return ((1-t)*(1-t)*(1-t) * lab0[i]) + (3 * (1-t) * (1-t) * t * lab1[i]) + (3 * (1-t) * t * t * lab2[i]) + (t*t*t * lab3[i]); }));
		                return new Color$5(lab, 'lab');
		            };
		        } else if (colors.length >= 5) {
		            // general case (degree n bezier)
		            var labs, row, n;
		            labs = colors.map(function (c) { return c.lab(); });
		            n = colors.length - 1;
		            row = binom_row(n);
		            I = function (t) {
		                var u = 1 - t;
		                var lab = ([0, 1, 2].map(function (i) { return labs.reduce(function (sum, el, j) { return (sum + row[j] * Math.pow( u, (n - j) ) * Math.pow( t, j ) * el[i]); }, 0); }));
		                return new Color$5(lab, 'lab');
		            };
		        } else {
		            throw new RangeError("No point in running bezier with only one color.")
		        }
		        return I;
		    };

		    var bezier_1 = function (colors) {
		        var f = bezier(colors);
		        f.scale = function () { return scale$1(f); };
		        return f;
		    };

		    /*
		     * interpolates between a set of colors uzing a bezier spline
		     * blend mode formulas taken from http://www.venture-ware.com/kevin/coding/lets-learn-math-photoshop-blend-modes/
		     */

		    var chroma$3 = chroma_1;

		    var blend = function (bottom, top, mode) {
		        if (!blend[mode]) {
		            throw new Error('unknown blend mode ' + mode);
		        }
		        return blend[mode](bottom, top);
		    };

		    var blend_f = function (f) { return function (bottom,top) {
		            var c0 = chroma$3(top).rgb();
		            var c1 = chroma$3(bottom).rgb();
		            return chroma$3.rgb(f(c0, c1));
		        }; };

		    var each = function (f) { return function (c0, c1) {
		            var out = [];
		            out[0] = f(c0[0], c1[0]);
		            out[1] = f(c0[1], c1[1]);
		            out[2] = f(c0[2], c1[2]);
		            return out;
		        }; };

		    var normal = function (a) { return a; };
		    var multiply = function (a,b) { return a * b / 255; };
		    var darken = function (a,b) { return a > b ? b : a; };
		    var lighten = function (a,b) { return a > b ? a : b; };
		    var screen = function (a,b) { return 255 * (1 - (1-a/255) * (1-b/255)); };
		    var overlay = function (a,b) { return b < 128 ? 2 * a * b / 255 : 255 * (1 - 2 * (1 - a / 255 ) * ( 1 - b / 255 )); };
		    var burn = function (a,b) { return 255 * (1 - (1 - b / 255) / (a/255)); };
		    var dodge = function (a,b) {
		        if (a === 255) { return 255; }
		        a = 255 * (b / 255) / (1 - a / 255);
		        return a > 255 ? 255 : a
		    };

		    // # add = (a,b) ->
		    // #     if (a + b > 255) then 255 else a + b

		    blend.normal = blend_f(each(normal));
		    blend.multiply = blend_f(each(multiply));
		    blend.screen = blend_f(each(screen));
		    blend.overlay = blend_f(each(overlay));
		    blend.darken = blend_f(each(darken));
		    blend.lighten = blend_f(each(lighten));
		    blend.dodge = blend_f(each(dodge));
		    blend.burn = blend_f(each(burn));
		    // blend.add = blend_f(each(add));

		    var blend_1 = blend;

		    // cubehelix interpolation
		    // based on D.A. Green "A colour scheme for the display of astronomical intensity images"
		    // http://astron-soc.in/bulletin/11June/289392011.pdf

		    var type$1 = utils.type;
		    var clip_rgb = utils.clip_rgb;
		    var TWOPI = utils.TWOPI;
		    var pow$2 = Math.pow;
		    var sin$1 = Math.sin;
		    var cos$1 = Math.cos;
		    var chroma$2 = chroma_1;

		    var cubehelix = function(start, rotations, hue, gamma, lightness) {
		        if ( start === void 0 ) start=300;
		        if ( rotations === void 0 ) rotations=-1.5;
		        if ( hue === void 0 ) hue=1;
		        if ( gamma === void 0 ) gamma=1;
		        if ( lightness === void 0 ) lightness=[0,1];

		        var dh = 0, dl;
		        if (type$1(lightness) === 'array') {
		            dl = lightness[1] - lightness[0];
		        } else {
		            dl = 0;
		            lightness = [lightness, lightness];
		        }

		        var f = function(fract) {
		            var a = TWOPI * (((start+120)/360) + (rotations * fract));
		            var l = pow$2(lightness[0] + (dl * fract), gamma);
		            var h = dh !== 0 ? hue[0] + (fract * dh) : hue;
		            var amp = (h * l * (1-l)) / 2;
		            var cos_a = cos$1(a);
		            var sin_a = sin$1(a);
		            var r = l + (amp * ((-0.14861 * cos_a) + (1.78277* sin_a)));
		            var g = l + (amp * ((-0.29227 * cos_a) - (0.90649* sin_a)));
		            var b = l + (amp * (+1.97294 * cos_a));
		            return chroma$2(clip_rgb([r*255,g*255,b*255,1]));
		        };

		        f.start = function(s) {
		            if ((s == null)) { return start; }
		            start = s;
		            return f;
		        };

		        f.rotations = function(r) {
		            if ((r == null)) { return rotations; }
		            rotations = r;
		            return f;
		        };

		        f.gamma = function(g) {
		            if ((g == null)) { return gamma; }
		            gamma = g;
		            return f;
		        };

		        f.hue = function(h) {
		            if ((h == null)) { return hue; }
		            hue = h;
		            if (type$1(hue) === 'array') {
		                dh = hue[1] - hue[0];
		                if (dh === 0) { hue = hue[1]; }
		            } else {
		                dh = 0;
		            }
		            return f;
		        };

		        f.lightness = function(h) {
		            if ((h == null)) { return lightness; }
		            if (type$1(h) === 'array') {
		                lightness = h;
		                dl = h[1] - h[0];
		            } else {
		                lightness = [h,h];
		                dl = 0;
		            }
		            return f;
		        };

		        f.scale = function () { return chroma$2.scale(f); };

		        f.hue(hue);

		        return f;
		    };

		    var Color$4 = Color_1;
		    var digits = '0123456789abcdef';

		    var floor$1 = Math.floor;
		    var random = Math.random;

		    var random_1 = function () {
		        var code = '#';
		        for (var i=0; i<6; i++) {
		            code += digits.charAt(floor$1(random() * 16));
		        }
		        return new Color$4(code, 'hex');
		    };

		    var type = type$p;
		    var log = Math.log;
		    var pow$1 = Math.pow;
		    var floor = Math.floor;
		    var abs$1 = Math.abs;


		    var analyze = function (data, key) {
		        if ( key === void 0 ) key=null;

		        var r = {
		            min: Number.MAX_VALUE,
		            max: Number.MAX_VALUE*-1,
		            sum: 0,
		            values: [],
		            count: 0
		        };
		        if (type(data) === 'object') {
		            data = Object.values(data);
		        }
		        data.forEach(function (val) {
		            if (key && type(val) === 'object') { val = val[key]; }
		            if (val !== undefined && val !== null && !isNaN(val)) {
		                r.values.push(val);
		                r.sum += val;
		                if (val < r.min) { r.min = val; }
		                if (val > r.max) { r.max = val; }
		                r.count += 1;
		            }
		        });

		        r.domain = [r.min, r.max];

		        r.limits = function (mode, num) { return limits(r, mode, num); };

		        return r;
		    };


		    var limits = function (data, mode, num) {
		        if ( mode === void 0 ) mode='equal';
		        if ( num === void 0 ) num=7;

		        if (type(data) == 'array') {
		            data = analyze(data);
		        }
		        var min = data.min;
		        var max = data.max;
		        var values = data.values.sort(function (a,b) { return a-b; });

		        if (num === 1) { return [min,max]; }

		        var limits = [];

		        if (mode.substr(0,1) === 'c') { // continuous
		            limits.push(min);
		            limits.push(max);
		        }

		        if (mode.substr(0,1) === 'e') { // equal interval
		            limits.push(min);
		            for (var i=1; i<num; i++) {
		                limits.push(min+((i/num)*(max-min)));
		            }
		            limits.push(max);
		        }

		        else if (mode.substr(0,1) === 'l') { // log scale
		            if (min <= 0) {
		                throw new Error('Logarithmic scales are only possible for values > 0');
		            }
		            var min_log = Math.LOG10E * log(min);
		            var max_log = Math.LOG10E * log(max);
		            limits.push(min);
		            for (var i$1=1; i$1<num; i$1++) {
		                limits.push(pow$1(10, min_log + ((i$1/num) * (max_log - min_log))));
		            }
		            limits.push(max);
		        }

		        else if (mode.substr(0,1) === 'q') { // quantile scale
		            limits.push(min);
		            for (var i$2=1; i$2<num; i$2++) {
		                var p = ((values.length-1) * i$2)/num;
		                var pb = floor(p);
		                if (pb === p) {
		                    limits.push(values[pb]);
		                } else { // p > pb
		                    var pr = p - pb;
		                    limits.push((values[pb]*(1-pr)) + (values[pb+1]*pr));
		                }
		            }
		            limits.push(max);

		        }

		        else if (mode.substr(0,1) === 'k') { // k-means clustering
		            /*
		            implementation based on
		            http://code.google.com/p/figue/source/browse/trunk/figue.js#336
		            simplified for 1-d input values
		            */
		            var cluster;
		            var n = values.length;
		            var assignments = new Array(n);
		            var clusterSizes = new Array(num);
		            var repeat = true;
		            var nb_iters = 0;
		            var centroids = null;

		            // get seed values
		            centroids = [];
		            centroids.push(min);
		            for (var i$3=1; i$3<num; i$3++) {
		                centroids.push(min + ((i$3/num) * (max-min)));
		            }
		            centroids.push(max);

		            while (repeat) {
		                // assignment step
		                for (var j=0; j<num; j++) {
		                    clusterSizes[j] = 0;
		                }
		                for (var i$4=0; i$4<n; i$4++) {
		                    var value = values[i$4];
		                    var mindist = Number.MAX_VALUE;
		                    var best = (void 0);
		                    for (var j$1=0; j$1<num; j$1++) {
		                        var dist = abs$1(centroids[j$1]-value);
		                        if (dist < mindist) {
		                            mindist = dist;
		                            best = j$1;
		                        }
		                        clusterSizes[best]++;
		                        assignments[i$4] = best;
		                    }
		                }

		                // update centroids step
		                var newCentroids = new Array(num);
		                for (var j$2=0; j$2<num; j$2++) {
		                    newCentroids[j$2] = null;
		                }
		                for (var i$5=0; i$5<n; i$5++) {
		                    cluster = assignments[i$5];
		                    if (newCentroids[cluster] === null) {
		                        newCentroids[cluster] = values[i$5];
		                    } else {
		                        newCentroids[cluster] += values[i$5];
		                    }
		                }
		                for (var j$3=0; j$3<num; j$3++) {
		                    newCentroids[j$3] *= 1/clusterSizes[j$3];
		                }

		                // check convergence
		                repeat = false;
		                for (var j$4=0; j$4<num; j$4++) {
		                    if (newCentroids[j$4] !== centroids[j$4]) {
		                        repeat = true;
		                        break;
		                    }
		                }

		                centroids = newCentroids;
		                nb_iters++;

		                if (nb_iters > 200) {
		                    repeat = false;
		                }
		            }

		            // finished k-means clustering
		            // the next part is borrowed from gabrielflor.it
		            var kClusters = {};
		            for (var j$5=0; j$5<num; j$5++) {
		                kClusters[j$5] = [];
		            }
		            for (var i$6=0; i$6<n; i$6++) {
		                cluster = assignments[i$6];
		                kClusters[cluster].push(values[i$6]);
		            }
		            var tmpKMeansBreaks = [];
		            for (var j$6=0; j$6<num; j$6++) {
		                tmpKMeansBreaks.push(kClusters[j$6][0]);
		                tmpKMeansBreaks.push(kClusters[j$6][kClusters[j$6].length-1]);
		            }
		            tmpKMeansBreaks = tmpKMeansBreaks.sort(function (a,b){ return a-b; });
		            limits.push(tmpKMeansBreaks[0]);
		            for (var i$7=1; i$7 < tmpKMeansBreaks.length; i$7+= 2) {
		                var v = tmpKMeansBreaks[i$7];
		                if (!isNaN(v) && (limits.indexOf(v) === -1)) {
		                    limits.push(v);
		                }
		            }
		        }
		        return limits;
		    };

		    var analyze_1 = {analyze: analyze, limits: limits};

		    var Color$3 = Color_1;


		    var contrast = function (a, b) {
		        // WCAG contrast ratio
		        // see http://www.w3.org/TR/2008/REC-WCAG20-20081211/#contrast-ratiodef
		        a = new Color$3(a);
		        b = new Color$3(b);
		        var l1 = a.luminance();
		        var l2 = b.luminance();
		        return l1 > l2 ? (l1 + 0.05) / (l2 + 0.05) : (l2 + 0.05) / (l1 + 0.05);
		    };

		    var Color$2 = Color_1;
		    var sqrt = Math.sqrt;
		    var pow = Math.pow;
		    var min = Math.min;
		    var max = Math.max;
		    var atan2 = Math.atan2;
		    var abs = Math.abs;
		    var cos = Math.cos;
		    var sin = Math.sin;
		    var exp = Math.exp;
		    var PI = Math.PI;

		    var deltaE = function(a, b, Kl, Kc, Kh) {
		        if ( Kl === void 0 ) Kl=1;
		        if ( Kc === void 0 ) Kc=1;
		        if ( Kh === void 0 ) Kh=1;

		        // Delta E (CIE 2000)
		        // see http://www.brucelindbloom.com/index.html?Eqn_DeltaE_CIE2000.html
		        var rad2deg = function(rad) {
		            return 360 * rad / (2 * PI);
		        };
		        var deg2rad = function(deg) {
		            return (2 * PI * deg) / 360;
		        };
		        a = new Color$2(a);
		        b = new Color$2(b);
		        var ref = Array.from(a.lab());
		        var L1 = ref[0];
		        var a1 = ref[1];
		        var b1 = ref[2];
		        var ref$1 = Array.from(b.lab());
		        var L2 = ref$1[0];
		        var a2 = ref$1[1];
		        var b2 = ref$1[2];
		        var avgL = (L1 + L2)/2;
		        var C1 = sqrt(pow(a1, 2) + pow(b1, 2));
		        var C2 = sqrt(pow(a2, 2) + pow(b2, 2));
		        var avgC = (C1 + C2)/2;
		        var G = 0.5*(1-sqrt(pow(avgC, 7)/(pow(avgC, 7) + pow(25, 7))));
		        var a1p = a1*(1+G);
		        var a2p = a2*(1+G);
		        var C1p = sqrt(pow(a1p, 2) + pow(b1, 2));
		        var C2p = sqrt(pow(a2p, 2) + pow(b2, 2));
		        var avgCp = (C1p + C2p)/2;
		        var arctan1 = rad2deg(atan2(b1, a1p));
		        var arctan2 = rad2deg(atan2(b2, a2p));
		        var h1p = arctan1 >= 0 ? arctan1 : arctan1 + 360;
		        var h2p = arctan2 >= 0 ? arctan2 : arctan2 + 360;
		        var avgHp = abs(h1p - h2p) > 180 ? (h1p + h2p + 360)/2 : (h1p + h2p)/2;
		        var T = 1 - 0.17*cos(deg2rad(avgHp - 30)) + 0.24*cos(deg2rad(2*avgHp)) + 0.32*cos(deg2rad(3*avgHp + 6)) - 0.2*cos(deg2rad(4*avgHp - 63));
		        var deltaHp = h2p - h1p;
		        deltaHp = abs(deltaHp) <= 180 ? deltaHp : h2p <= h1p ? deltaHp + 360 : deltaHp - 360;
		        deltaHp = 2*sqrt(C1p*C2p)*sin(deg2rad(deltaHp)/2);
		        var deltaL = L2 - L1;
		        var deltaCp = C2p - C1p;    
		        var sl = 1 + (0.015*pow(avgL - 50, 2))/sqrt(20 + pow(avgL - 50, 2));
		        var sc = 1 + 0.045*avgCp;
		        var sh = 1 + 0.015*avgCp*T;
		        var deltaTheta = 30*exp(-pow((avgHp - 275)/25, 2));
		        var Rc = 2*sqrt(pow(avgCp, 7)/(pow(avgCp, 7) + pow(25, 7)));
		        var Rt = -Rc*sin(2*deg2rad(deltaTheta));
		        var result = sqrt(pow(deltaL/(Kl*sl), 2) + pow(deltaCp/(Kc*sc), 2) + pow(deltaHp/(Kh*sh), 2) + Rt*(deltaCp/(Kc*sc))*(deltaHp/(Kh*sh)));
		        return max(0, min(100, result));
		    };

		    var Color$1 = Color_1;

		    // simple Euclidean distance
		    var distance = function(a, b, mode) {
		        if ( mode === void 0 ) mode='lab';

		        // Delta E (CIE 1976)
		        // see http://www.brucelindbloom.com/index.html?Equations.html
		        a = new Color$1(a);
		        b = new Color$1(b);
		        var l1 = a.get(mode);
		        var l2 = b.get(mode);
		        var sum_sq = 0;
		        for (var i in l1) {
		            var d = (l1[i] || 0) - (l2[i] || 0);
		            sum_sq += d*d;
		        }
		        return Math.sqrt(sum_sq);
		    };

		    var Color = Color_1;

		    var valid = function () {
		        var args = [], len = arguments.length;
		        while ( len-- ) args[ len ] = arguments[ len ];

		        try {
		            new (Function.prototype.bind.apply( Color, [ null ].concat( args) ));
		            return true;
		        } catch (e) {
		            return false;
		        }
		    };

		    // some pre-defined color scales:
		    var chroma$1 = chroma_1;

		    var scale = scale$2;

		    var scales = {
		    	cool: function cool() { return scale([chroma$1.hsl(180,1,.9), chroma$1.hsl(250,.7,.4)]) },
		    	hot: function hot() { return scale(['#000','#f00','#ff0','#fff']).mode('rgb') }
		    };

		    /**
		        ColorBrewer colors for chroma.js

		        Copyright (c) 2002 Cynthia Brewer, Mark Harrower, and The
		        Pennsylvania State University.

		        Licensed under the Apache License, Version 2.0 (the "License");
		        you may not use this file except in compliance with the License.
		        You may obtain a copy of the License at
		        http://www.apache.org/licenses/LICENSE-2.0

		        Unless required by applicable law or agreed to in writing, software distributed
		        under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
		        CONDITIONS OF ANY KIND, either express or implied. See the License for the
		        specific language governing permissions and limitations under the License.
		    */

		    var colorbrewer = {
		        // sequential
		        OrRd: ['#fff7ec', '#fee8c8', '#fdd49e', '#fdbb84', '#fc8d59', '#ef6548', '#d7301f', '#b30000', '#7f0000'],
		        PuBu: ['#fff7fb', '#ece7f2', '#d0d1e6', '#a6bddb', '#74a9cf', '#3690c0', '#0570b0', '#045a8d', '#023858'],
		        BuPu: ['#f7fcfd', '#e0ecf4', '#bfd3e6', '#9ebcda', '#8c96c6', '#8c6bb1', '#88419d', '#810f7c', '#4d004b'],
		        Oranges: ['#fff5eb', '#fee6ce', '#fdd0a2', '#fdae6b', '#fd8d3c', '#f16913', '#d94801', '#a63603', '#7f2704'],
		        BuGn: ['#f7fcfd', '#e5f5f9', '#ccece6', '#99d8c9', '#66c2a4', '#41ae76', '#238b45', '#006d2c', '#00441b'],
		        YlOrBr: ['#ffffe5', '#fff7bc', '#fee391', '#fec44f', '#fe9929', '#ec7014', '#cc4c02', '#993404', '#662506'],
		        YlGn: ['#ffffe5', '#f7fcb9', '#d9f0a3', '#addd8e', '#78c679', '#41ab5d', '#238443', '#006837', '#004529'],
		        Reds: ['#fff5f0', '#fee0d2', '#fcbba1', '#fc9272', '#fb6a4a', '#ef3b2c', '#cb181d', '#a50f15', '#67000d'],
		        RdPu: ['#fff7f3', '#fde0dd', '#fcc5c0', '#fa9fb5', '#f768a1', '#dd3497', '#ae017e', '#7a0177', '#49006a'],
		        Greens: ['#f7fcf5', '#e5f5e0', '#c7e9c0', '#a1d99b', '#74c476', '#41ab5d', '#238b45', '#006d2c', '#00441b'],
		        YlGnBu: ['#ffffd9', '#edf8b1', '#c7e9b4', '#7fcdbb', '#41b6c4', '#1d91c0', '#225ea8', '#253494', '#081d58'],
		        Purples: ['#fcfbfd', '#efedf5', '#dadaeb', '#bcbddc', '#9e9ac8', '#807dba', '#6a51a3', '#54278f', '#3f007d'],
		        GnBu: ['#f7fcf0', '#e0f3db', '#ccebc5', '#a8ddb5', '#7bccc4', '#4eb3d3', '#2b8cbe', '#0868ac', '#084081'],
		        Greys: ['#ffffff', '#f0f0f0', '#d9d9d9', '#bdbdbd', '#969696', '#737373', '#525252', '#252525', '#000000'],
		        YlOrRd: ['#ffffcc', '#ffeda0', '#fed976', '#feb24c', '#fd8d3c', '#fc4e2a', '#e31a1c', '#bd0026', '#800026'],
		        PuRd: ['#f7f4f9', '#e7e1ef', '#d4b9da', '#c994c7', '#df65b0', '#e7298a', '#ce1256', '#980043', '#67001f'],
		        Blues: ['#f7fbff', '#deebf7', '#c6dbef', '#9ecae1', '#6baed6', '#4292c6', '#2171b5', '#08519c', '#08306b'],
		        PuBuGn: ['#fff7fb', '#ece2f0', '#d0d1e6', '#a6bddb', '#67a9cf', '#3690c0', '#02818a', '#016c59', '#014636'],
		        Viridis: ['#440154', '#482777', '#3f4a8a', '#31678e', '#26838f', '#1f9d8a', '#6cce5a', '#b6de2b', '#fee825'],

		        // diverging

		        Spectral: ['#9e0142', '#d53e4f', '#f46d43', '#fdae61', '#fee08b', '#ffffbf', '#e6f598', '#abdda4', '#66c2a5', '#3288bd', '#5e4fa2'],
		        RdYlGn: ['#a50026', '#d73027', '#f46d43', '#fdae61', '#fee08b', '#ffffbf', '#d9ef8b', '#a6d96a', '#66bd63', '#1a9850', '#006837'],
		        RdBu: ['#67001f', '#b2182b', '#d6604d', '#f4a582', '#fddbc7', '#f7f7f7', '#d1e5f0', '#92c5de', '#4393c3', '#2166ac', '#053061'],
		        PiYG: ['#8e0152', '#c51b7d', '#de77ae', '#f1b6da', '#fde0ef', '#f7f7f7', '#e6f5d0', '#b8e186', '#7fbc41', '#4d9221', '#276419'],
		        PRGn: ['#40004b', '#762a83', '#9970ab', '#c2a5cf', '#e7d4e8', '#f7f7f7', '#d9f0d3', '#a6dba0', '#5aae61', '#1b7837', '#00441b'],
		        RdYlBu: ['#a50026', '#d73027', '#f46d43', '#fdae61', '#fee090', '#ffffbf', '#e0f3f8', '#abd9e9', '#74add1', '#4575b4', '#313695'],
		        BrBG: ['#543005', '#8c510a', '#bf812d', '#dfc27d', '#f6e8c3', '#f5f5f5', '#c7eae5', '#80cdc1', '#35978f', '#01665e', '#003c30'],
		        RdGy: ['#67001f', '#b2182b', '#d6604d', '#f4a582', '#fddbc7', '#ffffff', '#e0e0e0', '#bababa', '#878787', '#4d4d4d', '#1a1a1a'],
		        PuOr: ['#7f3b08', '#b35806', '#e08214', '#fdb863', '#fee0b6', '#f7f7f7', '#d8daeb', '#b2abd2', '#8073ac', '#542788', '#2d004b'],

		        // qualitative

		        Set2: ['#66c2a5', '#fc8d62', '#8da0cb', '#e78ac3', '#a6d854', '#ffd92f', '#e5c494', '#b3b3b3'],
		        Accent: ['#7fc97f', '#beaed4', '#fdc086', '#ffff99', '#386cb0', '#f0027f', '#bf5b17', '#666666'],
		        Set1: ['#e41a1c', '#377eb8', '#4daf4a', '#984ea3', '#ff7f00', '#ffff33', '#a65628', '#f781bf', '#999999'],
		        Set3: ['#8dd3c7', '#ffffb3', '#bebada', '#fb8072', '#80b1d3', '#fdb462', '#b3de69', '#fccde5', '#d9d9d9', '#bc80bd', '#ccebc5', '#ffed6f'],
		        Dark2: ['#1b9e77', '#d95f02', '#7570b3', '#e7298a', '#66a61e', '#e6ab02', '#a6761d', '#666666'],
		        Paired: ['#a6cee3', '#1f78b4', '#b2df8a', '#33a02c', '#fb9a99', '#e31a1c', '#fdbf6f', '#ff7f00', '#cab2d6', '#6a3d9a', '#ffff99', '#b15928'],
		        Pastel2: ['#b3e2cd', '#fdcdac', '#cbd5e8', '#f4cae4', '#e6f5c9', '#fff2ae', '#f1e2cc', '#cccccc'],
		        Pastel1: ['#fbb4ae', '#b3cde3', '#ccebc5', '#decbe4', '#fed9a6', '#ffffcc', '#e5d8bd', '#fddaec', '#f2f2f2'],
		    };

		    // add lowercase aliases for case-insensitive matches
		    for (var i = 0, list = Object.keys(colorbrewer); i < list.length; i += 1) {
		        var key = list[i];

		        colorbrewer[key.toLowerCase()] = colorbrewer[key];
		    }

		    var colorbrewer_1 = colorbrewer;

		    var chroma = chroma_1;

		    // feel free to comment out anything to rollup
		    // a smaller chroma.js built

		    // io --> convert colors

















		    // operators --> modify existing Colors










		    // interpolators












		    // generators -- > create new colors
		    chroma.average = average;
		    chroma.bezier = bezier_1;
		    chroma.blend = blend_1;
		    chroma.cubehelix = cubehelix;
		    chroma.mix = chroma.interpolate = mix$1;
		    chroma.random = random_1;
		    chroma.scale = scale$2;

		    // other utility methods
		    chroma.analyze = analyze_1.analyze;
		    chroma.contrast = contrast;
		    chroma.deltaE = deltaE;
		    chroma.distance = distance;
		    chroma.limits = analyze_1.limits;
		    chroma.valid = valid;

		    // scale
		    chroma.scales = scales;

		    // colors
		    chroma.colors = w3cx11_1;
		    chroma.brewer = colorbrewer_1;

		    var chroma_js = chroma;

		    return chroma_js;

		})); 
	} (chroma$1));

	var chromaExports = chroma$1.exports;
	var chroma = /*@__PURE__*/getDefaultExportFromCjs(chromaExports);

	function pickForegroundColorToBackgroundColor(color, blackColor = '#000000', whiteColor = '#ffffff') {
	    return chroma(color).luminance() > 0.5 ? blackColor : whiteColor;
	}
	function blues(i, n) {
	    return chroma
	        .scale('Blues')(i / (n - 1))
	        .hex();
	}

	function inBetween(v, min, max, delta = 10e-6) {
	    return v >= Math.min(min, max) - delta && v <= Math.max(min, max) + delta;
	}
	function transpose(m) {
	    return {
	        left: m.top,
	        right: m.bottom,
	        top: m.left,
	        bottom: m.right,
	        horizontal: !m.horizontal,
	    };
	}
	class TrapezoidElement extends chart_js.BarElement {
	    constructor() {
	        super(...arguments);
	        this.align = 'center';
	        this.next = undefined;
	        this.previous = undefined;
	    }
	    getBounds(useFinalPosition = false) {
	        const { x, y, base, width, height, horizontal } = this.getProps(['x', 'y', 'base', 'width', 'height', 'horizontal'], useFinalPosition);
	        if (horizontal) {
	            const w = Math.abs(x - base);
	            let left = base - (this.align !== 'left' ? w : 0);
	            let right = base + (this.align !== 'right' ? w : 0);
	            let half = height / 2;
	            let top = y - half;
	            let bottom = y + half;
	            return { left, top, right, bottom, horizontal };
	        }
	        else {
	            const h = Math.abs(y - base);
	            let half = width / 2;
	            let left = x - half;
	            let right = x + half;
	            let top = base - (this.align !== 'right' ? h : 0);
	            let bottom = base + (this.align !== 'left' ? h : 0);
	            return { left, top, right, bottom, horizontal };
	        }
	    }
	    inRange(mouseX, mouseY, useFinalPosition) {
	        const bb = this.getBounds(useFinalPosition);
	        const inX = mouseX == null || inBetween(mouseX, bb.left, bb.right);
	        const inY = mouseY == null || inBetween(mouseY, bb.top, bb.bottom);
	        return inX && inY;
	    }
	    inXRange(mouseX, useFinalPosition) {
	        return this.inRange(mouseX, null, useFinalPosition);
	    }
	    inYRange(mouseY, useFinalPosition) {
	        return this.inRange(null, mouseY, useFinalPosition);
	    }
	    getCenterPoint(useFinalPosition) {
	        const { x, y, base, horizontal } = this.getProps(['x', 'y', 'base', 'horizontal'], useFinalPosition);
	        const r = {
	            center: {
	                x: horizontal ? base : x,
	                y: horizontal ? y : base,
	            },
	            left: {
	                x: horizontal ? (base + x) / 2 : x,
	                y: horizontal ? y : (base + y) / 2,
	            },
	            right: {
	                x: horizontal ? base - (x - base) / 2 : x,
	                y: horizontal ? y : base - (y + base) / 2,
	            },
	        }[this.align];
	        return r;
	    }
	    tooltipPosition(useFinalPosition) {
	        return this.getCenterPoint(useFinalPosition);
	    }
	    getRange(axis) {
	        const { width, height } = this.getProps(['width', 'height']);
	        return axis === 'x' ? width : height;
	    }
	    computeWayPoints(useFinalPosition = false) {
	        let dir = this.options.shrinkAnchor;
	        let shrinkFraction = Math.max(Math.min(this.options.shrinkFraction, 1), 0);
	        if (shrinkFraction === 0) {
	            dir = 'none';
	            shrinkFraction = 1;
	        }
	        let bounds = this.getBounds(useFinalPosition);
	        const hor = bounds.horizontal;
	        let nextBounds = this.next && (dir === 'top' || dir === 'middle') ? this.next.getBounds(useFinalPosition) : bounds;
	        let prevBounds = this.previous && (dir === 'bottom' || dir === 'middle') ? this.previous.getBounds(useFinalPosition) : bounds;
	        if (!hor) {
	            bounds = transpose(bounds);
	            nextBounds = transpose(nextBounds);
	            prevBounds = transpose(prevBounds);
	        }
	        const hi = Math.floor((bounds.bottom - bounds.top) * (1 - shrinkFraction));
	        const hiRest = Math.floor((bounds.bottom - bounds.top - hi) / 2);
	        const points = [];
	        const rPoints = [];
	        if (dir === 'none' || dir === 'top') {
	            points.push([bounds.left, bounds.top], [bounds.right, bounds.top]);
	        }
	        else {
	            let pFraction = 1;
	            if (dir === 'middle') {
	                const pHiRest = Math.floor((prevBounds.bottom - prevBounds.top) * shrinkFraction * 0.5);
	                pFraction = hiRest / (pHiRest + hiRest);
	            }
	            points.push([bounds.left + (prevBounds.left - bounds.left) * pFraction, bounds.top], [bounds.right + (prevBounds.right - bounds.right) * pFraction, bounds.top]);
	        }
	        if (dir === 'middle') {
	            points.push([bounds.right, bounds.top + hiRest]);
	            points.push([bounds.right, bounds.bottom - hiRest]);
	            rPoints.push([bounds.left, bounds.top + hiRest]);
	            rPoints.push([bounds.left, bounds.bottom - hiRest]);
	        }
	        else if (dir === 'top' && shrinkFraction < 1) {
	            points.push([bounds.right, bounds.top + hi]);
	            rPoints.push([bounds.left, bounds.top + hi]);
	        }
	        else if (dir === 'bottom' && shrinkFraction < 1) {
	            points.push([bounds.right, bounds.bottom - hi]);
	            rPoints.push([bounds.left, bounds.bottom - hi]);
	        }
	        if (dir === 'none' || dir === 'bottom') {
	            points.push([bounds.right, bounds.bottom], [bounds.left, bounds.bottom]);
	        }
	        else {
	            let nFraction = 1;
	            if (dir === 'middle') {
	                const nHiRest = Math.floor((nextBounds.bottom - nextBounds.top) * shrinkFraction * 0.5);
	                nFraction = hiRest / (nHiRest + hiRest);
	            }
	            points.push([bounds.right + (nextBounds.right - bounds.right) * nFraction, bounds.bottom], [bounds.left + (nextBounds.left - bounds.left) * nFraction, bounds.bottom]);
	        }
	        points.push(...rPoints.reverse());
	        if (!hor) {
	            return points.map(([x, y]) => [y, x]);
	        }
	        return points;
	    }
	    draw(ctx) {
	        const { options } = this;
	        ctx.save();
	        ctx.beginPath();
	        const points = this.computeWayPoints();
	        ctx.moveTo(points[0][0], points[0][1]);
	        for (const p of points.slice(1)) {
	            ctx.lineTo(p[0], p[1]);
	        }
	        if (options.backgroundColor) {
	            ctx.fillStyle = options.backgroundColor;
	            ctx.fill();
	        }
	        if (options.borderColor) {
	            ctx.strokeStyle = options.borderColor;
	            ctx.lineWidth = options.borderWidth;
	            ctx.stroke();
	        }
	        ctx.restore();
	    }
	}
	TrapezoidElement.id = 'trapezoid';
	TrapezoidElement.defaults = {
	    ...chart_js.BarElement.defaults,
	    shrinkAnchor: 'top',
	    shrinkFraction: 1,
	};
	TrapezoidElement.defaultRoutes = chart_js.BarElement.defaultRoutes;

	function patchController(type, config, controller, elements = [], scales = []) {
	    chart_js.registry.addControllers(controller);
	    if (Array.isArray(elements)) {
	        chart_js.registry.addElements(...elements);
	    }
	    else {
	        chart_js.registry.addElements(elements);
	    }
	    if (Array.isArray(scales)) {
	        chart_js.registry.addScales(...scales);
	    }
	    else {
	        chart_js.registry.addScales(scales);
	    }
	    const c = config;
	    c.type = type;
	    return c;
	}

	class FunnelController extends chart_js.BarController {
	    getMinMax(scale, canStack) {
	        const { max } = super.getMinMax(scale, canStack);
	        const r = {
	            center: { min: -max, max },
	            left: { min: 0, max },
	            right: { min: -max, max: 0 },
	        }[this.options.align];
	        return r;
	    }
	    update(mode) {
	        super.update(mode);
	        const meta = this._cachedMeta;
	        const elements = (meta.data || []);
	        for (let i = 0; i < elements.length; i++) {
	            elements[i].align = this.options.align;
	            elements[i].next = elements[i + 1];
	            elements[i].previous = elements[i - 1];
	        }
	    }
	}
	FunnelController.id = 'funnel';
	FunnelController.defaults = helpers.merge({}, [
	    chart_js.BarController.defaults,
	    {
	        dataElementType: TrapezoidElement.id,
	        barPercentage: 1,
	        align: 'center',
	        categoryPercentage: 0.98,
	    },
	]);
	FunnelController.overrides = helpers.merge({}, [
	    chart_js.BarController.overrides,
	    {
	        plugins: {
	            legend: {
	                display: false,
	            },
	            colors: {
	                enabled: false,
	            },
	            datalabels: {
	                anchor: 'start',
	                textAlign: 'center',
	                font: {
	                    size: 20,
	                },
	                color: (context) => {
	                    const bgColor = context.chart.getDatasetMeta(context.datasetIndex).data[context.dataIndex].options
	                        .backgroundColor;
	                    return pickForegroundColorToBackgroundColor(bgColor, chart_js.Chart.defaults.color, '#ffffff');
	                },
	                formatter: (value, context) => {
	                    var _a, _b;
	                    const label = (_b = (_a = context.chart.data.labels) === null || _a === void 0 ? void 0 : _a[context.dataIndex]) !== null && _b !== void 0 ? _b : '';
	                    return `${label}\n${(value * 100).toLocaleString()}%`;
	                },
	            },
	        },
	        scales: {
	            _index_: {
	                display: false,
	                padding: 10,
	                grid: {
	                    display: false,
	                },
	            },
	            _value_: {
	                display: false,
	                beginAtZero: false,
	                grace: 0,
	                grid: {
	                    display: false,
	                },
	            },
	        },
	        elements: {
	            trapezoid: {
	                backgroundColor(context) {
	                    const nData = context.chart.data.datasets[context.datasetIndex].data.length;
	                    return blues(context.dataIndex, nData);
	                },
	            },
	        },
	    },
	]);
	class FunnelChart extends chart_js.Chart {
	    constructor(item, config) {
	        super(item, patchController('funnel', config, FunnelController, TrapezoidElement, [chart_js.CategoryScale, chart_js.LinearScale]));
	    }
	}
	FunnelChart.id = FunnelController.id;

	chart_js.registry.addControllers(FunnelController);
	chart_js.registry.addElements(TrapezoidElement);

	exports.FunnelChart = FunnelChart;
	exports.FunnelController = FunnelController;
	exports.TrapezoidElement = TrapezoidElement;
	exports.blues = blues;
	exports.pickForegroundColorToBackgroundColor = pickForegroundColorToBackgroundColor;

}));
//# sourceMappingURL=index.umd.js.map
