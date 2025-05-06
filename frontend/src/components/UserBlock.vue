<script>
import {toLink} from "@/utils.js";

export default {
    name: "UserBlock",
    methods: {toLink},
    props: {
        name: {
            type: String,
            required: true,
        },
        city: {
            type: String,
            required: true,
        },
        avatar: {
            type: String,
            required: true,
        },
        rating: {
            type: Number,
            required: true
        }
    }
}
</script>

<template>
    <div class="profile_header user">
        <img :src="avatar" alt="">
        <div class="profile_info">
            <div class="profile_info_name">{{ name }}</div>
            <div class="profile_info_city">{{ city }}</div>
            <div class="block_post_img">
                <div class="green-bgc">
                    <img src="/star.svg" alt="">
                    <div class="grey-light">{{ rating }}</div>
                </div>
            </div>
        </div>
        <button @click="toLink('chat')">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M22 12C22 17.5229 17.5229 22 12 22C9.01327 22 2 22 2 22C2 22 2 14.5361 2 12C2 6.47715 6.47717 2 12 2C17.5229 2 22 6.47715 22 12Z" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7 9H16" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7 13H16" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7 17H12" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</template>

<style scoped>

</style>