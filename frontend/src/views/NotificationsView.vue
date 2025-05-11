<script>
import PostBlock from "@/components/PostBlock.vue";
import UserBlock from "@/components/UserBlock.vue";
import EventBlock from "@/components/EventBlock.vue";
import axios from "axios";
import config from "@/config.json";
import {toLink} from "@/utils.js";

export default {
    name: "NotificationsView",
    methods: {
        toLink,
        async fetchData () {
            if (this.$route.query.id) {
                await axios.post(config.backend + "notification/" + this.$route.query.id, {
                    initData: window.Telegram.WebApp.initData,
                }).then((response) => {
                    this.notification = response.data;
                }).catch((error) => {
                    if (error.response)
                        alert (error.message);
                });
            }
        }
    },
    components: {EventBlock, UserBlock, PostBlock},
    data () {
        return {
            notification: null,
        }
    },
    async mounted () {
        this.fetchData();
    },
    computed: {
        user() {
            return this.$store.state.user;
        },
    },
    watch: {
        $route () {
            this.fetchData();
        }
    }
}
</script>

<template>
    <div class="notifications margin-side">
        <h1 class="notifications_title">Уведомления</h1>
        <div v-if="user?.notifications?.length === 0 && !$route.query.id" class="notifications_empty">
            <div class="notifications_empty_title">У вас нет уведомлений</div>
            <div class="notifications_empty_img">
                <img src="/dog.png" alt="">
            </div>
        </div>
        <div v-else-if="!$route.query.id" class="notifications_main">
            <div @click="toLink('notifications', notification.id)" v-for="notification in user.notifications">
                <h3 class="notifications_el_title">{{ notification.title }}</h3>
                <div class="notifications_el_text sign">{{ notification.description }}</div>
                <div class="notifications_el_alert" v-if="notification.readed === 0"></div>
            </div>
        </div>
        <div v-else-if="notification" class="notifications_show">
            <h3 class="notifications_show_title">{{ notification.title }}</h3>
            <div class="notifications_show_description sign">{{ notification.description }}</div>
            <div class="notifications_show_object">
                <post-block v-if="['post', 'service'].includes(notification.type)"
                            :object="notification.object" :type="notification.type" :my="true" />
                <user-block style="width:100%" v-if="['user'].includes(notification.type)" :user="notification.object" />
                <event-block v-if="['event'].includes(notification.type)" :event="notification.object" />
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>