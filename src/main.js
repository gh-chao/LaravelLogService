import Vue from 'vue'
import {MenuItem, TableColumn, Menu, Input, Button, Select, Table, Dialog, Pagination, DatePicker, Option, Loading, Autocomplete} from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import App from './App.vue'

Vue.use(MenuItem)
Vue.use(TableColumn)
Vue.use(Menu)
Vue.use(Input)
Vue.use(Button)
Vue.use(Select)
Vue.use(Table)
Vue.use(Dialog)
Vue.use(Pagination)
Vue.use(DatePicker)
Vue.use(Option)
Vue.use(Loading)
Vue.use(Autocomplete)

// eslint-disable-next-line no-new
new Vue({
  el: '#app',
  render: h => h(App)
})
