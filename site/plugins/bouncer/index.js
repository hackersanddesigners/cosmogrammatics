(function(){"use strict";var d=function(){var e=this,t=e.$createElement,r=e._self._c||t;return e.show?r("div",{staticClass:"bouncer-nav"},[r("div",{staticClass:"bouncer-nav-container"},[r("div",{staticClass:"bouncer-nav-inner"},[r("strong",[e._v("Basculer vers :")]),e._l(e.pages,function(o){return r("div",{staticClass:"page"},[r("k-link",{attrs:{to:o.path}},[e._v(e._s(o.title))])],1)})],2)])]):e._e()},h=[],y="";function f(e,t,r,o,s,u,_,$){var n=typeof e=="function"?e.options:e;t&&(n.render=t,n.staticRenderFns=r,n._compiled=!0),o&&(n.functional=!0),u&&(n._scopeId="data-v-"+u);var i;if(_?(i=function(a){a=a||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,!a&&typeof __VUE_SSR_CONTEXT__!="undefined"&&(a=__VUE_SSR_CONTEXT__),s&&s.call(this,a),a&&a._registeredComponents&&a._registeredComponents.add(_)},n._ssrRegister=i):s&&(i=$?function(){s.call(this,(n.functional?this.parent:this).$root.$options.shadowRoot)}:s),i)if(n.functional){n._injectStyles=i;var g=n.render;n.render=function(w,v){return i.call(v),g(w,v)}}else{var c=n.beforeCreate;n.beforeCreate=c?[].concat(c,i):[i]}return{exports:e,options:n}}const p={data(){return{user:void 0}},created(){this.$api.get("current-user").then(e=>{this.user=e,this.showBar(e)&&this.$nextTick(()=>{let t=this.$root.$el,r=t.querySelector(".k-panel-main"),o=t.querySelector(".k-sections .bouncer-nav-container"),s=document.querySelector(".k-panel > .bouncer-nav-container");t.classList.add("bouncer-padding-top"),s&&s.remove(),t.insertBefore(o,r)})})},computed:{show(){return this.showBar(this.user)},pages(){return this.show?this.user.allowed.filter(e=>e.path!=this.parent):[]}},methods:{showBar(e){return e&&e.nav&&Array.isArray(e.allowed)&&e.allowed.length>1}}},l={};var m=f(p,d,h,!1,C,null,null,null);function C(e){for(let t in l)this[t]=l[t]}var b=function(){return m.exports}();panel.plugin("sylvainjule/bouncer",{sections:{bouncernav:b}})})();
