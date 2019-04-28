import init from './bootstrap'
import vue from 'vue'
import LoginForm from './components/LoginForm.vue'

init()
window.Vue = vue

Vue.component('login-form', LoginForm)

new Vue({
    el: '#app'
})
