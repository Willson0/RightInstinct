<script>
import {toLink} from "@/utils.js";

export default {
    name: "ProfileView",
    data () {
        return {
            notifications: false,
        }
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
                <div class="profile_settings_mail_el_switcher" :class="notifications ? 'active' : ''"
                    @click="notifications = !notifications">
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
            <input type="text" placeholder="Екатеринбург">
        </div>
    </div>
    <div class="profile margin-all">
        <div class="profile_header">
            <img src="/avatar.jpg" alt="">
            <div class="profile_info">
                <div class="profile_info_name">Василий</div>
                <div class="profile_info_city">Челябинск</div>
                <div class="block_post_img">
                    <div class="green-bgc">
                        <img src="/star.svg" alt="">
                        <div class="grey-light">4,7</div>
                    </div>
                </div>
            </div>
            <button @click="showOverlay('settings')">
                <img src="/like.svg" alt="">
            </button>
        </div>
        <div class="profile_nav">
            <div v-for="(link, name) in {
                'Мои объявления': 'myposts',
                'Мои услуги': 'myservices',
                'Мои мероприятия': 'myevents',
                'Мои оценки': 'myratings',
                'Избранное': 'myfavourites',
            }">
                <div>{{ name }}</div>
                <button @click="toLink(link)"><img src="/arrow.svg" alt=""></button>
            </div>
            <div>
                <div>Мои подписки</div>
                <div @click="toLink('mysubscriptions')" class="profile_subscribers">
                    <div style="left: 0px;"><div>12+</div></div>
                    <img style="left: 7px;" src="/avatar_3.png" alt="">
                    <img style="left: 14px;" src="/avatar_2.png" alt="">
                    <img style="left: 21px;" src="/avatar_1.png" alt="">
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>