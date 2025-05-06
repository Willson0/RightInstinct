<script>
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
                    <div v-else>{{ breed }}</div>
                    <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                </div>
            </div>
            <div class="store_input_select_list">
                <div @click="breed = 'Спаниель'; hideList($event)">
                    <div>Спаниель</div>
                </div>
                <div @click="breed = 'Немецкая овчарка'; hideList($event)">
                    <div>Немецкая овчарка</div>
                </div>
                <div @click="breed = 'Шпиц'; hideList($event)">
                    <div>Шпиц</div>
                </div>
                <div @click="breed = 'Хаски'; hideList($event)">
                    <div>Хаски</div>
                </div>
            </div>
        </div>
        <div style="z-index:8" class="store_input_select_container">
            <div @click="openList" class="store_input_select">
                <div class="store_input_select_main">
                    <div v-if="!city">Город</div>
                    <div v-else>{{ city }}</div>
                    <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                </div>
            </div>
            <div class="store_input_select_list">
                <div @click="city = 'Екатеринбург'; hideList($event)">
                    <div>Екатеринбург</div>
                </div>
                <div @click="city = 'Челябинск'; hideList($event)">
                    <div>Челябинск</div>
                </div>
                <div @click="city = 'Магнитогорск'; hideList($event)">
                    <div>Магнитогорск</div>
                </div>
                <div @click="city = 'Крым'; hideList($event)">
                    <div>Крым</div>
                </div>
            </div>
        </div>
        <div style="z-index:7" class="store_input_container">
            <input v-model="price" type="number" placeholder="Цена, ₽">
            <div ref="genderSelect" class="store_input_select_container">
                <div @click="openList" class="store_input_select">
                    <div class="store_input_select_main">
                        <div v-if="!category">Категория</div>
                        <div v-else>{{category}}</div>
                        <img class="store_input_select_triangle" src="/triangle.svg" alt="">
                    </div>
                </div>
                <div class="store_input_select_list">
                    <div @click="category = 'Вязка'; hideList($event)">
                        <div>Вязка</div>
                    </div>
                    <div @click="category = 'Продажа'; hideList($event)">
                        <div>Продажа</div>
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
            && description ? 'active' : ''
        ">Сохранить</button>
    </div>
</template>

<style scoped>

</style>