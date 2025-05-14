<script>
import adminnav from "@/components/adminnav.vue";
import "@/assets/admin.css";
import axios from "axios";
import config from "@/config.json";
import {notify, removeLoading} from "@/assets/admin.js";

export default {
    name: "adminComplainsView",
    components: {
        adminnav,
    },
    data () {
        return {
            complaints: [],
        }
    },
    computed: {
        filteredComplaints() {
            if (this.complaints)
                return this.complaints?.filter(c => c.user_id && c.object && c.object.link);
        }
    },
    methods: {
        markRead(complaint) {
            if (!confirm('Пометить эту жалобу как прочитанную?')) return;
            axios.delete(config.backend + "complain/" + complaint.id).then((response) => {
                notify("Жалоба успешно проверена!");
                this.complaints = this.complaints.filter(el => el.id !== complaint.id);
            });
        },
        formatDate(iso) {
            const d = new Date(iso);
            return d.toLocaleString('ru-RU', { year: 'numeric', month: 'short', day: '2-digit', hour:'2-digit', minute:'2-digit' });
        }
    },
    async mounted () {
        axios.defaults.withCredentials = true;

        await axios.get(config.backend + "complain").then((response) => {
            this.complaints = response.data;
            removeLoading();
        }).catch ((error) => {
            if (error.response)
                alert (error.message);
        })
    }
}
</script>

<template>
    <adminnav>
        <div id="app" class="admin_complains_container">
            <div class="admin_complains_title">Жалобы на посты</div>
            <div
                v-if="filteredComplaints.length === 0"
                class="admin_complains_empty"
            >
                Жалоб нет
            </div>
            <div
                v-for="complaint in filteredComplaints"
                :key="complaint.id"
                class="admin_complains_card"
            >
                <div class="admin_complains_row">
                    <span class="admin_complains_label">Отправитель:</span>
                    <span class="admin_complains_value">ID пользователя — {{ complaint.user_id }} ({{complaint.user?.fullname}})</span>
                </div>
                <div class="admin_complains_row">
                    <span class="admin_complains_label">Жалоба:</span>
                    <span class="admin_complains_reason">{{ complaint.reason }}</span>
                </div>
                <div class="admin_complains_row">
                    <span class="admin_complains_label">Пост:</span>
                    <a
                        class="admin_complains_postlink"
                        :href="'/admin/' + complaint.type + '/' + complaint.object.id"
                        target="_blank"
                    >
                        {{ complaint.object.title || 'Ссылка на пост' }}
                    </a>
                </div>
                <div class="admin_complains_row admin_complains_actions">
                    <button
                        class="admin_complains_markread"
                        @click="markRead(complaint)"
                    >
                        Отметить, как прочитанное
                    </button>
                </div>
                <div class="admin_complains_created">
                    Получена: {{ formatDate(complaint.created_at) }}
                </div>
            </div>
        </div>
    </adminnav>
</template>

<style scoped>

</style>