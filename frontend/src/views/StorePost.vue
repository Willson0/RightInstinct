<script>
import axios from "axios";
import config from "@/config.json"
import {notify, toLink} from "@/utils.js";

export default {
    name: "StorePost",
    data () {
        return {
            gender: null, // 0 - ж 1 - м
            title: "",
            breed: "",
            city: "",
            price: "",
            category: "",
            photos: [],
            rewards: "",
            age: "",
            description: "",
            isLoading: false,
            data: [],
        }
    },
    async mounted () {
        window.addEventListener("click", (event) => {
            console.log(event.target.closest(".store_input_select_container"));
            if (event.target.closest(".store_input_select_container") === null)
                document.querySelectorAll(".store_input_select_list").forEach(el => {
                    el.classList.remove("active");
                })
        });

        await axios.get(config.backend + "data").then((response) => {
            this.data = response.data;
        });
    },
    methods: {
        async openList (event) {
            let select = event.target.closest(".store_input_select_container");
            document.querySelectorAll(".store_input_select_list").forEach(el => {
                if (el !== select.querySelector(".store_input_select_list"))
                    el.classList.remove("active");
            })

            select.querySelector(".store_input_select_list").classList.toggle("active");
        },
        async hideList (event) {
            let el = event.target.closest(".store_input_select_container");
            el.querySelector(".store_input_select_list").classList.remove("active");
        },
        async addImage (ev) {
            let file = ev.target.files[0];

            if (file && file.type.startsWith("image/")) {
                this.photos.push({
                    file: file,
                    link: URL.createObjectURL(file),
                });
            }
        },
        async changeImage (ev) {
            let file = ev.target.files[0];

            if (file && file.type.startsWith("image/")) {
                this.photos[0] = {
                    file: file,
                    link: URL.createObjectURL(file),
                };
            }
        },
        async deleteImage (ev) {
            this.photos.shift();
        },
        async sendData () {
            if (this.isLoading) return;

            let fd = new FormData();
            fd.append("initData", window.Telegram.WebApp.initData);
            fd.append("title", this.title);
            fd.append("age", this.age);
            fd.append("gender", this.gender);
            fd.append("breed_id", this.breed);
            fd.append("city_id", this.city);
            fd.append("price", this.price);
            fd.append("category_id", this.category);
            fd.append("description", this.description);
            fd.append("rewards", this.rewards);

            let index = 0;
            for (let img of this.photos) {
                fd.append(`pictures[${index}]`, img.file);
                index ++;
            }

            this.isLoading = true;
            await axios.post(config.backend + "post/", fd)
            .then((response) => {
                notify("Пост успешно создан!");
                setTimeout(() => {
                    toLink('home');
                }, 2000);
            }).catch((error) => {
                if (error.response) {
                    return alert (`An error occurred: ${error.message}`);
                }
            }).finally(() => {
                this.isLoading = false;
            })
        }
    }
}
</script>

<template>
    <div class="store">
        <h1 class="store_title">Добавить объявление</h1>
        <input v-model="title" class="store_input" type="text" placeholder="Название">
        <div style="z-index:10" class="store_input_container">
            <input v-model="age" type="text" placeholder="Возраст">
            <div ref="genderSelect" class="store_input_select_container">
                <div @click="openList" class="store_input_select">
                    <div class="store_input_select_main">
                        <div v-if="gender === null">Пол</div>
                        <div v-else-if="gender === 0">
                            <img src="/female.svg" alt="">
                            <div>Сука</div>
                        </div>
                        <div v-else-if="gender === 1">
                            <img src="/male.svg" alt="">
                            <div>Кобель</div>
                        </div>
                        <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                    </div>
                </div>
                <div class="store_input_select_list">
                    <div @click="gender = 0; hideList($event)">
                        <img src="/female.svg" alt="">
                        <div>Сука</div>
                    </div>
                    <div @click="gender = 1; hideList($event)">
                        <img src="/male.svg" alt="">
                        <div>Кобель</div>
                    </div>
                </div>
            </div>
        </div>
        <div style="z-index:9" ref="breedSelect" class="store_input_select_container">
            <div @click="openList" class="store_input_select">
                <div class="store_input_select_main">
                    <div v-if="!breed">Порода</div>
                    <div v-else>{{ data.breeds.find(el => el.id === breed).name }}</div>
                    <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                </div>
            </div>
            <div class="store_input_select_list">
                <div v-for="br in data.breeds" @click="breed = br.id; hideList($event)">
                    <div>{{ br.name }}</div>
                </div>
            </div>
        </div>
        <div style="z-index:8" class="store_input_select_container">
            <div @click="openList" class="store_input_select">
                <div class="store_input_select_main">
                    <div v-if="!city">Город</div>
                    <div v-else>{{ data.cities.find(el => el.id === city).name }}</div>
                    <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                </div>
            </div>
            <div class="store_input_select_list">
                <div v-for="ct in data.cities" @click="city = ct.id; hideList($event)">
                    <div>{{ ct.name }}</div>
                </div>
            </div>
        </div>
        <div style="z-index:7" class="store_input_container">
            <input v-model="price" type="number" placeholder="Цена, ₽">
            <div ref="genderSelect" class="store_input_select_container">
                <div @click="openList" class="store_input_select">
                    <div class="store_input_select_main">
                        <div v-if="!category">Категория</div>
                        <div v-else>{{data.categories.find(el => el.id === category).name}}</div>
                        <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                    </div>
                </div>
                <div class="store_input_select_list">
                    <div v-for="catg in data.categories" @click="category = catg.id; hideList($event)">
                        <div>{{ catg.name }}</div>
                    </div>
                </div>
            </div>
        </div>
        <label v-if="photos.length === 0" for="image" class="store_photo_container">
            <div class="store_photo_empty">
                <div>
                    <img src="/camera.svg" alt="">
                    <div class="store_photo_title">Загрузить фото или видео</div>
                    <div class="sign">Минимум 1 фото</div>
                </div>
            </div>
        </label>
        <div v-else class="store_photos">
            <div>
                <img :src="photos[0].link" alt="">
                <div class="store_photo_first_number">Главное</div>
                <div class="store_photo_first_buttons">
                    <label for="firstImage">
                        <img src="/edit.svg" alt="">
                    </label>
                    <input id="firstImage" type="file" @input="changeImage" accept="image/*" style="display:none">
                    <button @click="deleteImage">
                        <img src="/trash.svg" alt="">
                    </button>
                </div>
            </div>
            <img :src="photo.link" alt="" v-for="photo in photos.slice(1)">
            <label for="image">
                <img src="/camera.svg" alt="">
                <div class="store_photo_title">Загрузить</div>
            </label>
        </div>
        <input @input="addImage" id="image" type="file" style="display:none;" accept="image/*">
        <textarea placeholder="Описание" rows="2" v-model="description" class="input"></textarea>
        <input v-model="rewards" type="text" placeholder="Титулы и награды">
        <button class="store_button button" :class="
            photos.length !== 0 && price && rewards && category && city && breed && gender !== null && title && age
            && description && !isLoading ? 'active' : ''
        " @click="sendData">Сохранить</button>
    </div>
</template>

<style scoped>

</style>