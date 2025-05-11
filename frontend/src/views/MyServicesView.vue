<script>
import PostBlock from "@/components/PostBlock.vue";
import {toLink} from "@/utils.js";

export default {
    name: "MyServicesView",
    methods: {toLink},
    components: {PostBlock},
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
}
</script>

<template>
    <div class="myposts margin-all">
        <h1>Мои услуги</h1>
        <div class="myposts_main">
            <post-block :object="object" v-for="object in user.my?.services" :my="true" type="service"/>
        </div>
        <div @click="toLink('store', 'service')" class="home_block_button green-bgc button">
            <div>
                <img src="/plus.svg" alt="">
                <div class="button">Добавить услугу</div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>