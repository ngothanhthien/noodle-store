<script setup>
import { computed, onMounted, reactive, ref,unref } from 'vue';
import MealCard from '../../components/MealCard.vue';
import { arrayFindObjectById } from '../../logic/array'
import mealTypes from '../../mapping/type';
import ShoppingCartIcon from '../../components/icons/ShoppingCartIcon.vue';
import { numberToMoney } from '../../logic/money'
import CartItem from '../../components/CartItem.vue';
import { onClickOutside } from '@vueuse/core';
import {getAllMealAPI,orderAPI,getCustomerByPhone} from '../../api';
import {getUserToken} from '../../logic/auth';
import errorHandle from '../../logic/errorHandle';
import axios from 'axios';
import InfoModal from '../../components/InfoModal.vue';
import validateOrder from '../../validate/order';
import FilterContainer from '../../components/FilterContainer.vue';
import LoadingElement from '../../components/LoadingElement.vue';
import MealSelectDetail from '../../components/MealSelectDetail.vue';
const meals = ref([]);
const filterType = ref(null);
const mealSearchBar = ref('');
const inCart = ref({});
const cartDetailVisible = ref(false);
const cartDetailElement = ref(null);
const token=getUserToken();
const form=reactive({
    address:'',
    payment_gate:'',
    phone:'',
});
const orderCreatedMessageVisible=ref(false);
const orderError=ref('');
const createOrderButton=ref(null);
const addressElement=ref(null);
const isLoadingMeals=ref(true);
onClickOutside(cartDetailElement, () => {
    cartDetailVisible.value = false;
})
onMounted(async () => {
    try{
        const response=await axios({
            method:'GET',
            url:getAllMealAPI,
            headers:{
                'Authorization': 'Bearer '+token
            },
        });
        isLoadingMeals.value=false;
        meals.value=response.data.meals;
    }catch(error){
        errorHandle(error.response.status, error);
    }
})
const filteredMeal = computed(() => {
    if (filterType.value == null) {
        if(mealSearchBar.value.length<3){
            return meals.value;
        }
        return meals.value.filter((meal) => meal.name.toLowerCase().includes(mealSearchBar.value.toLowerCase()));
    }
    if(mealSearchBar.value.length<3){
        return meals.value.filter((meal) => meal.type == filterType.value);
    }
    return meals.value.filter((meal) => meal.type == filterType.value).filter((meal) => meal.name.toLowerCase().includes(mealSearchBar.value.toLowerCase()))
})
const totalMeal = computed(() => {
    let total = 0;
    Object.values(inCart.value).forEach((meal) => {
        total += meal.quality;
    })
    return total;
})
const totalPrice = computed(() => {
    let total = 0;
    Object.values(inCart.value).forEach((meal) => {
        total += meal.quality * meal.price;
    })
    return numberToMoney(total);
})
async function createOrder() {
    createOrderButtonDisable();
    try{
        validateOrder(unref(form));
        const meals={};
        Object.values(inCart.value).forEach((meal) => {
            meals[meal.id]={
                "quality":meal.quality
            };
        })
        const data={
            "payment_gate":form.payment_gate,
            "meals":meals,
        };
        if(form.phone!=''){
            data['phone']=form.phone;
        }
        if(form.address!=''){
            data['address']=form.address;
        }
        await axios({
            method:"POST",
            url:orderAPI,
            data:data,
            headers:{
                "Authorization": "Bearer " + token,
            }
        });
        afterCreatedOrder();
    }catch(e){
        if(e.response){
            errorHandle(e.response.status, e);
            return;
        }
        orderError.value=e;
        createOrderButtonEnable();
    }
}
function afterCreatedOrder(){
    orderCreatedMessageVisible.value=true;
    inCart.value={};
    form.address='';
    form.payment_gate='';
    form.phone='';
    orderError.value='';
    cartDetailVisible.value=false;
    createOrderButtonEnable();
}
function increaseMeal(id) {
    const meal = inCart.value[id];
    if (meal) {
        meal.quality++;
        return;
    }
    const newMeal = arrayFindObjectById(meals.value, id);
    newMeal['quality'] = 1;
    inCart.value[id] = newMeal;
}
function decreaseMeal(id) {
    const meal = inCart.value[id];
    if (meal.quality == 1) {
        delete inCart.value[id];
        return;
    }
    meal.quality--;
}
function addTypeToFilter(type) {
    if (filterType.value == type) {
        filterType = '';
        return;
    }
    filterType.value = type;
}
function createOrderButtonDisable(){
    const button=createOrderButton.value;
    button.disabled = true;
    button.contentText ="Đang tạo...";
}
function createOrderButtonEnable(){
    const button=createOrderButton.value;
    button.disabled =false;
    button.contentText="Tạo đơn";
}
</script>
<template>
    <div class="h-full flex flex-col overflow-y-scroll">
        <div class="text-2xl">Tạo đơn hàng</div>
        <div class="mx-auto w-1/2 my-4">
            <input v-model="mealSearchBar" type="text"
                class="w-full outline-none px-2 py-1 rounded outline-green-700 outline-offset-0"
                placeholder="Tên sản phẩm...">
        </div>
        <FilterContainer @changeFilter="addTypeToFilter" :currentFilter="filterType" :filterList="mealTypes"></FilterContainer>
        <LoadingElement v-if="isLoadingMeals" class="mt-8 ml-3" />
        <div class="flex flex-wrap grow">
            <div class="w-1/5 px-2 py-2" :key="meal.id" v-for="meal in filteredMeal">
                <MealCard :quality="inCart[meal.id]?inCart[meal.id].quality:0" @imageClicked="increaseMeal(meal.id)"
                    @increase="increaseMeal(meal.id)" @decrease="decreaseMeal(meal.id)" :meal="meal"></MealCard>
            </div>
        </div>
        <div v-if="totalMeal>0" :class="{
            'sticky bottom-0 h-12':!cartDetailVisible,
            'absolute w-full h-full left-0 top-0 z-50 bg-gray-700/20 pt-20 px-5':cartDetailVisible,
        }" class="shadow-md select-none">
            <div ref="cartDetailElement"
                class="h-full bg-white rounded-t-lg">
                <div v-if="cartDetailVisible" class="h-[calc(100%-3rem)] border-b px-3 py-2">
                    <div class="overflow-y-auto h-[calc(100%-9.5rem)]">
                        <div :key="id" v-for="(item,id) in inCart">
                            <CartItem @increase="increaseMeal(id)" @decrease="decreaseMeal(id)" :item="item"></CartItem>
                        </div>
                    </div>
                </div>
                <div 
                    class="flex items-center bg-white cursor-pointer relative h-12">
                    <div @click="cartDetailVisible=!cartDetailVisible" class="h-full grow py-1 flex items-center">
                        <div class="relative h-full flex items-center w-10 ml-1">
                            <ShoppingCartIcon class="w-8 text-green-700 h-8 fill-current" />
                            <div
                                class="absolute text-white select-none bg-green-700 text-xs w-5 h-5 flex items-center justify-center rounded-full top-0 right-0">
                                {{totalMeal}}</div>
                        </div>
                        <div class="mr-3 ml-auto select-none text-red-600">{{totalPrice}}</div>
                    </div>
                    <div @click="createOrder" class="ml-auto h-full">
                        <div v-if="cartDetailVisible"
                        ref="createOrderButton"
                            class="ml-auto uppercase hover:bg-green-600 transition cursor-pointer h-full select-none bg-green-700 text-white px-4 flex items-center">
                            Thanh Toán</div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="orderCreatedMessageVisible">
            <InfoModal @close="orderCreatedMessageVisible=false">
                <template v-slot:content>Đơn hàng đã được tạo</template>
                <template v-slot:button>OK</template>
            </InfoModal>
        </div>
        <MealSelectDetail />
    </div>
</template>