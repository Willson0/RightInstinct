<script>
import {notify, toLink, utcToLocalTime} from "@/utils.js";
import axios from "axios";
import config from "@/config.json";

export default {
    name: "DialogView",
    data () {
        return {
            data: null,
            interval: null,
            message: "",
            isLoading: false,
        }
    },
    async mounted () {
        if (!this.$route.query.id) return toLink('chat');
        if (Number(this.$route.query.id) === this.user.id) toLink("chat", null, null, 0);

        await this.fetchData();
        this.interval = setInterval(() => {
            this.fetchData();
        }, 5000);

        this.$store.dispatch("updateInterval", this.interval);
    },
    methods: {
        toLink,
        utcToLocalTime,
        async fetchData () {
            await axios.post(config.backend + "chat/" + this.$route.query.id, {
                "initData": window.Telegram.WebApp.initData,
            }).then((response) => {
                this.data = response.data;

                requestAnimationFrame(() => {
                    const element = document.querySelector('.dialog_main');
                    element.scrollTop = element.scrollHeight;
                });

                this.user.chat.find(el => String(el.user.id) === this.$route.query.id).unreaded = 0;
                this.$store.dispatch("updateUser", this.user);
            }).catch((error) => {
                if (error.response) {
                    return alert (`An error occurred: ${error.message}`);
                }
            })
        },
        async sendMessage () {
            if (this.isLoading) return;
            if (this.message.length === 0) return;

            const element = document.querySelector('.dialog_main');
            element.scrollTo({
                top: element.scrollHeight,
                behavior: 'smooth'
            });

            this.data.dialog.push({
                "sender_id": this.user.id,
                "recipient_id": this.data.companion.id,
                "message": this.message,
                "readed": 0,
            })

            this.isLoading = true;
            await axios.post(config.backend + "chat/" + this.$route.query.id + "/send", {
                "initData": window.Telegram.WebApp.initData,
                "message": this.message
            }).then((response) => {
                this.data = response.data;
                this.message = "";
            }).catch((error) => {
                if (error.response) {
                    return alert (`An error occurred: ${error.message}`);
                }
            }).finally(() => {
                this.isLoading = false;
            })
        }
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
    watch: {
        user () {
            if (Number(this.$route.query.id) === this.user.id) toLink("chat", null, null, 0);
        }
    }
}
</script>

<template>
    <div class="dialog">
        <div class="dialog_header" @click="toLink('user', data?.companion.id)">
            <div>
                <img :src="data?.companion.avatar" alt="">
                <div>{{ data?.companion.fullname }}</div>
            </div>
        </div>
        <div class="dialog_main margin-all">
            <div :class="message.sender_id === user.id ? 'dialog_main_from_user' : 'dialog_main_to_user'"
                 v-for="message in data?.dialog">
                <div class="dialog_main_text">{{ message.message }}</div>
                <div class="dialog_main_footer">
                    <div class="dialog_main_footer_time">{{ utcToLocalTime(message.created_at) }}</div>
                    <img v-if="message.readed" src="/checked.svg" alt="">
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" viewBox="0 0 18 21" fill="none">
                    <path d="M17.7437 19.9604C12.5804 20.9705 7.33521 18.3175 5.59562 16.5532C7.14607 12.3905 -3.8542 2.87887 3.14003 2.59482C4.75731 2.52922 5.9668 -1.7634 11.9778 1.17863C12.0512 2.46355 12.2105 6.91513 12.2412 7.67289C12.6667 18.1643 18.7184 19.3171 17.7437 19.9604Z" fill="#F1EBD8"/>
                </svg>
            </div>
        </div>
        <div class="dialog_input margin-side">
            <img src="/attach.svg" alt="">
            <img src="/smile.svg" alt="">
            <input type="text" v-model="message" placeholder="Введите сообщение...">
            <img @click="sendMessage" src="/plane.svg" alt="">
        </div>
    </div>
</template>