<script>
import axios from "axios";
import config from "@/config.json"
import {hideOverlay, notify, showOverlay, toLink, toLocalSimpleISO} from "@/utils.js";
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    name: "StorePost",
    components: {VueDatePicker},
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
            type: 'post',
            details: "",
            dates: [],
        }
    },
    async mounted () {
        this.type = this.$route.query.id;

        window.addEventListener("click", (event) => {
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
        showOverlay,
        hideOverlay,
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
            Object.values(this.$refs).forEach(el => {
                if (el) el.style.border = ""
            });

            let rules = [];
            if (this.type === 'post')
                rules = [
                    [this.age < 1, "Возраст не может быть меньше 1 месяца!", "age"],
                    [this.age > 360, "Возраст не может быть больше 360 месяцев!", "age"],
                    [this.price < 0, "Цена не может быть меньше 0 рублей!", "price"],
                    [this.description.length < 10, "Описание не может быть меньше 10 символов!", "description"],
                ];
            else if (this.type === 'service')
                rules = [
                    [this.price < 0, "Цена не может быть меньше 0 рублей!", "price"],
                    [this.description.length < 10, "Описание не может быть меньше 10 символов!", "description"],
                ]
            else if (this.type === 'event')
                rules = [
                    [this.description.length < 10, "Описание не может быть меньше 10 символов!", "description"],
                    [this.details.length < 10, "Описание деталей не может быть меньше 10 символов!", "details"],
                ]
            let isError = false;
            for (let rule of rules) {
                if (rule[0]) {
                    notify(rule[1], 1);
                    this.$refs[rule[2]].style.border = "1px solid #DD1117";
                    isError = true;
                }
            }
            if (isError) return;

            if (this.isLoading) return;

            let fd = new FormData();
            fd.append("initData", window.Telegram.WebApp.initData);
            fd.append("title", this.title);
            fd.append("description", this.description);
            fd.append("city_id", this.city);
            if (this.type === "post") {
                fd.append("age", this.age);
                fd.append("gender", this.gender);
                fd.append("breed_id", this.breed);
                fd.append("price", this.price);
                fd.append("category_id", this.category);
                fd.append("rewards", this.rewards);
            } else if (this.type === "service") {
                fd.append("type_id", this.category);
                fd.append("price", this.price);
            } else if (this.type === 'event') {
                fd.append("type_id", this.category);
                fd.append("details", this.details);
                fd.append("start_date", toLocalSimpleISO(this.dates[0]));
                fd.append("end_date", toLocalSimpleISO(this.dates[1]));
            }

            let index = 0;
            for (let img of this.photos) {
                fd.append(`pictures[${index}]`, img.file);
                index ++;
            }

            this.isLoading = true;
            await axios.post(config.backend + this.type + "/", fd)
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
    },
    computed: {
        beautifullyDate () {
            if (this.dates[0].getYear() === this.dates[1].getYear())
                return (String(this.dates[0].getDate()).padStart(2, "0") + "."
                    + String(this.dates[0].getMonth()+1).padStart(2, "0") + " - "
                    + String(this.dates[1].getDate()).padStart(2, "0")
                        + "." + String(this.dates[1].getMonth()+1).padStart(2, "0")
                        + "." + String(this.dates[1].getFullYear()).padStart(2,"0"))
        },
        rulePost () {
            return this.photos.length !== 0 && this.price && this.rewards && this.category && this.city
                && this.breed && this.gender !== null && this.title && this.age && this.description
                && !this.isLoading;
        },
        ruleService () {
            return this.photos.length !== 0 && this.price && this.category && this.city && this.title
                    && this.description && !this.isLoading;
        },
        ruleEvent () {
            return this.photos.length !== 0 && this.category && this.city && this.title && this.details
                && this.dates.length > 0 && this.description && !this.isLoading;
        }
    }
}
</script>

<template>
    <div style="display:none" @click="hideOverlay('dateSelect')" class="background dateSelect"></div>
    <div style="display:none" class="overlay dateSelect">
        <div @click="hideOverlay('dateSelect')"  class="overlay_button"><div></div></div>
        <div class="dateSelect_main">
            <VueDatePicker v-model="dates" :range="true" :enable-time-picker="false" :inline="true"
                           :auto-apply="true" :min-date="new Date()" locale="ru" :month-picker="false"
                           :hide-input-icon="true"/>
        </div>
    </div>
    <div class="store">
        <h1 class="store_title">Добавить {{ this.type === 'post' ? 'объявление' : this.type === 'service' ? 'услугу' : 'мероприятие' }}</h1>
        <input v-model="title" class="store_input" type="text" placeholder="Название">
        <input ref="description" v-if="['event'].includes(type)" v-model="description" class="store_input" type="text" placeholder="Описание">
        <div v-if="['post'].includes(type)" style="z-index:10" class="store_input_container">
            <input ref="age" v-model="age" type="number" placeholder="Возраст (мес)">
            <div class="store_input_select_container">
                <div ref="gender" @click="openList" class="store_input_select">
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
        <div v-if="['post'].includes(type)" style="z-index:9" class="store_input_select_container">
            <div ref="breed" @click="openList" class="store_input_select">
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
            <div ref="city" @click="openList" class="store_input_select">
                <div class="store_input_select_main">
                    <div v-if="!city">{{ !['event'].includes(type) ? 'Город' : 'Место проведения' }}</div>
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
            <input v-if="['post'].includes(type)" ref="price" v-model="price" type="number" placeholder="Цена, ₽">
            <div v-if="['event'].includes(type)" ref="category" class="store_input_select_container">
                <div @click="showOverlay('dateSelect')" class="store_input_select">
                    <div class="store_input_select_main">
                        <div v-if="!dates.length">
                            <img src="/calendar.svg" alt="">
                            <div>Дата</div>
                        </div>
                        <div v-else>{{ beautifullyDate }}</div>
                    </div>
                </div>
            </div>
            <div v-if="['post'].includes(type)" ref="category" class="store_input_select_container">
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
            <div v-if="['service', 'event'].includes(type)" ref="category" class="store_input_select_container">
                <div @click="openList" class="store_input_select">
                    <div class="store_input_select_main">
                        <div v-if="!category">Вид услуги</div>
                        <div v-else>{{data.types.find(el => el.id === category).name}}</div>
                        <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                    </div>
                </div>
                <div class="store_input_select_list">
                    <div v-for="catg in data.types" @click="category = catg.id; hideList($event)">
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
        <textarea v-if="['event'].includes(type)" ref="details" placeholder="Подробности" rows="2" v-model="details" class="input"></textarea>
        <textarea v-else ref="description" placeholder="Описание" rows="2" v-model="description" class="input"></textarea>
        <input v-if="['post'].includes(type)" ref="rewards" v-model="rewards" type="text" placeholder="Титулы и награды">
        <button v-if="['post'].includes(type)"  class="store_button button"
                :class="rulePost ? 'active' : ''" @click="rulePost ? sendData() : ''">Сохранить</button>
        <button v-if="['service'].includes(type)"  class="store_button button"
                :class="ruleService ? 'active' : ''" @click="ruleService ? sendData() : ''">Сохранить</button>
        <button v-if="['event'].includes(type)"  class="store_button button"
                :class="ruleEvent ? 'active' : ''" @click="ruleEvent ? sendData() : ''">Отправить на модерацию</button>
    </div>
</template>

<style scoped>

</style>