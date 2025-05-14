<script>
import axios from "axios";
import config from "@/config.json"
import {hideOverlay, notify, showOverlay, toLink, toLocalSimpleISO} from "@/utils.js";
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    name: "UpdateView",
    components: {VueDatePicker},
    data () {
        return {
            originalObject: null,
            object: {},
            isLoading: false,
            data: [],
            type: 'post',
            dates: [],
            photos: [],
            main_photo: null,
            allPhotos: [],
            deletedPhotos: [],
            cityString: "",
        }
    },
    async mounted () {
        this.type = this.$route.query.type;

        window.addEventListener("click", (event) => {
            if (event.target.closest(".store_input_select_container") === null)
                document.querySelectorAll(".store_input_select_list").forEach(el => {
                    el.classList.remove("active");
                })
        });

        await axios.get(config.backend + "data").then((response) => {
            this.data = response.data;
        });

        await axios.post(config.backend + `${this.type}/` + this.$route.query.id, {
            initData: window.Telegram.WebApp.initData,
        }).then((response) => {
            this.originalObject = {... response.data};
            this.object = {... response.data};

            this.allPhotos = response.data.pictures;
            for (let photo in this.allPhotos) {
                this.allPhotos[photo].url = config.storage + this.allPhotos[photo].url;
            }

            this.cityString = this.object.city.name;

            if (this.object.start_date) {
                this.dates[0] = new Date(this.object.start_date);
                this.dates[1] = new Date(this.object.end_date);
            }
        }).catch((error) => {
            if (error.response)
                alert (error.message);
        })
    },
    methods: {
        showOverlay,
        hideOverlay,
        focusToString(event, id) {
            document.getElementById(id).focus();
            this.openList(event);
        },
        async openList (event, id = null) {
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
                this.allPhotos.push({
                    file: file,
                    url: URL.createObjectURL(file),
                });
            }
        },
        async changeImage (ev) {
            let file = ev.target.files[0];

            if (file && file.type.startsWith("image/")) {
                if (this.allPhotos[0].created_at)
                    this.deletedPhotos.push(this.allPhotos[0].id);

                this.allPhotos[0] = {
                    file: file,
                    url: URL.createObjectURL(file),
                };
            }
        },
        async deleteImage (ev) {
            if (this.allPhotos[0].created_at)
                this.deletedPhotos.push(this.allPhotos[0].id);

            this.allPhotos.shift();
        },
        isValidUrl(str) {
            try {
                console.log(new URL(str));
                return true;
            } catch (_) {
                return false;
            }
        },
        async sendData () {
            Object.values(this.$refs).forEach(el => {
                if (el) el.style.border = ""
            });

            let rules = [];
            if (this.type === 'post')
                rules = [
                    [this.object.age < 1, "Возраст не может быть меньше 1 месяца!", "age"],
                    [this.object.age > 360, "Возраст не может быть больше 360 месяцев!", "age"],
                    [this.object.price < 0, "Цена не может быть меньше 0 рублей!", "price"],
                    [this.object.description.length < 10, "Описание не может быть меньше 10 символов!", "description"],
                ];
            else if (this.type === 'service')
                rules = [
                    [this.object.price < 0, "Цена не может быть меньше 0 рублей!", "price"],
                    [this.object.description.length < 10, "Описание не может быть меньше 10 символов!", "description"],
                ]
            else if (this.type === 'event')
                rules = [
                    [this.object.description.length < 10, "Описание не может быть меньше 10 символов!", "description"],
                    [this.object.details.length < 10, "Описание деталей не может быть меньше 10 символов!", "details"],
                ]
            if (this.object.link) rules.push([!this.isValidUrl(this.object.link), "Неправильная ссылка!", "link"])

            this.object.city_id = this.data.cities.find(el => el.name.toLowerCase() === this.cityString.toLowerCase())?.id;
            rules.push([!this.object.city_id, "Выберите правильный город!", "city"]);

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
            for (const key in this.object) {
                if (this.object[key] !== this.originalObject[key]) {
                    fd.append(key, this.object[key]);
                }
            }

            for (let picture of this.deletedPhotos)
                fd.append('delete_pictures[]', picture);
            for (let picture of this.allPhotos) {
                if (picture.file) {
                    fd.append('pictures[]', picture.file);
                }
            }
            if (this.allPhotos[0].file) fd.append("number_main_picture", 0);

            if (this.dates[0]) {
                fd.append("start_date", toLocalSimpleISO(this.dates[0]));
                fd.append("end_date", toLocalSimpleISO(this.dates[1]));
            }

            this.isLoading = true;
            await axios.post(config.backend + this.type + "/" + this.object.id + "/update", fd)
            .then((response) => {
                notify("Пост успешно обновлён!");
                axios.post(config.backend + "auth/profile", {
                    "initData": window.Telegram.WebApp.initData,
                }).then((response) => {
                    this.$store.dispatch("updateUser", response.data);
                    toLink('home');
                })
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
            if (this.dates.length > 0)
                if (this.dates[0].getYear() === this.dates[1].getYear())
                    return (String(this.dates[0].getDate()).padStart(2, "0") + "."
                        + String(this.dates[0].getMonth()+1).padStart(2, "0") + " - "
                        + String(this.dates[1].getDate()).padStart(2, "0")
                            + "." + String(this.dates[1].getMonth()+1).padStart(2, "0")
                            + "." + String(this.dates[1].getFullYear()).padStart(2,"0"))
        },
        rulePost () {
            return this.object.pictures.length !== 0 && this.object.price && this.object.rewards
                && this.object.title && this.object.age && this.object.description && !this.isLoading;
        },
        ruleService () {
            return this.object.pictures.length !== 0 && this.object.price && this.object.title
                    && this.object.description && !this.isLoading;
        },
        ruleEvent () {
            return this.object.pictures.length !== 0 && this.object.title && this.object.details
                && this.object.description && !this.isLoading;
        },
        isInputOpen () {
            return document.getElementById("city_string").isFocused;
        },
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
        <div @click="hideOverlay('dateSelect')" class="home_block_button green-bgc button margin-side">
            <div>
                <div class="button">Выбрать даты</div>
            </div>
        </div>
    </div>
    <div class="store" v-if="originalObject">
        <h1 class="store_title">Добавить {{ this.type === 'post' ? 'объявление' : this.type === 'service' ? 'услугу' : 'мероприятие' }}</h1>
        <input v-model="object.title" class="store_input" type="text" placeholder="Название">
        <input ref="description" v-if="['event'].includes(type)" v-model="object.description" class="store_input" type="text" placeholder="Описание">
        <div v-if="['post'].includes(type)" style="z-index:10" class="store_input_container">
            <input ref="age" v-model="object.age" type="number" placeholder="Возраст (мес)">
            <div class="store_input_select_container">
                <div ref="gender" @click="openList" class="store_input_select">
                    <div class="store_input_select_main">
                        <div v-if="object.gender === null">Пол</div>
                        <div v-else-if="object.gender === 0">
                            <img src="/female.svg" alt="">
                            <div>Сука</div>
                        </div>
                        <div v-else-if="object.gender === 1">
                            <img src="/male.svg" alt="">
                            <div>Кобель</div>
                        </div>
                        <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                    </div>
                </div>
                <div class="store_input_select_list">
                    <div @click="object.gender = 0; hideList($event)">
                        <img src="/female.svg" alt="">
                        <div>Сука</div>
                    </div>
                    <div @click="object.gender = 1; hideList($event)">
                        <img src="/male.svg" alt="">
                        <div>Кобель</div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="['post'].includes(type)" style="z-index:9" class="store_input_select_container">
            <div ref="breed" @click="openList" class="store_input_select">
                <div class="store_input_select_main">
                    <div v-if="!object.breed_id">Порода</div>
                    <div v-else>{{ data.breeds.find(el => el.id === object.breed_id).name }}</div>
                    <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                </div>
            </div>
            <div class="store_input_select_list">
                <div v-for="br in data.breeds" @click="object.breed_id = br.id; hideList($event)">
                    <div>{{ br.name }}</div>
                </div>
            </div>
        </div>
        <div style="z-index:8" class="store_input_select_container">
            <div ref="city" @click="focusToString($event, 'city_string')" class="store_input_select">
                <div class="store_input_select_main">
                    <input type="text" v-model="cityString" style="padding: 0; border: 0;" id="city_string"
                           :placeholder="!['event'].includes(type) ? 'Город' : 'Место проведения'" @blur="hideList">
                    <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                </div>
            </div>
            <div class="store_input_select_list">
                <div v-for="ct in data.cities?.filter(el => el.name.toLowerCase().includes(cityString.toLowerCase()))" @click="cityString = ct.name; hideList($event)">
                    <div>{{ ct.name }}</div>
                </div>
            </div>
        </div>
        <div style="z-index:7" class="store_input_container">
            <input v-if="['post', 'service'].includes(type)" ref="price" v-model="object.price" type="number" placeholder="Цена, ₽">
            <div v-if="['event'].includes(type)" ref="category" class="store_input_select_container">
                <div @click="showOverlay('dateSelect')" class="store_input_select">
                    <div class="store_input_select_main">
                        <div>{{ beautifullyDate }}</div>
                    </div>
                </div>
            </div>
            <div v-if="['post'].includes(type)" ref="category" class="store_input_select_container">
                <div @click="openList" class="store_input_select">
                    <div class="store_input_select_main">
                        <div>{{data.categories.find(el => el.id === object.category_id).name}}</div>
                        <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                    </div>
                </div>
                <div class="store_input_select_list">
                    <div v-for="catg in data.categories" @click="object.category_id = catg.id; hideList($event)">
                        <div>{{ catg.name }}</div>
                    </div>
                </div>
            </div>
            <div v-if="['service'].includes(type)" ref="category" class="store_input_select_container">
                <div @click="openList" class="store_input_select">
                    <div class="store_input_select_main">
                        <div>{{data.types.find(el => el.id === object.type_id).name}}</div>
                        <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                    </div>
                </div>
                <div class="store_input_select_list">
                    <div v-for="catg in data.types" @click="object.type_id = catg.id; hideList($event)">
                        <div>{{ catg.name }}</div>
                    </div>
                </div>
            </div>
            <div v-if="['event'].includes(type)" ref="category" class="store_input_select_container">
                <div @click="openList" class="store_input_select">
                    <div class="store_input_select_main">
                        <div>{{data.event_types.find(el => el.id === object.type_id).name}}</div>
                        <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                    </div>
                </div>
                <div class="store_input_select_list">
                    <div v-for="catg in data.event_types" @click="object.type_id = catg.id; hideList($event)">
                        <div>{{ catg.name }}</div>
                    </div>
                </div>
            </div>
        </div>
        <label v-if="allPhotos.length === 0" for="image" class="store_photo_container">
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
                <img :src="allPhotos[0].url" alt="">
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
            <img :src="photo.url" alt="" v-for="photo in allPhotos.slice(1)">
            <label for="image">
                <img src="/camera.svg" alt="">
                <div class="store_photo_title">Загрузить</div>
            </label>
        </div>
        <input @input="addImage" id="image" type="file" style="display:none;" accept="image/*">
        <input v-if="['post', 'service'].includes(type)" ref="link" v-model="object.link" type="text" placeholder="Ссылка на видео">
        <textarea v-if="['event'].includes(type)" ref="details" placeholder="Подробности" rows="2" v-model="object.details" class="input"></textarea>
        <textarea v-else ref="description" placeholder="Описание" rows="2" v-model="object.description" class="input"></textarea>
        <input v-if="['post'].includes(type)" ref="rewards" v-model="object.rewards" type="text" placeholder="Титулы и награды">
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