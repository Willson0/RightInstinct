<script>
import {endLoading, notify, toLink, utcToLocalTime} from "@/utils.js";
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
            firstLoading: true,
            attachment: [],
            config: config,
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

                if (this.firstLoading) {
                    this.firstLoading = false;
                    endLoading("loading_dialog");

                    const element = document.querySelector('.dialog_main');
                    this.$nextTick(() => {
                        requestAnimationFrame(() => {
                            element.scrollTo({
                                top: element.scrollHeight,
                                // bottom: 0,
                                behavior: 'instant'
                            });
                        });
                    });
                }

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
            if (this.message && this.message.length === 0) return;

            this.data.dialog.push({
                "sender_id": this.user.id,
                "recipient_id": this.data.companion.id,
                "message": this.message,
                "readed": 0,
                "created_at": new Date().toISOString(),
            })

            requestAnimationFrame(() => {
                const element = document.querySelector('.dialog_main');
                element.scrollTo({
                    top: element.scrollHeight + 100,
                    // bottom: 0,
                    behavior: 'smooth'
                });
            });

            let formData = new FormData();
            formData.append("initData", window.Telegram.WebApp.initData);
            formData.append("message", this.message);
            for (let file of this.attachment) formData.append("attachments[]", file);

            this.isLoading = true;
            await axios.post(config.backend + "chat/" + this.$route.query.id + "/send",
                formData).then((response) => {
                this.data = response.data;
                this.message = "";
                this.attachment = [];
            }).catch((error) => {
                if (error.response) {
                    return alert (`An error occurred: ${error.message}`);
                }
            }).finally(() => {
                this.isLoading = false;
            })
        },
        addFiles (ev) {
            for (let file of ev.target.files) {
                this.attachment.push(file);
                this.attachment[this.attachment.length - 1].preview = URL.createObjectURL(file);
            }
            ev.target.value = "";
        },
        removePhoto (index) {
            this.attachment.splice(index, 1);
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
    <div class="loading loading_dialog"></div>
    <div class="dialog">
        <div class="dialog_header" @click="toLink('user', data?.companion.id)">
            <div>
                <img :src="data?.companion.avatar" alt="">
                <div>{{ data?.companion.fullname }}</div>
            </div>
        </div>
        <div class="dialog_main margin-all">
            <div :style="message.attachments?.length ? 'max-width: 100%' : ''"
                :class="message.sender_id === user.id ? 'dialog_main_from_user' : 'dialog_main_to_user'"
                 v-for="message in data?.dialog">
                <div class="dialog_main_photos">
                    <img :src="config.storage + photo.url" v-for="photo in message.attachments" alt="">
                </div>
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
        <div v-if="attachment.length" class="dialog_attachment">
            <div v-for="(file, key) in attachment">
                <div class="dialog_attachment_trash" @click="removePhoto(key)">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.75 6.16667C2.75 5.70644 3.09538 5.33335 3.52143 5.33335L6.18567 5.3329C6.71502 5.31841 7.18202 4.95482 7.36214 4.41691C7.36688 4.40277 7.37232 4.38532 7.39185 4.32203L7.50665 3.94993C7.5769 3.72179 7.6381 3.52303 7.72375 3.34536C8.06209 2.64349 8.68808 2.1561 9.41147 2.03132C9.59457 1.99973 9.78848 1.99987 10.0111 2.00002H13.4891C13.7117 1.99987 13.9056 1.99973 14.0887 2.03132C14.8121 2.1561 15.4381 2.64349 15.7764 3.34536C15.8621 3.52303 15.9233 3.72179 15.9935 3.94993L16.1083 4.32203C16.1279 4.38532 16.1333 4.40277 16.138 4.41691C16.3182 4.95482 16.8778 5.31886 17.4071 5.33335H19.9786C20.4046 5.33335 20.75 5.70644 20.75 6.16667C20.75 6.62691 20.4046 7 19.9786 7H3.52143C3.09538 7 2.75 6.62691 2.75 6.16667Z" fill="#fff"/>
                        <path d="M11.6068 21.9998H12.3937C15.1012 21.9998 16.4549 21.9998 17.3351 21.1366C18.2153 20.2734 18.3054 18.8575 18.4855 16.0256L18.745 11.945C18.8427 10.4085 18.8916 9.6402 18.45 9.15335C18.0084 8.6665 17.2628 8.6665 15.7714 8.6665H8.22905C6.73771 8.6665 5.99204 8.6665 5.55047 9.15335C5.10891 9.6402 5.15777 10.4085 5.25549 11.945L5.515 16.0256C5.6951 18.8575 5.78515 20.2734 6.66534 21.1366C7.54553 21.9998 8.89927 21.9998 11.6068 21.9998Z" fill="#fff"/>
                    </svg>
                </div>
                <img :src="file.preview" alt="">
            </div>
        </div>
        <div class="dialog_input margin-side">
            <label for="attach">
                <img src="/attach.svg" alt="">
            </label>
            <input type="file" @change="addFiles" multiple
                   style="display: none" accept="image/*" id="attach">
<!--            <img src="/smile.svg" alt="">-->
            <input type="text" v-model="message" style="border-radius: 0 !important" placeholder="Введите сообщение...">
            <img @click="sendMessage" src="/plane.svg" alt="">
        </div>
    </div>
</template>