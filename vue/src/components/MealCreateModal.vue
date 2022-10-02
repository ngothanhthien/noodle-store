<script setup>
import { onClickOutside } from '@vueuse/core';
import { reactive, ref, unref } from 'vue';
import ButtonClose from './ButtonClose.vue';
import axios from 'axios';
import Cookies from 'js-cookie';
import { mealAPI } from '../api';
import validate from '../validate/meal';
import errorHandle from '../logic/errorHandle';
import AddIcon from './icons/AddIcon.vue';
import tagMapping from '../mapping/type'
const emits = defineEmits(['outside', 'close', 'successCb']);
const token = Cookies.get('User Token');
const form = reactive({
    name: '',
    type: 0,
    price: '',
    description: '',
});
const image=ref(null);
const imagePreviewElement=ref(null);
const isImagePreUpload=ref(false);
const submitButton = ref(null);
const error = ref(null);
async function onSubmit() {
    buttonSubmitting();
    try {
        validate(form);
    } catch (e) {
        error.value = e;
        buttonNotSubmit();
        return;
    }
    try {
        const formData=new FormData();
        formData.append('image', image.value.files[0]);
        formData.append('name',form.name);
        formData.append('type', form.type);
        formData.append('price', form.price);
        formData.append('description', form.description);
        const response = await axios({
            method: 'POST',
            url: mealAPI,
            data: formData,
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'Multipart/form-data'
            }
        });
        emits('successCb', response.data);
    } catch (e) {
        errorHandle(e.response.status, e);
        buttonNotSubmit();
        return;
    }
}
const box = ref(null);
onClickOutside(box, () => emits('outside'));
function clearError() {
    error.value = null;
}
function buttonSubmitting() {
    submitButton.value.innerHTML = 'Đang tạo...';
    submitButton.value.disabled = true;
}
function buttonNotSubmit() {
    submitButton.value.innerHTML = 'Tạo món';
    submitButton.value.disabled = false;
}
function imagePreview(e){
    const preview=imagePreviewElement.value;
    preview.src= URL.createObjectURL(e.target.files[0]);
    isImagePreUpload.value=true;
}
</script>
    
<template>
    <div class="w-full h-full fixed flex justify-center items-center left-0 top-0 z-50 bg-gray-800/20">
        <div ref="box" class="w-3/4 bg-gray-200 inline-block px-3 py-2 relative">
            <ButtonClose @close="emits('close')"></ButtonClose>
            <div class="text-xl">Tạo món</div>
            <div class="text-red-600">{{error}}</div>
            <form @submit.prevent="onSubmit" class="mt-5">
                <div class="flex">
                    <div class="w-1/2 p-2 h-96 overflow-hidden">
                        <input @change="imagePreview" ref="image" type="file" class="hidden" id="imageUpload">
                        <label for="imageUpload">
                            <div v-show="isImagePreUpload" class="cursor-pointer">
                                <img ref="imagePreviewElement">
                            </div>
                            <div v-show="!isImagePreUpload" class="bg-white rounded overflow-hidden">
                                <div
                                    class="h-96 w-full flex items-center justify-center cursor-pointer group">
                                    <div class="flex items-center justify-center cursor-pointer">
                                        <AddIcon
                                            class="w-14 h-14 stroke-white stroke-2 fill-current group-hover:fill-gray-600 transition" />
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="w-1/2 p-2">
                        <input @focus="clearError" v-model="form.name" type="text" class="text-xl p-2 w-full outline-none bg-inherit"
                            placeholder="Tên món ăn...">
                        <div class="flex">
                            <div class="flex mx-1.5 select-none" v-for="(tag,value) in tagMapping" :key="tag">
                                <input class="cursor-pointer" @change="clearError" :value="value" v-model="form.type" :id="'tag'+value" name="tag" type="radio">
                                <label class="pb-0.5 cursor-pointer" :for="'tag'+value">{{tag.name}}</label>
                            </div>
                        </div>
                        <input @focus="clearError" v-model="form.price" class="p-2 bg-inherit outline-none" type="text" placeholder="Giá bán...">
                        <textarea @focus="clearError" v-model="form.description" class="w-full resize-none h-32 outline-none bg-inherit p-2 mt-1" type="text" placeholder="Thông tin thêm..."></textarea>
                    </div>
                </div>
                <div class="text-right p-1">
                    <button ref="submitButton" type="submit"
                        class="w-32 transition hover:bg-blue-600 disabled:bg-blue-500 bg-blue-700 text-white rounded p-2">
                        Tạo món
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>