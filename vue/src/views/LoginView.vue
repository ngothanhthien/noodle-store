<script setup>
import { reactive, ref,unref } from "vue";
import {useRouter} from 'vue-router';
import Cookies from 'js-cookie'
import PersonIcon from "../components/icons/PersonIcon.vue";
import LockIcon from "../components/icons/LockIcon.vue";
import { usernameValidate, passwordValidate } from "../validate/user";
import { fetchMethod } from "../logic/fetchAPI";
import { loginAPI } from "../api";
const router = useRouter()
const form = reactive({
  username: "",
  password: "",
});
const error = ref("");
const login = async (e) => {
  const button = e.target;
  loginDisable(button);
  try {
    usernameValidate(form.username);
    passwordValidate(form.password);
  } catch (err) {
    error.value = err;
    loginEnable(button);
    return;
  }
  const response=await fetchMethod(loginAPI,"POST",unref(form));
  if(response.errors){
    error.value = "Sai tài khoản hoặc mật khẩu";
    loginEnable(button);
    return;
  }
  Cookies.set('User Token', response.token);
  router.push({name:'dashboard'});
};
const clearError = () => {
  error.value = "";
};
const loginDisable=(button)=>{
  button.disabled = true;
  button.textContent = "Đang đăng nhập...";
}
const loginEnable=(button)=>{
  button.disabled = false;
  button.textContent = "Đăng nhập";
}
</script>
<template>
  <div class="h-screen flex justify-center items-center bg-gray-100">
    <div
      class="flex flex-col w-full max-w-md px-4 py-8 bg-white rounded-lg shadow sm:px-6 md:px-8 lg:px-10"
    >
      <div class="self-center mb-3 text-xl font-light text-gray-600 sm:text-2xl">
        Đăng nhập
      </div>
      <div class="self-center text-red-500">{{ error }}</div>
      <div class="mt-8">
        <form @submit.prevent action="#" autoComplete="on">
          <div class="flex flex-col mb-2">
            <div class="flex relative">
              <span
                class="rounded-l-md inline-flex items-center px-3 border-t bg-white border-l border-b border-gray-300 text-gray-500 shadow-sm text-sm"
              >
                <PersonIcon class="w-5 h-5 fill-current" />
              </span>
              <input
                @focus="clearError"
                type="text"
                class="rounded-r-lg flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                placeholder="Tài khoản"
                v-model="form.username"
              />
            </div>
          </div>
          <div class="flex flex-col mb-6">
            <div class="flex relative">
              <span
                class="rounded-l-md inline-flex items-center px-3 border-t bg-white border-l border-b border-gray-300 text-gray-500 shadow-sm text-sm"
              >
                <LockIcon class="w-5 h-5 fill-current" />
              </span>
              <input
                @focus="clearError"
                type="password"
                class="rounded-r-lg flex-1 appearance-none border border-gray-300 w-full py-2 px-4 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                placeholder="Mật khẩu"
                v-model="form.password"
              />
            </div>
          </div>
          <div class="flex w-full">
            <button
              type="submit"
              @click="login"
              class="py-2 px-4 disabled:bg-purple-400 bg-purple-600 hover:bg-purple-700 focus:ring-purple-500 focus:ring-offset-purple-200 text-white w-full text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg"
            >
              Đăng nhập
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
