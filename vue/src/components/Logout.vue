<script setup>
import { logoutAPI } from '../api'
import { fetchGet } from '../logic/fetchAPI'
import {backToLogin} from '../logic/auth'
import Cookies from 'js-cookie'

const logout=async ()=>{
    const token=Cookies.get('User Token');
    if(token){
        await fetchGet(logoutAPI, token);
    }
    backToLogin();
}
</script>
<template>
    <div @click="logout" class="fixed right-3 top-1 underline cursor-pointer hover:text-red-500">
        Đăng xuất
    </div>
</template>