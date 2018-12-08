import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

const _37680cd6 = () => import('../pages/admin/index.vue' /* webpackChunkName: "pages/admin/index" */).then(m => m.default || m)
const _a33aeb8c = () => import('../pages/admin/user/index.vue' /* webpackChunkName: "pages/admin/user/index" */).then(m => m.default || m)
const _2941d0b0 = () => import('../pages/admin/login/index.vue' /* webpackChunkName: "pages/admin/login/index" */).then(m => m.default || m)
const _58ad3a3a = () => import('../pages/admin/script/index.vue' /* webpackChunkName: "pages/admin/script/index" */).then(m => m.default || m)
const _40e60e89 = () => import('../pages/admin/user/add.vue' /* webpackChunkName: "pages/admin/user/add" */).then(m => m.default || m)
const _8a45d4da = () => import('../pages/admin/user/changepassword.vue' /* webpackChunkName: "pages/admin/user/changepassword" */).then(m => m.default || m)
const _5366f55c = () => import('../pages/admin/user/logout.vue' /* webpackChunkName: "pages/admin/user/logout" */).then(m => m.default || m)
const _15982c96 = () => import('../pages/index.vue' /* webpackChunkName: "pages/index" */).then(m => m.default || m)



if (process.client) {
  window.history.scrollRestoration = 'manual'
}
const scrollBehavior = function (to, from, savedPosition) {
  // if the returned position is falsy or an empty object,
  // will retain current scroll position.
  let position = false

  // if no children detected
  if (to.matched.length < 2) {
    // scroll to the top of the page
    position = { x: 0, y: 0 }
  } else if (to.matched.some((r) => r.components.default.options.scrollToTop)) {
    // if one of the children has scrollToTop option set to true
    position = { x: 0, y: 0 }
  }

  // savedPosition is only available for popstate navigations (back button)
  if (savedPosition) {
    position = savedPosition
  }

  return new Promise(resolve => {
    // wait for the out transition to complete (if necessary)
    window.$nuxt.$once('triggerScroll', () => {
      // coords will be used if no selector is provided,
      // or if the selector didn't match any element.
      if (to.hash) {
        let hash = to.hash
        // CSS.escape() is not supported with IE and Edge.
        if (typeof window.CSS !== 'undefined' && typeof window.CSS.escape !== 'undefined') {
          hash = '#' + window.CSS.escape(hash.substr(1))
        }
        try {
          if (document.querySelector(hash)) {
            // scroll to anchor by returning the selector
            position = { selector: hash }
          }
        } catch (e) {
          console.warn('Failed to save scroll position. Please add CSS.escape() polyfill (https://github.com/mathiasbynens/CSS.escape).')
        }
      }
      resolve(position)
    })
  })
}


export function createRouter () {
  return new Router({
    mode: 'history',
    base: '/',
    linkActiveClass: 'nuxt-link-active',
    linkExactActiveClass: 'nuxt-link-exact-active',
    scrollBehavior,
    routes: [
		{
			path: "/admin",
			component: _37680cd6,
			name: "admin"
		},
		{
			path: "/admin/user",
			component: _a33aeb8c,
			name: "admin-user"
		},
		{
			path: "/admin/login",
			component: _2941d0b0,
			name: "admin-login"
		},
		{
			path: "/admin/script",
			component: _58ad3a3a,
			name: "admin-script"
		},
		{
			path: "/admin/user/add",
			component: _40e60e89,
			name: "admin-user-add"
		},
		{
			path: "/admin/user/changepassword",
			component: _8a45d4da,
			name: "admin-user-changepassword"
		},
		{
			path: "/admin/user/logout",
			component: _5366f55c,
			name: "admin-user-logout"
		},
		{
			path: "/",
			component: _15982c96,
			name: "index"
		}
    ],
    
    
    fallback: false
  })
}