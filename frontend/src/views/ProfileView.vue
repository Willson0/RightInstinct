<script>
import {levenshtein, notify, toLink} from "@/utils.js";
import axios from "axios";
import config from "@/config.json";

export default {
    name: "ProfileView",
    data () {
        return {
            notifications: false,
            isLoading: false,
            city: "",
            data: null,
        }
    },
    async mounted () {
        await axios.get(config.backend + "data").then((response) => {
            this.data = response.data;
        });
    },
    methods: {
        toLink,
        showOverlay (cl) {
            document.body.style.overflow = "hidden";

            let el = document.querySelector(`.overlay.${cl}`);
            el.style.display = "";
            el.style.transform = "translateY(100%)";

            let background = document.querySelector(`.background.${cl}`);
            background.style.display = "";
            background.style.opacity = 0;

            requestAnimationFrame(() => {
                el.style.transform = "";
                background.style.opacity = "";
            });
        },
        hideOverlay (cl) {
            let el = document.querySelector(`.overlay.${cl}`);
            el.style.transform = "translateY(100%)";

            let background = document.querySelector(`.background.${cl}`);
            background.style.opacity = 0;

            setTimeout(() => {
                el.style.transform = "";
                background.style.opacity = "";
                background.style.display = "none";

                el.style.display = "none";
                document.body.style.overflow = "";
            }, 200);
        },
        async changeNotification () {
            if (this.isLoading) return;

            this.isLoading = true;
            await axios.post(config.backend + "auth/update", {
                "initData": window.Telegram.WebApp.initData,
                "notification": !this.user.notification
            }).then((response) => {
                this.$store.dispatch("updateUser", response.data);
                notify("Уведомления успешно " + (response.data.notification ? "включены" : "отключены"));
            }).catch((error) => {
                if (error.response) {
                    return alert (`An error occurred: ${error.message}`);
                }
            }).finally(() => {
                this.isLoading = false;
            });
        },
        async changeCity () {
            if (this.isLoading) return;
            if (this.city === this.user.city?.name) return;

            let minDistance = Infinity;
            let closestCity = null;

            for(const city of this.data.cities){
                const dist = levenshtein(city.name.toLowerCase(), this.city.toLowerCase());
                if (dist < minDistance) {
                    minDistance = dist;
                    closestCity = city.id;
                }
            }

            this.city = this.data.cities.find(el => el.id === closestCity).name;

            this.isLoading = true;
            await axios.post(config.backend + "auth/update", {
                "initData": window.Telegram.WebApp.initData,
                "city_id": closestCity
            }).then((response) => {
                this.$store.dispatch("updateUser", response.data);
                notify("Город успешно изменен на " + this.city);
            }).catch((error) => {
                if (error.response) {
                    return alert (`An error occurred: ${error.message}`);
                }
            }).finally(() => {
                this.isLoading = false;
            });
        }
    },
    computed: {
        user () {
            return this.$store.state.user;
        },
        username () {
            return (window.Telegram.WebApp.initDataUnsafe.user.first_name + window.Telegram.WebApp.initDataUnsafe.user.last_name);
        },
        avatar () {
            return window.Telegram.WebApp.initDataUnsafe.user?.photo_url
        },
        subLength () {
            return this.user.subscriptions?.length;
        }
    },
}
</script>

<template>
    <div style="display:none" @click="hideOverlay('settings')" class="background settings"></div>
    <div style="display:none" class="overlay settings">
        <div @click="hideOverlay('settings')"  class="overlay_button"><div></div></div>
        <div class="profile_settings_main">
            <div class="profile_settings_main_title">Профиль</div>
            <div class="profile_settings_main_el">
                <div class="profile_settings_mail_el_title">Местоположение</div>
                <button @click="showOverlay('location')"><img src="/edit.svg" alt=""></button>
            </div>
            <div class="profile_settings_main_el">
                <div class="profile_settings_mail_el_title">Уведомления</div>
                <div class="profile_settings_mail_el_switcher" :class="user.notification ? 'active' : ''"
                    @click="changeNotification">
                    <div></div>
                </div>
            </div>
        </div>
    </div>
    <div style="display:none" @click="hideOverlay('location')" class="background location"></div>
    <div style="display:none" class="overlay location">
        <div @click="hideOverlay('location')"  class="overlay_button"><div></div></div>
        <div class="profile_settings_main profile_location">
            <div class="profile_settings_main_title">Изменить местопложение</div>
            <input @blur="changeCity" v-model="city" type="text" placeholder="Екатеринбург">
        </div>
    </div>
    <div class="profile margin-all">
        <div class="profile_header">
            <img :src="avatar" alt="">
            <div class="profile_info">
                <div class="profile_info_name">{{ username }}</div>
                <div class="profile_info_city" v-if="user.city">{{user.city.name}}</div>
                <div v-if="user.rating !== 0" class="block_post_img">
                    <div class="green-bgc">
                        <img src="/star.svg" alt="">
                        <div class="grey-light">{{user.rating}}</div>
                    </div>
                </div>
            </div>
            <button @click="showOverlay('settings');  city = user.city?.name ?? ''">
                <img src="/settings.svg" alt="">
            </button>
        </div>
        <div class="profile_nav">
            <div v-for="(link, name) in {
                'Мои объявления': 'myposts',
                'Мои услуги': 'myservices',
                'Мои мероприятия': 'myevents',
                'Мои оценки': 'myratings',
                'Избранное': 'favourite',
            }">
                <div>{{ name }}</div>
                <button @click="toLink(link)"><img src="/arrow.svg" alt=""></button>
            </div>
            <div v-if="user.subscriptions && user.subscriptions?.length !== 0">
                <div>Мои подписки</div>
                <div @click="toLink('mysubscriptions')" class="profile_subscribers">
                    <div v-if="subLength > 3" :style="`left: 0px;`"><div>{{ subLength-3 }}+</div></div>
                    <img v-if="subLength > 2" :style="`left: ${(subLength-3)*7 > 7 ? 7 : (subLength-3)*7}px;`" :src="user.subscriptions[2].user_subscription.avatar" alt="">
                    <img v-if="subLength > 1" :style="`left: ${(subLength-2)*7 > 14 ? 14 : (subLength-2)*7}px;`" :src="user.subscriptions[1].user_subscription.avatar" alt="">
                    <img :style="`left: ${(subLength-1)*7 > 21 ? 21 : (subLength-1)*7}px;`" :src="user?.subscriptions[0]?.user_subscription.avatar" alt="">
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>