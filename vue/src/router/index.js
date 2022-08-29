import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import Cookies from 'js-cookie'
const checkLogged=async ()=>{
  if(!Cookies.get('User Token')){
    return {
      name: 'login',
    }
  }
  return true;
}
const routes=[
  {
    path:'/login',
    name:'login',
    component: LoginView,
    beforeEnter:()=>{
      if(Cookies.get('User Token')){
        return {
          name: 'dashboard',
        }
      }
      return true;
    }
  },
  {
    path:'/',
    name:'dashboard',
    component: DashboardView,
    beforeEnter: checkLogged,
  }
]
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
