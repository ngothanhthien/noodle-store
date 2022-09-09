<script setup>

</script>

<template>
    <div class="flex w-1/2">
        <div>
            <select name="type" class="form-select border focus:border focus:outline-none py-1 px-2">
                <option value="name">Theo tên</option>
            </select>
        </div>
        <div class="grow">
           <input type="text" name="search" class="py-1 px-2 border focus:border focus:outline-none w-full">
        </div>
    </div>
</template>