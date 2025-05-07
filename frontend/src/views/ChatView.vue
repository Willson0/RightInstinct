<script>
import {toLink} from "@/utils.js";

export default {
    name: "ChatView",
    methods: {toLink},
    data () {
        return {
            query: "",
        }
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
    }
}
</script>

<template>
    <div class="chat margin-all">
        <div class="chat_search">
            <img src="/search.svg" alt="">
            <input v-model="query" type="text" placeholder="Найти...">
        </div>
        <div class="chat_main">
            <div @click="toLink('dialog', dialog.user.id)" v-for="dialog in user.chat?.filter(c => c.user.fullname.toLowerCase().includes(query.toLowerCase()) )">
                <img :src="dialog.user.avatar" alt="">
                <div>
                    <div class="chat_main_dialog_header">
                        <div class="chat_main_dialog_header_title">{{ dialog.user.fullname }}</div>
                        <div class="chat_main_dialog_header_new" v-if="dialog.unreaded">
                            <div>{{ dialog.unreaded }}</div>
                        </div>
                    </div>
                    <div class="chat_main_dialog_message">
                        <div class="chat_main_dialog_message_last">
                            {{ !dialog.from_last_message ? 'Вы: ' : '' }}{{ dialog.last_message }}
                        </div>
                        <img v-if="dialog.checked" src="/checked.svg" alt="" class="chat_main_dialog_message_checked">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>