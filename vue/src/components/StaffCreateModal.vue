<script setup>
import { onClickOutside } from '@vueuse/core';
import { reactive, ref, unref } from 'vue';
import ButtonClose from './ButtonClose.vue';
import CheckBox from './CheckBox.vue';
import rules from '../mapping/rule';
import {addOrRemove} from '../logic/array';
import axios from 'axios';
import Cookies from 'js-cookie';
import {staffAPI} from '../api';
import validate from '../validate/staff';
import status from '../mapping/status';
import { backToLogin } from '../logic/auth';
const emits= defineEmits(['outside','close','successCb']);
const token=Cookies.get('User Token');
const form = reactive({
    name: '',
    phone: '',
    rules:[],
})
const submitButton=ref(null);
const error=ref(null);
async function onSubmit(){
    buttonSubmitting();
    try{
        validate(form);
    }catch(e){
        error.value=e;
        buttonNotSubmit();
        return;
    }
    try{
        const response=await axios({
            method:'POST',
            url:staffAPI,
            data: unref(form),
            headers:{
                'Authorization': 'Bearer '+token
            }
        });
        emits('successCb',response.data);
    }catch(e){
        if(e.response.status==status['unauthorized']){
            backToLogin();
        }
        buttonNotSubmit();
        return;
    }
}
const box = ref(null);
onClickOutside(box, () => emits('outside'));
function test(v){
    console.log(v)
}
function clearError(){
    error.value=null;
}
function buttonSubmitting(){
    submitButton.value.innerHTML='Đang tạo...';
    submitButton.value.disabled=true;
}
function buttonNotSubmit(){
    submitButton.value.innerHTML='Tạo nhân viên';
    submitButton.value.disabled=false;
}
</script>

<template>
    <div class="w-full h-full fixed flex justify-center items-center left-0 top-0 z-50 bg-gray-800/20">
        <div ref="box" class="w-3/4 bg-gray-200 inline-block px-3 py-2 relative">
            <ButtonClose @close="emits('close')"></ButtonClose>
            <div class="text-xl">Tạo nhân viên</div>
            <div class="text-red-600 mb-1">
                {{error}}
            </div>
            <form @submit.prevent="onSubmit" action="" :class="{'mt-5':error==null}">
                <div class="flex">
                    <div class="mr-10">
                        <div class="text-lg">Tên</div>
                        <input @focus="clearError" v-model="form.name"
                            class="outline-none rounded outline-offset-0 focus:outline-green-600 py-1 px-2" type="text">
                    </div>
                    <div>
                        <div class="text-lg">Số điện thoại</div>
                        <input @focus="clearError" v-model="form.phone"
                            class="outline-none rounded outline-offset-0 focus:outline-green-600 py-1 px-2" type="text">
                    </div>
                </div>
                <div class="my-3">
                    <div class="text-lg mb-1">Thêm quyền</div>
                    <div class="flex">
                        <div v-for="(rule,key) in rules">
                            <CheckBox :isChecked="form.rules.includes(key)" @check="addOrRemove(form.rules,key)">{{rule}}</CheckBox>
                        </div>
                    </div>
                </div>
                <div class="text-right p-1">
                    <button ref="submitButton" type="submit" class="w-32 transition hover:bg-blue-600 disabled:bg-blue-500 bg-blue-700 text-white rounded p-2">
                        Tạo nhân viên
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>