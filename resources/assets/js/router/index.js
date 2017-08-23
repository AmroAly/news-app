import Vue from 'vue'
import VueRouter from 'vue-router'

import Auth from '../store/auth'
import Register from '../views/Auth/Register.vue'
import Login from '../views/Auth/Login.vue'
import NewsIndex from '../views/News/Index.vue'
import NewsShow from '../views/News/Show.vue'
import NewsCreate from '../views/News/Create.vue'
import NotFound from '../views/NotFound.vue'

Vue.use(VueRouter)
Auth.initialize()

const router = new VueRouter({
    mode: 'history',
    routes: [
        { path: '/', component: NewsIndex },
        { path: '/users/:id/news', component: NewsIndex, meta: { type: 'currentUserNewsPosts', requiredAuth: true}},
        { path: '/news/create', component: NewsCreate, meta: { requiredAuth: true } },
        { path: '/news/:id', component: NewsShow },
        { path: '/register', component: Register, meta: { requiredNotAuth: true } },
        { path: '/register/confirm/:token', component: Login, meta: { hasToken: true } },
        { path: '/login', component: Login, meta: { requiredNotAuth: true } },
        { path: '*', component: NotFound }
    ]
})
router.beforeEach((to, from, next) => {
    if (to.meta.requiredAuth) {
        if (Auth.state.api_token && Auth.state.user_id) {
            next()
        } else {
            next('/login')
        }
    }
    if (to.meta.requiredNotAuth) {
        if (Auth.state.api_token && Auth.state.user_id) {
            next('/')
        } else {
            next()
        }
    }
    next()
})

export default router
