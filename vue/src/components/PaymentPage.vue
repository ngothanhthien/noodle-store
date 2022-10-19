<script setup>
async function findCustomer(){
    if(form.phone.length>=10){
        const input=addressElement.value;
        input.placeholder="Đang tìm kiếm...";
        try{
            const response=await axios({
                method:"GET",
                url:getCustomerByPhone,
                params:{
                    "phone": form.phone,
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
            form.address=customer.address;
        }catch(e){
            errorHandle(e.response.status, e);
        }
    }
}
</script>
<template>
    <div class="bg-green-100 h-40 flex flex-col justify-center relative pt-3">
        <ErrorDisplay :error="orderError" class="ml-2 mt-1"></ErrorDisplay>
        <div class="flex items-center mx-2 mb-2">
            <div class="w-28">
                <label for="phone">Số điện thoại:</label>
            </div>
            <div class="grow overflow-hidden max-w-[200px]"><input @keyup="findCustomer" @focus="orderError=''"
                    v-model="form.phone" class="w-full py-1 px-2 ml-2 outline-none" id="phone" type="text"></div>
        </div>
        <div class="flex items-center mx-2">
            <div class="w-28">
                <label for="address">Địa chỉ:</label>
            </div>
            <div class="grow overflow-hidden max-w-[600px]"><input ref="addressElement"
                    placeholder="Nhập số điện thoại trước!" disabled @focus="orderError=''" v-model="form.address"
                    class="w-full py-1 px-2 ml-2 outline-none  disabled:bg-slate-100" id="address" type="text"></div>
        </div>
        <div class="flex mt-2 mx-2">
            <label class="flex items-center mr-3 cursor-pointer" :key="key" v-for="(name,key) in paymentGateMapping">
                <input v-model="form.payment_gate" class="mt-1" name="payment" type="radio" :value="key">
                <div class="ml-0.5">{{name}}</div>
            </label>
        </div>
    </div>
</template>