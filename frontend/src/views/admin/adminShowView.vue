<script>
import adminnav from "@/components/adminnav.vue";
import "@/assets/admin.css";
import axios from "axios";
import config from "@/config.json";
import {notify, removeLoading} from "@/assets/admin.js";

export default {
    name: "adminShowView.vue",
    components: { adminnav },
    data () {
        return {
            post: {},
            showModal: false,
            currentImageIdx: 0,
            isDeleted: false,
            config: config,
        }
    },
    methods: {
        openImage(idx) {
            this.currentImageIdx = idx;
            this.showModal = true;
            document.body.style.overflow = "hidden";
        },
        closeImage() {
            this.showModal = false;
            document.body.style.overflow = "";
        },
        async deletePost() {
            if (confirm('Вы уверены, что хотите удалить этот пост?')) {
                await axios.delete(config.backend + "admin/" + this.$route.params.type + "/" + this.$route.params.id).then((response) => {
                    notify("Пост успешно удален!");
                    setTimeout(() => {
                        this.$router.push("/admin");
                    }, 2000);
                }).catch((error) => {
                    if (error.response) {
                        alert (error.message);
                    }
                })
            }
        },
        formatDate(dateStr) {
            if (!dateStr) return;

            const [datePart] = dateStr.split(' ');
            const [year, month, day] = datePart.split('-');
            return `${day}.${month}.${year}`;
        },
        formatDateUTC(dateStr) {
            let date = new Date(dateStr);
            return `${date.getDate()}.${date.getMonth()+1}.${date.getFullYear()}`;
        }
    },
    async mounted () {
        axios.defaults.withCredentials = true;

        await axios.get(config.backend + "admin/" + this.$route.params.type + "/" + this.$route.params.id).then((response) => {
            this.post = response.data;
            removeLoading();
        }).catch((error) => {
            if (error.response) {
                alert (error.message);
            }
        });
    }
}
</script>

<template>
    <adminnav>
        <div id="app" class="admin_show_root">
            <div v-if="!isDeleted" class="admin_show_content">
                <div class="admin_show_fields-wrap">
                    <h1 class="admin_show_title">{{ post.title }}</h1>
                    <div class="admin_show_fields">
                        <p><b>ID:</b> {{ post.id }}</p>
                        <p><b>Пользователь:</b> {{ post.user?.fullname }} (ID {{ post.user?.id }})</p>
                        <p v-if="['post', 'service'].includes($route.params.type)"><b>Возраст:</b> {{ post.age }} мес.</p>
                        <p v-if="['post', 'service'].includes($route.params.type)"><b>Пол:</b> {{ post.gender ? 'Мужской' : 'Женский' }}</p>
                        <p v-if="['post'].includes($route.params.type)"><b>Порода:</b> {{ post.breed?.name }}</p>
                        <p><b>Город:</b> {{ post.city?.name }}</p>
                        <p v-if="['post', 'service'].includes($route.params.type)"><b>Категория:</b> {{ post.category?.name }}</p>
                        <p v-if="['post', 'service'].includes($route.params.type)"><b>Цена:</b> {{ post.price }} руб.</p>
                        <p><b>Описание:</b> {{ post.description }}</p>
                        <p v-if="['post', 'service'].includes($route.params.type)"><b>Ссылка:</b> <a :href="post.link" target="_blank">{{ post.link }}</a></p>
                        <p v-if="['post'].includes($route.params.type)"><b>Награды:</b> {{ post.rewards }}</p>
                        <p><b>Рейтинг:</b> {{ post.rating }}</p>
                        <p v-if="['event'].includes($route.params.type)"><b>Начало:</b> {{ formatDate(post.start_date) }}</p>
                        <p v-if="['event'].includes($route.params.type)"><b>Конец:</b> {{ formatDate(post.end_date) }}</p>
                        <p><b>Создано:</b> {{ formatDateUTC(post.created_at) }}</p>
                        <p><b>Обновлено:</b> {{ formatDateUTC(post.updated_at) }}</p>
                        <p v-if="['event'].includes($route.params.type)"><b>Детали:</b> {{ post.details }}</p>
                    </div>
                    <button class="admin_show_delete-btn" @click="deletePost">Удалить</button>
                </div>
                <div class="admin_show_pictures-block">
                    <h2 class="admin_show_pictures-title">Картинки</h2>
                    <div class="admin_show_pictures-list">
                        <img
                            v-for="(pic, idx) in post.pictures"
                            :key="pic"
                            :src="config.storage + pic.url"
                            class="admin_show_thumbnail"
                            @click="openImage(idx)"
                            alt="Post picture"
                        />
                    </div>
                </div>
            </div>
            <div v-else class="admin_show_deleted-message">
                <h2>Пост успешно удалён</h2>
            </div>
            <div v-if="showModal" class="admin_show_modal" @click.self="closeImage">
                <img :src="config.storage + post.pictures[currentImageIdx].url" class="admin_show_modal-img" />
                <button type="button" class="admin_show_modal-close" @click="closeImage">&times;</button>
            </div>
        </div>
    </adminnav>
</template>

<style scoped>

</style>