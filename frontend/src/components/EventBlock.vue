<script>
import config from "@/config.json"
import {favourite, notify, toLink} from "@/utils.js";
import axios from "axios";
export default {
    name: "EventBlock",
    data () {
        return {
            config: config,
            overlay: false,
            isLoading: {status: false},
            overlayImage: null,
        }
    },
    props: {
        event: {
            type: Object,
            required: true
        },
        my: {
            type: Boolean,
            default: 0,
        }
    },
    computed: {
        beautifullyDate () {
            let startDate = new Date(this.event.start_date);
            let endDate = new Date(this.event.end_date);

            const months = ["января","февраля","марта","апреля","мая","июня","июля","августа",
                "сентября","октября","ноября","декабря"]

            if (startDate.getYear() === endDate.getYear())
                return (String(startDate.getDate()).padStart(2, "0") + " "
                    + months[startDate.getMonth()] + " - "
                    + String(endDate.getDate()).padStart(2, "0")
                    + " " + months[startDate.getMonth()]
                    + " " + String(endDate.getFullYear()).padStart(2,"0")) + " г."
        },
        user () {
            return this.$store.state.user;
        },
    },
    methods: {
        favourite,
        toLink,
        showOverlay (cl) {
            this.overlay = true;
            requestAnimationFrame(() => {
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
            })
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
                this.overlay = false;
            }, 200);
        },
        async deleteEvent (id) {
            const name = this.user.my.events.find(el => el.id === id).title;
            if (confirm(`Вы уверены, что хотите удалить мероприятие \"${name}\"?`)) {
                await axios.post(config.backend + "event/" + id + "/delete", {
                    initData: window.Telegram.WebApp.initData,
                }).then((response) => {
                    notify("Мероприятие успешно удалено!");
                    this.user.my.events = this.user.my.events.filter(el => el.id !== id);

                    this.hideOverlay("postOverlay");
                }).catch((error) => {
                    if (error.response) {
                        return alert (`An error occurred: ${error.message}`);
                    }
                });
            }
        },
        openFullScreen(ev) {
            this.overlayImage = ev.target.src;
            document.body.style.overflow = 'hidden';
        },
        closeFullScreen() {
            this.overlayImage = null
            document.body.style.overflow = '';
        }
    }
}
</script>

<template>
    <div v-if="overlayImage" class="image-overlay" @click="closeFullScreen">
        <img :src="overlayImage" alt="" />
    </div>
    <div v-if="overlay" style="display:none" @click="hideOverlay('postOverlay')" class="background postOverlay"></div>
    <div v-if="overlay" style="display:none" class="overlay postOverlay">
        <div @click="hideOverlay('postOverlay')" class="overlay_button"><div></div></div>
        <div class="postOverlay_main">
            <div class="postOverlay_main_photos" v-if="event.pictures?.length !== 0">
                <div>
                    <img @click="openFullScreen" :src="config.storage + event.pictures[0]?.url" alt="">
                    <div class="green-bgc">
                        <img src="/star.svg" alt="">
                        <div class="grey-light">{{ event.rating }}</div>
                    </div>
                </div>
                <img @click="openFullScreen" v-for="img in event.pictures.slice(1)" :src="config.storage + img.url" alt="">
            </div>
            <div class="postOverlay_mainContainer">
                <h4>{{ event.title }}</h4>
                <div class="input">{{ event.description }}</div>
                <div class="postOverlay_main_info">
                    <h4 class="postOverlay_main_info_category">
                        {{ event.category.name }}
                    </h4>
                    <div class="postOverlay_main_info_location">
                        <img src="/location.svg" alt="">
                        <div class="input">{{ event.city.name }}</div>
                    </div>
                </div>
            </div>
            <div class="postOverlay_main_description">
                {{ event.details }}
            </div>
            <div class="postOverlay_main_buttons">
                <div class="button postOverlay_main_buttons_calendar">
                    <div>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.08325 7.91675H17.9166V16.6667C17.9166 17.127 17.5435 17.5001 17.0833 17.5001H2.91659C2.45635 17.5001 2.08325 17.127 2.08325 16.6667V7.91675Z" stroke="#222B1B" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="M2.08325 3.75008C2.08325 3.28984 2.45635 2.91675 2.91659 2.91675H17.0833C17.5435 2.91675 17.9166 3.28984 17.9166 3.75008V7.91675H2.08325V3.75008Z" stroke="#222B1B" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="M6.66675 1.66675V5.00008" stroke="#222B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13.3333 1.66675V5.00008" stroke="#222B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.6667 14.1667H14.1667" stroke="#222B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.83325 14.1667H8.33325" stroke="#222B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.6667 10.8333H14.1667" stroke="#222B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.83325 10.8333H8.33325" stroke="#222B1B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="sign">{{ beautifullyDate }}</div>
                    </div>
                </div>
                <button v-if="!my" @click.stop="favourite(!user?.favourites[type]?.includes(event.id), 'event', event.id, isLoading, user)">
                    <img v-if="user?.favourites['event']?.includes(event.id)"
                         src="/like_active.svg" style="width:24px; height: 24px;" alt="">
                    <img v-else src="/like.svg" alt="">
                </button>
                <button v-if="!my"><img style="width: 24px; height: 24px;" src="/share.svg" alt=""></button>

                <button v-if="my"><img style="width: 24px; height: 24px;" src="/share.svg" alt=""></button>
                <button v-if="my" @click="toLink('update', event.id, 'event')">
                    <img style="width: 24px; height: 24px;" src="/edit.svg" alt="">
                </button>
                <button v-if="my" @click="deleteEvent(event.id)"><img style="width: 24px; height: 24px;" src="/trash.svg" alt=""></button>
            </div>
            <a :href="config.complain" class="postOverlay_main_complain">Пожаловаться</a>
        </div>
    </div>
    <div @click="showOverlay('postOverlay')" class="event">
        <img v-if="event.pictures[0]" :src="config.storage + event?.pictures[0].url" alt="">
        <div class="event_info">
            <h4>{{ event.title }}</h4>
            <div class="event_description sign">{{ event.description }}</div>
        </div>
        <div class="event_location">
            <img src="/location.svg" alt="">
            <div class="sign">{{ event.city.name }}</div>
        </div>
        <div class="event_calendar">
            <img src="/calendar.svg" alt="">
            <div class="sign">{{ beautifullyDate }}</div>
        </div>
    </div>
</template>

<style scoped>

</style>