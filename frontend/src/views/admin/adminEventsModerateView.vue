<script>
import adminnav from "@/components/adminnav.vue";
import {notify, removeLoading} from "@/assets/admin.js";
import axios from "axios";
import config from "@/config.json"
export default {
    name: "adminEventsModerateView",
    data () {
        return {
            events: [],
            admin_moderate_fullImg: null,
            config: config,
        }
    },
    methods: {
        openImage(img) {
            this.admin_moderate_fullImg = img;
        },
        closeImage() {
            this.admin_moderate_fullImg = null;
        },
        formatDate(dateStr) {
            const [datePart] = dateStr.split(' ');
            const [year, month, day] = datePart.split('-');
            return `${day}.${month}.${year}`;
        },
        async accept (id) {
            axios.get(config.backend + "admin/events/moderate/" + id + "/accept").then((response) => {
                notify("Мероприятие успешно одобрено!");
                this.events = this.events.filter(el => el.id !== id);
            });
        },
        async destroy (id) {
            axios.get(config.backend + "admin/events/moderate/" + id + "/delete").then((response) => {
                notify("Мероприятие успешно удалено!");
                this.events = this.events.filter(el => el.id !== id);
            });
        },
    },
    components: {
        adminnav,
    },
    async mounted () {
        removeLoading();
        axios.defaults.withCredentials = true;

        await axios.get(config.backend + "admin/events/moderate").then((response) => {
            this.events = response.data;
        }).catch((error) => {
            if (error.response)
                alert (error.message);
        });
    },
    computed: {
    }
}
</script>

<template>
    <adminnav>
        <div style="color:white" v-if="events.length === 0">Нет новых заявок на модерацию...</div>
        <div class="admin_moderate">
            <div v-for="record in events" :key="record.id" class="admin_moderate_card">
                <div class="admin_moderate_header">
                    <strong class="admin_moderate_title">{{ record.title }}</strong>
                    <span class="admin_moderate_id">#{{ record.id }}</span>
                </div>
                <div class="admin_moderate_desc">{{ record.description }}</div>
                <div class="admin_moderate_meta">
                    <span>Город: {{ record.city.name }}</span>
                    <span>Категория: {{ record.category.name }}</span>
<!--                    <span v-if="record.moderated" class="admin_moderate_status admin_moderate_moderated">модерировано</span>-->
<!--                    <span v-else class="admin_moderate_status admin_moderate_nomoderate">на модерации</span>-->
                </div>
                <div class="admin_moderate_desc">
                    Детали: {{record.details}}
                </div>
                <div class="admin_moderate_dates">
                    <span>С: {{ formatDate(record.start_date) }}</span>
                    <span>По: {{ formatDate(record.end_date) }}</span>
                </div>
                <div class="admin_moderate_imgs">
                    <img v-for="(img, idx) in record.pictures"
                         :key="idx"
                         :src="config.storage + img.url"
                         class="admin_moderate_img"
                         @click="openImage(img.url)"
                         alt="preview">
                </div>
                <div class="admin_moderate_buttons">
                    <button style="background-color:#DD1117" @click="destroy(record.id)">Удалить</button>
                    <button style="background-color:#787D46" @click="accept(record.id)">Одобрить</button>
                </div>
            </div>
            <div v-if="admin_moderate_fullImg" class="admin_moderate_fullscreen" @click="closeImage">
                <img :src="config.storage + admin_moderate_fullImg" class="admin_moderate_full_img">
            </div>
        </div>
    </adminnav>
</template>

<style scoped>

</style>