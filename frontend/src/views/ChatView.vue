<script>
import {toLink} from "@/utils.js";

export default {
    name: "ChatView",
    methods: {toLink}
}
</script>

<template>
    <div class="chat margin-all">
        <div class="chat_search">
            <img src="/search.svg" alt="">
            <input type="text" placeholder="Найти...">
        </div>
        <div class="chat_main">
            <div @click="toLink('dialog', 2)" v-for="el in 10">
                <img src="/avatar_1.png" alt="">
                <div>
                    <div class="chat_main_dialog_header">
                        <div class="chat_main_dialog_header_title">Константин</div>
                        <div class="chat_main_dialog_header_new"><div>2</div></div>
                    </div>
                    <div class="chat_main_dialog_message">
                        <div class="chat_main_dialog_message_last">Вы: Lorem ipsum dolor sit amet, consetetur sadipsci</div>
                        <img src="/checked.svg" alt="" class="chat_main_dialog_message_checked">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>