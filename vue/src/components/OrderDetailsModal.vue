<script setup>
import { computed, onMounted, ref } from 'vue';
import ButtonClose from './ButtonClose.vue';
import orderState from '../mapping/orderState';
import paymentGate from '../mapping/paymentGate';
import { numberToMoney } from '../logic/money';
import { onClickOutside } from '@vueuse/core';
import LoadingElement from './LoadingElement.vue';
import { orderAPI } from '../api'
import { getUserToken } from '../logic/auth'
import errorHandle from '../logic/errorHandle';
import axios from 'axios';
import OrderHandleButtons from './OrderHandleButtons.vue';
const token = getUserToken();
const emits = defineEmits(['close', 'cancel', 'fail', 'success']);
const props = defineProps(['id']);
const box = ref(null);
const order = ref(null);
const orderDetailExits = ref(true)
onMounted(async () => {
    try {
        const response = await axios({
            method: 'GET',
            url: orderAPI + props.id,
            headers: {
                "Authorization": "Bearer " + token,
            }
        });
        order.value = response.data;
    } catch (e) {
        errorHandle(e.response.status, e);
        orderDetailExits.value = false;
    }
});
const staff = computed(() => {
    return order.user != null ? order.user : 'Admin';
});
onClickOutside(box, () => {
    emits('close');
})
</script>

<template>
    <div class="absolute h-full top-0 left-0 p-12 w-full bg-gray-800/20">
        <div ref="box" class="bg-white h-full rounded-lg px-3 py-1 relative overflow-y-auto">
            <ButtonClose @close="emits('close')"></ButtonClose>
            <LoadingElement :isNotFound="!orderDetailExits" v-if="order==null" />
            <div v-else>
                <div>
                    <span class="text-xl mb-2">Đơn hàng #</span>
                    <span class="text-xl mb-2">{{id}}</span>
                    <span class="ml-7">{{order.created_at}}</span>
                </div>
                <div>
                    <span>Chốt đơn:</span>
                    <span class="ml-2">{{staff}}</span>
                </div>
                <div>
                    <span>Trạng thái:</span>
                    <span class="ml-2"
                        :class="orderState[order.state]['css']">{{orderState[order.state]['name']}}</span>
                </div>
                <div class="flex">
                    <div>Thông tin khách:</div>
                    <div class="ml-2">
                        <div>{{paymentGate[order.payment_gate]}}</div>
                        <div v-if="order.customer!=null">{{order.customer.phone}}</div>
                        <div v-if="order.customer!=null">{{order.customer.address}}</div>
                    </div>
                </div>
                <div>
                    <span>Tổng đơn:</span>
                    <span class="ml-2">{{numberToMoney(order.total_price)}}</span>
                </div>
                <div>
                    <table class="table-auto w-5/6">
                        <tr>
                            <th></th>
                            <td class="text-lg">Tên món</td>
                            <td class="text-lg">Đơn giá</td>
                        </tr>
                        <tr v-for="meal in order.meals" :key="meal.id">
                            <td><img class="w-20 h-20 inline-block"
                                    src="https://api.lorem.space/image/burger?w=150&h=150" alt=""></td>
                            <td class="">
                                <div>{{meal.name}}</div>
                                <div>
                                    <span>x</span>
                                    <span>{{meal.order_details.quality}}</span>
                                </div>
                            </td>
                            <td class="text-red-700">
                                {{numberToMoney(meal.order_details.price)}}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="flex items-center">
                    <div>
                        Tác vụ:
                    </div>
                    <div>
                        <OrderHandleButtons @cancel="emits('cancel',order.id)" @fail="emits('fail',order.id)" @success="emits('success',order.id)"
                            :state="order.state" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>