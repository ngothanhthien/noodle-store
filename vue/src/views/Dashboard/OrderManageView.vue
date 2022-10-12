<script setup>
import { onMounted, reactive, ref, watch } from 'vue';
import FilterContainer from '../../components/FilterContainer.vue';
import orderStates from '../../mapping/orderState'
import PaginateContainer from '../../components/PaginateContainer.vue';
import PaymentMap from '../../mapping/paymentGate';
import StateMap from '../../mapping/orderState';
import { numberToMoney } from '../../logic/money'
import { getOrders, orderStateChange } from '../../api'
import { getUserToken } from '../../logic/auth';
import axios from 'axios';
import LoadingElement from '../../components/LoadingElement.vue';
import errorHandle from '../../logic/errorHandle';
import OrderDetailsModal from '../../components/OrderDetailsModal.vue';
import InfoModal from '../../components/InfoModal.vue';
import OrderHandleButtons from '../../components/OrderHandleButtons.vue';
const ORDER_SUCCESS = 0;
const ORDER_DELIVERY = 1;
const ORDER_FAIL = 2;
const ORDER_CANCEL = 3;
const SEARCH_BY_ID = 0;
const SEARCH_BY_PHONE = 1;
const searchType = ref(SEARCH_BY_ID);
const token = getUserToken();
const orderSearchBar = ref('');
const orderFilter = ref(ORDER_DELIVERY.toString());
const paginate = reactive({
    totalPage: 1,
    url: getOrders,
    currentPage: 1,
})
const orders = ref([]);
const isLoadingOrder = ref(true);
const isMovingPage = ref(false);
const orderDetailID = ref(null);
const isUnAuthorizeOrder = ref(false);
onMounted(async () => {
    try {
        const response = await axios({
            method: "GET",
            url: getOrders + ORDER_DELIVERY,
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });
        orders.value = response.data.data;
        paginate.totalPage = response.data.meta['last_page'];
        paginate.url = response.data.meta['path'];
        isLoadingOrder.value = false;
    } catch (error) {
        console.log(error);
        errorHandle(error.response.status, error);
    }
});
watch(orderFilter, async (state) => {
    if (state == null) {
        state = '';
    }
    isLoadingOrder.value = true;
    try {
        const response = await axios({
            method: "GET",
            url: getOrders + state,
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });
        orders.value = response.data.data;
        paginate.currentPage = 1;
        paginate.totalPage = response.data.meta['last_page'];
        paginate.url = response.data.meta['path'];
        isLoadingOrder.value = false;
    } catch (error) {
        errorHandle(error.response.status, error);
    }
});
async function changePage(page) {
    if (page == paginate.currentPage) {
        return;
    }
    paginate.currentPage = page;
    isMovingPage.value = true;
    try {
        const response = await axios({
            method: "GET",
            url: paginate.url + '?page=' + page,
            headers: {
                'Authorization': 'Bearer ' + token
            }
        })
        orders.value = response.data.data;
        isMovingPage.value = false;
    } catch (error) {
        if (e.response) {
            if (e.response.status == 403) {
                isUnAuthorizeOrder.value = true;
                return;
            }
            errorHandle(e.response.status, e);
        }
    }
}
async function failOrder(id) {
    try {
        await axios({
            url: orderStateChange.fail + id,
            method: "PATCH",
            headers: {
                "Authorization": "Bearer " + token
            }
        });
        const order = orders.value.find(obj => obj.id == id);
        order.state = ORDER_FAIL;
    } catch (e) {
        if (e.response) {
            if (e.response.status == 403) {
                isUnAuthorizeOrder.value = true;
                return;
            }
            errorHandle(e.response.status, e);
        }
    }
}
async function verifyOrder(id) {
    try {
        await axios({
            url: orderStateChange.success + id,
            method: "PATCH",
            headers: {
                "Authorization": "Bearer " + token
            }
        });
        const order = orders.value.find(obj => obj.id == id);
        order.state = ORDER_SUCCESS;
    } catch (e) {
        if (e.response) {
            if (e.response.status == 403) {
                isUnAuthorizeOrder.value = true;
                return;
            }
            errorHandle(e.response.status, e);
        }
    }
}
async function cancelOrder(id) {
    try {
        await axios({
            url: orderStateChange.cancel + id,
            method: "PATCH",
            headers: {
                "Authorization": "Bearer " + token
            }
        });
        const order = orders.value.find(obj => obj.id == id);
        order.state = 3;
    } catch (e) {
        if (e.response) {
            if (e.response.status == 401) {
                isUnAuthorizeOrder.value = true;
            }
        }
    }
}
function orderDetail(id) {
    orderDetailID.value = id;
}
function search() {
    if (searchType.value == SEARCH_BY_ID) {
        orderDetailID.value = orderSearchBar.value;
    } else if (searchType.value == SEARCH_BY_PHONE) {
        console.log('Tính năng đang nâng cấp');
    }
}
</script>

<template>
    <div class="h-full flex flex-col">
        <div class="text-2xl">Xử lý đơn hàng</div>
        <form @submit.prevent class="mx-auto w-1/2 my-4 flex">
            <select class="rounded-l-lg px-1 border-gray-500 border outline-none" v-model="searchType"
                name="searchType">
                <option :value="SEARCH_BY_ID">Theo mã</option>
                <option :value="SEARCH_BY_PHONE">Theo số điện thoại</option>
            </select>
            <input v-model="orderSearchBar" type="text"
                class="w-full outline-none px-2 py-1 border border-l-0 border-gray-500" placeholder="Tìm kiếm...">
            <button type="submit" @click="search" class="bg-blue-600 rounded-r-lg text-white px-2 py-2 min-w-max">Tìm
                kiếm</button>
        </form>
        <FilterContainer @changeFilter="(state)=>{orderFilter=state}" :filterList="orderStates"
            :currentFilter="orderFilter"></FilterContainer>
        <div class="mt-5 grow overflow-y-auto">
            <LoadingElement v-if="isLoadingOrder" class="mt-8 ml-3" />
            <div v-else>
                <LoadingElement v-if="isMovingPage" class="mt-8 ml-3" />
                <table v-else class="table-auto table border-collapse w-full">
                    <tr class="bg-gray-300">
                        <th class="p-2 border-gray-400 border">Mã</th>
                        <th class="p-2 border-gray-400 border">Thông tin</th>
                        <th class="p-2 border-gray-400 border">Tổng tiền</th>
                        <th class="p-2 border-gray-400 border">Thời gian</th>
                        <th class="p-2 border-gray-400 border">Trạng thái</th>
                        <th class="p-2 border-gray-400 border">Tác vụ</th>
                    </tr>
                    <tr class="hover:bg-gray-200" v-for="order in orders" :key="order.id">
                        <td class="text-center border-gray-400 border  cursor-pointer" @click="orderDetail(order.id)">
                            {{order.id}}</td>
                        <td class="text-center border-gray-400 border  cursor-pointer" @click="orderDetail(order.id)">
                            <div>{{PaymentMap[order.payment_gate]}}</div>
                            <div v-if="order.customer!=null">
                                <div>{{order.customer.phone}}</div>
                                <div>{{order.customer.address}}</div>
                            </div>
                        </td>
                        <td class="text-center border-gray-400 border p-2 cursor-pointer"
                            @click="orderDetail(order.id)">
                            {{numberToMoney(order.total_price)}}</td>
                        <td class="text-center border-gray-400 border p-2 cursor-pointer"
                            @click="orderDetail(order.id)">
                            <span class="mr-2">Tạo lúc:</span>
                            <span>{{order.created_at}}</span>
                        </td>
                        <td class="text-center border-gray-400 border p-2 cursor-pointer" @click="orderDetail(order.id)"
                            :class="StateMap[order.state]['css']">
                            {{StateMap[order.state]['name']}}</td>
                        <td class="border-gray-400 border p-2">
                            <OrderHandleButtons @cancel="cancelOrder(order.id)" @fail="failOrder(order.id)"
                                @success="verifyOrder(order.id)" :state="order.state" class="mx-auto" />
                        </td>
                    </tr>
                </table>
            </div>
            <PaginateContainer v-if="!isLoadingOrder" @pageClicked="changePage" :currentPage="paginate.currentPage"
                :totalPage="paginate.totalPage" />
        </div>
    </div>
    <InfoModal @close="isUnAuthorizeOrder=false" v-if="isUnAuthorizeOrder">
        <template #content>
            Bạn không phải người tạo đơn hàng này
        </template>
        <template #button>
            Đã hiểu
        </template>
    </InfoModal>
    <OrderDetailsModal @cancel="cancelOrder" @fail="failOrder" @success="verifyOrder" @close="orderDetailID=null"
        v-if="orderDetailID!=null" :id="orderDetailID" />
</template>