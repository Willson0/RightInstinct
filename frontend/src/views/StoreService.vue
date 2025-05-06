<script>
export default {
    name: "StoreService",
    data () {
        return {
            gender: null, // 0 - ж 1 - м
            breed: "",
            city: "",
            price: "",
            category: "",
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
            document.querySelectorAll(".store_input_select_list").forEach(el => {
                el.classList.remove("active");
            })

            let el = event.target.closest(".store_input_select_container");
            el.querySelector(".store_input_select_list").classList.toggle("active");
        },
        async hideList (event) {
            let el = event.target.closest(".store_input_select_container");
            el.querySelector(".store_input_select_list").classList.remove("active");
        }
    }
}
</script>

<template>
    <div class="store">
        <h1 class="store_title">Добавить услугу</h1>
        <input class="store_input" type="text" placeholder="Название">
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
                        <div v-if="!category">Вид услуги</div>
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
        <label for="image" class="store_photo_container">
            <div class="store_photo_empty">
                <div>
                    <img src="/camera.svg" alt="">
                    <div class="store_photo_title">Загрузить фото или видео</div>
                    <div class="sign">Минимум 1 фото</div>
                </div>
            </div>
        </label>
        <input id="image" type="file" style="display:none;" accept="image/*">
        <textarea placeholder="Описание" rows="2" class="input"></textarea>
        <button class="store_button button">Сохранить</button>
    </div>
</template>

<style scoped>

</style>