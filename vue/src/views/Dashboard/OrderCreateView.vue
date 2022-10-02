<script setup>
import { computed, onMounted, ref } from 'vue';
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
import paymentGateMapping from '../../mapping/paymentGate';
import validateOrder from '../../validate/order';
import FilterContainer from '../../components/FilterContainer.vue';
const meals = ref([]);
const filterType = ref(null);
const mealSearchBar = ref('');
const inCart = ref({});
const cartDetailVisible = ref(false);
const cartDetailElement = ref(null);
const token=getUserToken();
const phone=ref('');
const address=ref('');
const payment_gate=ref('');
const orderCreatedMessageVisible=ref(false);
const orderError=ref('');
const createOrderButton=ref(null);
const addressElement=ref(null);
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
        validateOrder(address.value,phone.value,payment_gate.value);
    }catch(e) {
        orderError.value=e;
        createOrderButtonEnable();
    }
    const meals={};
    Object.values(inCart.value).forEach((meal) => {
        meals[meal.id]={
            "quality":meal.quality
        };
    })
    try{
        const form={
            "payment_gate":payment_gate.value,
            "meals":meals,
        };
        if(phone.value!=''){
            form['phone']=phone.value;
        }
        if(address.value!=''){
            form['address']=address.value;
        }
        await axios({
            method:"POST",
            url:orderAPI,
            data:form,
            headers:{
                "Authorization": "Bearer " + token,
            }
        });
        afterCreatedOrder();
    }catch(error){
        errorHandle(error.response.status, error);
    }
    createOrderButtonEnable();
}
function afterCreatedOrder(){
    orderCreatedMessageVisible.value=true;
    inCart.value={};
    phone.value='';
    address.value='';
    payment_gate.value='';
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
async function findCustomer(){
    if(phone.value.length>=10){
        const input=addressElement.value;
        input.placeholder="Đang tìm kiếm...";
        try{
            const response=await axios({
                method:"GET",
                url:getCustomerByPhone,
                params:{
                    "phone": phone.value,
                },
                headers:{
                    "Authorization": "Bearer " + token,
                }
            });
            const customer=response.data.customer;
            input.disabled=false;
            if(customer==null){
                input.placeholder="Khách mới";
                return
            }
            input.placeholder='';
            address.value=customer.address;
        }catch(e){
            errorHandle(e.response.status, e);
        }
    }
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
                    <div class="bg-green-100 h-40 flex flex-col justify-center">
                        <div v-if="orderError!=''" class="h-8 flex items-center mx-2 text-red-600">{{orderError}}</div>
                        <div class="flex items-center mx-2 mb-2">
                            <div class="w-28">
                                <label for="phone">Số điện thoại:</label>
                            </div>
                            <div class="grow overflow-hidden max-w-[200px]"><input @keyup="findCustomer" @focus="orderError=''" v-model="phone" class="w-full py-1 px-2 ml-2 outline-none" id="phone" type="text"></div>
                        </div>
                        <div class="flex items-center mx-2">
                            <div class="w-28">
                                <label for="address">Địa chỉ:</label>
                            </div>
                            <div class="grow overflow-hidden max-w-[600px]"><input ref="addressElement" placeholder="Nhập số điện thoại trước!" disabled @focus="orderError=''" v-model="address" class="w-full py-1 px-2 ml-2 outline-none  disabled:bg-slate-100" id="address" type="text"></div>
                        </div>
                        <div class="flex mt-2 mx-2">
                            <label class="flex items-center mr-3 cursor-pointer" :key="key" v-for="(name,key) in paymentGateMapping">
                                <input v-model="payment_gate" class="mt-1" name="payment" type="radio" :value="key">
                                <div class="ml-0.5">{{name}}</div>
                            </label>
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
                            Tạo đơn</div>
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
    </div>
</template>