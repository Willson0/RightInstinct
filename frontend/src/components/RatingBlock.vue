<script>
export default {
    name: "RatingBlock",
    data () {
        return {
            overlay: false,
        }
    },
    methods: {
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
        async rate (rating) {

        },
        nothing () {},
    },
    props: {
        rating: {
            type: Number,
            default: 0,
        },
        type: {
            type: String,
            default: "post"
        },
        id: {
            type: Number,
            required: true,
        },
    }
}
</script>

<template>
    <div>
        <div style="display:none" @click.stop="hideOverlay('ratingOverlay')" class="background ratingOverlay"></div>
        <div @click.stop="nothing" style="display:none" class="overlay ratingOverlay">
            <div @click.stop="hideOverlay('ratingOverlay')" class="overlay_button"><div></div></div>
            <div @click.stop="nothing" class="ratingOverlay_main">
                <div class="ratingOverlay_main_title">Оценить</div>
                <div class="ratingOverlay_main_stars">
                    <img @click.stop="rate(star)" v-for="star in 5" src="/star.svg" alt="">
                </div>
            </div>
        </div>
    </div>
    <div @click.stop="showOverlay('ratingOverlay')" class="green-bgc">
        <img src="/star.svg" alt="">
        <div class="grey-light">{{ rating }}</div>
    </div>
</template>

<style scoped>

</style>