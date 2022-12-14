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
    children:[
      {
        path:'/staff',
        name:'Staff Manage',
        component: ()=>import('../views/Dashboard/StaffManageView.vue'),
        beforeEnter: checkLogged,
      },
      {
        path:'/order',
        redirect:'/order/create',
      },
      {
        path:'/order/create',
        name:'Order Create',
        component: ()=>import('../views/Dashboard/OrderCreateView.vue'),
        beforeEnter: checkLogged,
      },
      {
        path:'/order/manage',
        name:'Order Manage',
        component: ()=>import('../views/Dashboard/OrderManageView.vue'),
      },
      {
        path:'/meal/manage',
        name:'Meal Manage',
        component: ()=>import('../views/Dashboard/MealManageView.vue'),
      },
      {
        path:'/setting/change-password',
        name:'Change Password',
        component:()=>import('../views/Dashboard/ChangePasswordView.vue'),
      },
      {
        path:'/setting/change-info',
        name:'Change Info',
        component:()=>import('../views/Dashboard/ChangeInfoView.vue'),
      },
      {
        path:'/customers',
        name:'Customer View',
        component:()=>import('../views/Dashboard/CustomerView.vue'),
      }
    ],
  },
  {
    path:'/test',
    component: ()=>import('../views/TestView.vue')
  },
  {
    path:'/admin',
    name:'Admin Login',
    component: ()=>import('../views/AdminLoginView.vue'),
  }
]
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
