<script>
export default {
    name: "PostBlock",
    props: {
        title: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            required: true,
        },
        city: {
            type: String,
            required: true,
        },
        price: {
            type: Number,
            required: true,
        },
        rating: {
            type: Number,
            required: true,
        }
    },
    computed: {
        beautifullyPrice () {
            return this.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }
    }
}
</script>

<template>
    <div class="block_post">
        <div class="block_post_img">
            <img src="https://images.unsplash.com/photo-1518717758536-85ae29035b6d" alt="">
            <div class="green-bgc">
                <img src="/star.svg" alt="">
                <div class="grey-light">{{ rating }}</div>
            </div>
        </div>
        <div class="block_post_info">
            <div class="sign">{{ title }}</div>
            <div class="grey sign">{{ type }}</div>
        </div>
        <div class="block_post_location">
            <img src="/location.svg" alt="">
            <div class="sign">{{ city }}</div>
        </div>
        <div class="block_post_footer">
            <div class="block_post_price h3">
                <div>
                    {{ beautifullyPrice }} ₽
                </div>
            </div>
            <div class="button">
                <img src="/like.svg" alt="">
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>