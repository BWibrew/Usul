import init from './bootstrap'
import vue from 'vue'
import ExampleOptions from './components/ExampleComponent.vue'

init()
window.Vue = vue;

Vue.component('example', ExampleOptions);

const app = new Vue({
    el: '#app'
});
