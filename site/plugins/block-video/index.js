(function(){"use strict";const b="";function u(o,e,i,h,s,a,_,C){var t=typeof o=="function"?o.options:o;e&&(t.render=e,t.staticRenderFns=i,t._compiled=!0),h&&(t.functional=!0),a&&(t._scopeId="data-v-"+a);var r;if(_?(r=function(n){n=n||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,!n&&typeof __VUE_SSR_CONTEXT__<"u"&&(n=__VUE_SSR_CONTEXT__),s&&s.call(this,n),n&&n._registeredComponents&&n._registeredComponents.add(_)},t._ssrRegister=r):s&&(r=C?function(){s.call(this,(t.functional?this.parent:this).$root.$options.shadowRoot)}:s),r)if(t.functional){t._injectStyles=r;var k=t.render;t.render=function(y,c){return r.call(c),k(y,c)}}else{var l=t.beforeCreate;t.beforeCreate=l?[].concat(l,r):[r]}return{exports:o,options:t}}const d={data(){return{mime:null}},computed:{source(){return this.content.source_file[0]?this.content.source_file[0]:{}}},watch:{"source.link":{handler(o){o&&this.$api.get(o).then(e=>{this.mime=e.mime})},immediate:!0}}};var f=function(){var e=this,i=e._self._c;return i("k-block-figure",{attrs:{"is-empty":!e.source.url,"empty-icon":"file-video","empty-text":"No video selected yet \u2026"},on:{open:e.open,update:e.update}},[i("div",{staticClass:"k-block-type-video-wrapper"},[i("video",{staticClass:"k-block-type-video-element",attrs:{controls:""}},[i("source",{attrs:{src:e.source.url,type:e.mime}})]),i("h3",[e._v(e._s(e.content.title))]),i("p",[e._v(e._s(e.content.caption))])])])},p=[],v=u(d,f,p,!1,null,null,null,null);const m=v.exports;panel.plugin("cosmo/block-video",{blocks:{video:m}})})();