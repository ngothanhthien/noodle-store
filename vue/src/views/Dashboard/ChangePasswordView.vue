<script setup>
import axios from 'axios';
import { reactive, ref, unref } from 'vue';
import validate from '../../validate/changePassword'
import {changePasswordAPI} from '../../api'
import { backToLogin, getUserToken } from '../../logic/auth';
import ErrorDisplay from '../../components/ErrorDisplay.vue';
const buttonSubmit=ref(null);
const error=ref('');
const token=getUserToken();
const form=reactive({
    oldPassword:'',
    newPassword:'',
    confirmPassword:'',
})
async function submitForm(){
    buttonSubmitting();
    try{
        validate(unref(form));
        await axios({
            url:changePasswordAPI,
            method:'PATCH',
            headers:{
                'Authorization': 'Bearer ' + token,
            },
            data: unref(form)
        })
        buttonNormal();
        backToLogin();
    }catch(e){
        buttonNormal();
        if(e.response){
            if(e.response.status==401){
                error.value="Mật khẩu cũ không đúng";
            }
            return;
        }
        error.value=e;
    }
}
function buttonSubmitting(){
    buttonSubmit.value.textContent="Đang gửi...";
    buttonSubmit.value.disabled=true;
}
function buttonNormal(){
    buttonSubmit.value.textContent="ĐỔI MẬT KHẨU";
    buttonSubmit.value.disabled=false;
}
</script>
<template>
    <div>
        <div class="text-xl">Đổi mật khẩu</div>
        <form @submit.prevent="submitForm" class="w-80 relative pt-6">
            <ErrorDisplay :error="error"></ErrorDisplay>
            <div class="mt-3">
                <div>Mật khẩu cũ</div>
                <input type="password" v-model="form.oldPassword" class="outline-none mt-1 rounded px-2 py-1 w-full" />
            </div>
            <div class="mt-3">
                <div>Mật khẩu mới</div>
                <input type="password" v-model="form.newPassword" class="outline-none mt-1 rounded px-2 py-1 w-full" />
            </div>
            <div class="mt-3">
                <div>Xác nhận mật khẩu mới</div>
                <input type="password" v-model="form.confirmPassword" class="outline-none mt-1 rounded px-2 py-1 w-full" />
            </div>
            <button ref="buttonSubmit" type="submit" class="mt-2 p-2 w-32 text-sm uppercase disabled:bg-blue-500 hover:bg-blue-500 bg-blue-600 text-white inline-block rounded">
                Đổi mật khẩu
            </button>
        </form>
    </div>
</template>