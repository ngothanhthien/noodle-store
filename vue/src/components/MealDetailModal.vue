<script setup>
import { onClickOutside } from '@vueuse/core';
import {reactive, ref} from 'vue';
import ButtonClose from './ButtonClose.vue';
import axios from 'axios';
import Cookies from 'js-cookie';
import { mealAPI } from '../api';
import validate from '../validate/meal';
import errorHandle from '../logic/errorHandle';
import tagMapping from '../mapping/type';
import {moneyToNumber, numberToMoney} from '../logic/money';
import ErrorDisplay from './ErrorDisplay.vue';
const emits = defineEmits(['outside', 'close', 'successCb','successDelete']);
const token = Cookies.get('User Token');
const props = defineProps(['meal']);
const form = reactive({
    name: props.meal.name,
    type: props.meal.type,
    price:numberToMoney(props.meal.price),
    description: props.meal.description,
});
const image = ref(null);
const imagePreviewElement = ref(null);
const isImagePreUpload = ref(false);
const submitButton = ref(null);
const deleteButtonElement=ref(null);
const error = ref('');
async function onSubmit() {
    buttonSubmitting();
    try {
        validate(form);
        const formData = new FormData();
        if(image.value.files[0]){
            formData.append('image', image.value.files[0]);
        }
        formData.append('name', form.name);
        formData.append('type', form.type);
        formData.append('price', moneyToNumber(form.price));
        formData.append('_method','put')
        if(form.description){
            formData.append('description', form.description);
        }
        const response = await axios({
            method: 'POST',
            url: mealAPI + '/' + props.meal.id,
            data: formData,
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'Multipart/form-data'
            }
        });
        buttonNotSubmit();
        emits('successCb', response.data);
    } catch (e) {
        buttonNotSubmit();
        if(e.response){
            errorHandle(e.response.status, e);
            return;
        }
        error.value=e;
    }
}
const box = ref(null);
onClickOutside(box, () => emits('outside'));
function clearError() {
    error.value = null;
}
function buttonSubmitting() {
    submitButton.value.innerHTML = 'Đang lưu...';
    submitButton.value.disabled = true;
}
function buttonNotSubmit() {
    submitButton.value.innerHTML = 'Lưu thay đổi';
    submitButton.value.disabled = false;
}
function imagePreview(e) {
    const preview = imagePreviewElement.value;
    preview.src = URL.createObjectURL(e.target.files[0]);
    isImagePreUpload.value = true;
}
function moneyConvertToNumber(){
    error.value=null;
    form.price=moneyToNumber(form.price);
}
function numberConvertToMoney(){
    form.price=numberToMoney(form.price); 
}
async function deleteMeal(){
    deleteButtonElement.value.innerHTML="Đang xóa...";
    deleteButtonElement.value.disabled=true;
    try{
        await axios({
            method:'DELETE',
            url:mealAPI+'/'+props.meal.id,
            headers:{
                'Authorization': 'Bearer '+token,
            }
        })
        emits('successDelete',props.meal.id);
        deleteButtonElement.value.innerHTML="Xóa món";
        deleteButtonElement.value.disabled=false;
    }catch(e){
        if(e.response){
            errorHandle(e.response.status, e);
            return;
        }
        deleteButtonElement.value.innerHTML="Xóa món";
        deleteButtonElement.value.disabled=false;
        console.log(e);
    }
}
</script>
        
<template>
    <div class="w-full h-full fixed flex justify-center items-center left-0 top-0 z-50 bg-gray-800/20">
        <div ref="box" class="w-3/4 bg-gray-200 inline-block px-3 py-2 relative">
            <ButtonClose @close="emits('close')"></ButtonClose>
            <div class="text-xl">Chỉnh sửa</div>
            <ErrorDisplay :error="error" class="mt-9"></ErrorDisplay>
            <form @submit.prevent="onSubmit" class="mt-6">
                <div class="flex">
                    <div class="w-1/3 p-2 h-96 overflow-hidden">
                        <input @change="imagePreview" ref="image" type="file" class="hidden" id="imageUpload">
                        <label for="imageUpload">
                            <div class="cursor-pointer">
                                <img class="w-full" ref="imagePreviewElement" src="https://api.lorem.space/image/burger?w=300&h=300">
                            </div>
                        </label>
                    </div>
                    <div class="w-1/2 p-2">
                        <input @focus="clearError" v-model="form.name" type="text"
                            class="text-xl p-2 w-full outline-none bg-inherit" placeholder="Tên món ăn...">
                        <div class="flex">
                            <div class="flex mx-1.5 select-none" v-for="(tag,value) in tagMapping" :key="tag">
                                <input class="cursor-pointer" @change="clearError" :value="value" v-model="form.type"
                                    :id="'tag'+value" name="tag" type="radio">
                                <label class="pb-0.5 cursor-pointer" :for="'tag'+value">{{tag.name}}</label>
                            </div>
                        </div>
                        <input @focus="moneyConvertToNumber" @focusout="numberConvertToMoney" v-model="form.price" class="p-2 bg-inherit outline-none" type="text"
                            placeholder="Giá bán...">
                        <textarea @focus="clearError" v-model="form.description"
                            class="w-full resize-none h-32 outline-none bg-inherit p-2 mt-1" type="text"
                            placeholder="Thông tin thêm..."></textarea>
                    </div>
                </div>
                <div class="flex p-2 mt-1">
                    <div ref="deleteButtonElement" @click="deleteMeal" class="text-white bg-red-600 p-2 w-24 text-center rounded hover:bg-red-500 disabled:bg-red-500 cursor-pointer">
                        Xóa món
                    </div>
                    <button ref="submitButton" type="submit"
                        class="ml-auto w-32 transition hover:bg-blue-600 disabled:bg-blue-500 bg-blue-700 text-white rounded p-2">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>