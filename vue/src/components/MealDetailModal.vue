<script setup>
import { onClickOutside } from '@vueuse/core';
import { onMounted, reactive, ref } from 'vue';
import ButtonClose from './ButtonClose.vue';
import validate from '../validate/meal';
import errorHandle from '../logic/errorHandle';
import tagMapping from '../mapping/type';
import { moneyToNumber, numberToMoney } from '../logic/money';
import ErrorDisplay from './ErrorDisplay.vue';
import LoadingElement from './LoadingElement.vue';
import ToppingSelect from './ToppingSelect.vue';
import { updateMealAPI, getToppingByMealAPI, getAllToppingAPI } from '../api/mealAPI';
import { addOrRemove } from '../logic/array';
const emits = defineEmits(['outside', 'close', 'successCb', 'successDelete']);
const props = defineProps(['meal']);
const form = reactive({
    name: props.meal.name,
    type: props.meal.type,
    price: numberToMoney(props.meal.price),
    description: props.meal.description,
    toppings: [],
});
const toppingSelect = reactive({
    list: [],
    filter: 2,//selected
});
const image = ref(null);
const imagePreviewElement = ref(null);
const isImagePreUpload = ref(false);
const submitButton = ref(null);
const deleteButtonElement = ref(null);
const error = ref('');
const isLoadingToppings = ref(true);
onMounted(async () => {
    try{
        [form.toppings, toppingSelect.list] = await Promise.all([
            getToppingByMealAPI(props.meal.id),
            getAllToppingAPI()
        ]);
        isLoadingToppings.value=false;
    }catch(e){
        if(e.response){
            errorHandle(e.response.status, e);
        }
        console.log(e);
    }
})
async function onSubmit() {
    buttonSubmitting();
    try {
        validate(form);
        const formData = new FormData();
        if (image.value.files[0]) {
            formData.append('image', image.value.files[0]);
        }
        formData.append('name', form.name);
        formData.append('type', form.type);
        formData.append('price', moneyToNumber(form.price));
        for(let i=0;i<form.toppings.length;i++){
            formData.append('topping[]',form.toppings[i]);
        }
        formData.append('_method', 'put')
        if (form.description) {
            formData.append('description', form.description);
        }
        const response = await updateMealAPI(formData, props.meal.id);
        buttonNotSubmit();
        emits('successCb', response.data);
    } catch (e) {
        buttonNotSubmit();
        if (e.response) {
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
    submitButton.value.innerHTML = '??ang l??u...';
    submitButton.value.disabled = true;
}
function buttonNotSubmit() {
    submitButton.value.innerHTML = 'L??u thay ?????i';
    submitButton.value.disabled = false;
}
function imagePreview(e) {
    const preview = imagePreviewElement.value;
    preview.src = URL.createObjectURL(e.target.files[0]);
    isImagePreUpload.value = true;
}
function moneyConvertToNumber() {
    error.value = null;
    form.price = moneyToNumber(form.price);
}
function numberConvertToMoney() {
    form.price = numberToMoney(form.price);
}
function toggleTopping(id){
    addOrRemove(form.toppings,id);
}
function changeToppingFilter(filter){
    toppingSelect.filter=filter;
}
async function deleteMeal() {
    deleteButtonElement.value.innerHTML = "??ang x??a...";
    deleteButtonElement.value.disabled = true;
    try {
        await deleteMealAPI(props.meal.id);
        emits('successDelete', props.meal.id);
        deleteButtonElement.value.innerHTML = "X??a m??n";
        deleteButtonElement.value.disabled = false;
    } catch (e) {
        if (e.response) {
            errorHandle(e.response.status, e);
            return;
        }
        deleteButtonElement.value.innerHTML = "X??a m??n";
        deleteButtonElement.value.disabled = false;
        console.log(e);
    }
}
</script>
        
<template>
    <div class="w-full h-full fixed flex justify-center items-center left-0 top-0 z-50 bg-gray-800/20">
        <div ref="box" class="w-3/4 h-3/4 bg-gray-200 inline-block px-3 py-2 relative">
            <ButtonClose @close="emits('close')"></ButtonClose>
            <div class="text-xl">Ch???nh s???a</div>
            <ErrorDisplay :error="error" class="mt-9"></ErrorDisplay>
            <form @submit.prevent="onSubmit" class="mt-6">
                <div class="flex">
                    <div class="w-1/3 p-2 h-96 overflow-hidden">
                        <input @change="imagePreview" ref="image" type="file" class="hidden" id="imageUpload">
                        <label for="imageUpload">
                            <div class="cursor-pointer">
                                <img class="w-full" ref="imagePreviewElement"
                                    src="https://api.lorem.space/image/burger?w=300&h=300">
                            </div>
                        </label>
                    </div>
                    <div class="w-1/2 p-2">
                        <input @focus="clearError" v-model="form.name" type="text"
                            class="text-xl p-2 w-full outline-none bg-inherit" placeholder="T??n m??n ??n...">
                        <div class="flex">
                            <div class="flex mx-1.5 select-none" v-for="(tag,value) in tagMapping" :key="tag">
                                <input class="cursor-pointer" @change="clearError" :value="value" v-model="form.type"
                                    :id="'tag'+value" name="tag" type="radio">
                                <label class="pb-0.5 cursor-pointer" :for="'tag'+value">{{tag.name}}</label>
                            </div>
                        </div>
                        <LoadingElement v-if="isLoadingToppings" />
                        <ToppingSelect v-else :checked="form.toppings" :toppings="toppingSelect.list"
                            :filter="toppingSelect.filter" @toggleTopping="toggleTopping"
                            @changeFilter="changeToppingFilter" class="p-2" />
                        <input @focus="moneyConvertToNumber" @focusout="numberConvertToMoney" v-model="form.price"
                            class="p-2 bg-inherit outline-none" type="text" placeholder="Gi?? b??n...">
                        <textarea @focus="clearError" v-model="form.description"
                            class="w-full resize-none h-32 outline-none bg-inherit p-2 mt-1" type="text"
                            placeholder="Th??ng tin th??m..."></textarea>
                    </div>
                </div>
                <div class="flex p-2 mt-1 absolute bottom-1 right-0 w-full">
                    <div ref="deleteButtonElement" @click="deleteMeal"
                        class="text-white bg-red-600 p-2 w-24 text-center rounded hover:bg-red-500 disabled:bg-red-500 cursor-pointer">
                        X??a m??n
                    </div>
                    <button ref="submitButton" type="submit"
                        class="ml-auto w-32 transition hover:bg-blue-600 disabled:bg-blue-500 bg-blue-700 text-white rounded p-2">
                        L??u thay ?????i
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>