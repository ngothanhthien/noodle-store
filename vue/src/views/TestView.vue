<script setup>
import InfoModal from '../components/InfoModal.vue';

</script>

<template>
    <InfoModal >
        <template v-slot:content>
            <div class="text-lg mb-4 select-none">
                Thông tin đăng nhập
            </div>
            <div>
                <div class="text-left">
                    <span class="mr-2 select-none">Tài khoản:</span>
                    <span>ABC</span>
                </div>
                <div class="text-left mb-6">
                    <span class="mr-2 select-none">Mật khẩu:</span>
                    <span>ABCXYzzzzzzzzzzz</span>
                </div>
            </div>
        </template>
        <template v-slot:button>
            OK
        </template>
    </InfoModal>
</template>