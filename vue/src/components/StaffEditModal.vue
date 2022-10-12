<script setup>
import { onClickOutside } from '@vueuse/core';
import { reactive, ref, unref } from 'vue';
import ButtonClose from './ButtonClose.vue';
import CheckBox from './CheckBox.vue';
import rules from '../mapping/rule';
import { addOrRemove } from '../logic/array';
import axios from 'axios';
import Cookies from 'js-cookie';
import validate from '../validate/staff';
import errorHandle from '../logic/errorHandle';
import ErrorDisplay from './ErrorDisplay.vue';
import {staffAPI} from '../api'
const props=defineProps(['staff']);
const emits = defineEmits(['outside', 'close', 'successCb']);
const token = Cookies.get('User Token');
const form = reactive({
    name: props.staff.name,
    phone: props.staff.phone,
    rules: props.staff.rules.map(rule=>rule.name),
})
const submitButton = ref(null);
const error = ref(null);
async function onSubmit() {
    buttonSubmitting();
    try {
        validate(unref(form));
        await axios({
            method: 'PUT',
            url: staffAPI+'/'+props.staff.id,
            data: unref(form),
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });
        emits('successCb', unref(form),props.staff.id);
    } catch (e) {
        buttonNotSubmit();
        if(e.response){
            errorHandle(e.response.status, e);
            return;
        }
        error.value = e;
    }
}
const box = ref(null);
onClickOutside(box, () => emits('outside'));
function clearError() {
    error.value = null;
}
function buttonSubmitting() {
    submitButton.value.innerHTML = 'Đang cập nhật...';
    submitButton.value.disabled = true;
}
function buttonNotSubmit() {
    submitButton.value.innerHTML = 'Cập nhật';
    submitButton.value.disabled = false;
}
</script>
    
<template>
    <div class="w-full h-full fixed flex justify-center items-center left-0 top-0 z-50 bg-gray-800/20">
        <div ref="box" class="w-3/4 h-96 bg-gray-200 inline-block px-3 py-2 relative">
            <ButtonClose @close="emits('close')"></ButtonClose>
            <div class="text-xl mb-6">Sửa thông tin nhân viên</div>
            <ErrorDisplay :error="error" class="z-10 mt-9"></ErrorDisplay>
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
                    <div class="text-lg mb-1">Quyền</div>
                    <div class="flex">
                        <div v-for="(rule,key) in rules">
                            <CheckBox :isChecked="form.rules.includes(key)" @check="addOrRemove(form.rules,key)">
                                {{rule}}</CheckBox>
                        </div>
                    </div>
                </div>
                <div class="text-right p-1">
                    <button ref="submitButton" type="submit"
                        class="w-40 absolute bottom-2 right-5 transition hover:bg-blue-600 disabled:bg-blue-500 bg-blue-700 text-white rounded p-2">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>