<script setup>
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';
import { getAllCustomerAPI } from '../../api'
import { getUserToken } from '../../logic/auth';
import errorHandle from '../../logic/errorHandle';
import {numberToMoney} from '../../logic/money'
import PaginateContainer from '../../components/PaginateContainer.vue';
import LoadingElement from '../../components/LoadingElement.vue';
const token = getUserToken();
const customers = ref();
const page = reactive({
    total: 1,
    current: 1,
})
const isCustomersLoading = ref(true);
onMounted(async () => {
    try {
        const response = await axios({
            url: getAllCustomerAPI,
            method: 'GET',
            headers: {
                "Authorization": "Bearer " + token
            }
        })
        customers.value = response.data.data;
        page.total = response.data.meta.total;
        isCustomersLoading.value = false;
    } catch (e) {
        if (e.response) {
            errorHandle(e.response.status, e);
            return;
        }
        console.log(e);
    }
});
async function getCustomerOnPage(p) {
    page.current = p;
    try {
        const response = await axios({
            url: getAllCustomerAPI + '?page=' + p,
            method: 'GET',
            headers: {
                "Authorization": "Bearer " + token
            }
        })
        customers.value = response.data.data;
    } catch (e) {
        if (e.response) {
            errorHandle(e.response.status, e);
            return;
        }
        console.log(e);
    }
}
</script>
<template>
    <div>
        <div class="text-lg mb-5">Khách hàng</div>
        <LoadingElement v-if="isCustomersLoading" />
        <div v-else>
            <table class="table-auto w-full border-collapse border border-slate-300">
                <tr>
                    <th class="border border-slate-300 p-2">Số Điện thoại</th>
                    <th class="border border-slate-300 p-2">Địa chỉ</th>
                    <th class="border border-slate-300 p-2">Tổng chi</th>
                    <th class="border border-slate-300 p-2">Lần mua gần nhất</th>
                </tr>
                <tr class="hover:bg-gray-200 cursor-pointer" v-for="customer in customers">
                    <td class="border border-slate-300 text-center p-2">{{customer.phone}}</td>
                    <td class="border border-slate-300 text-center p-2">{{customer.address}}</td>
                    <td class="border border-slate-300 text-center p-2">{{numberToMoney(customer.purchased)}}</td>
                    <td class="border border-slate-300 text-center p-2">{{customer.last_bought}}</td>
                </tr>
            </table>
            <PaginateContainer @pageClicked="getCustomerOnPage" :currentPage="page.current" :totalPage="page.total" />
        </div>
    </div>
</template>