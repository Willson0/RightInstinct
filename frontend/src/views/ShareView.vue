<script>
import axios from "axios";
import config from "@/config";
import PostBlock from "@/components/PostBlock.vue";
import EventBlock from "@/components/EventBlock.vue";
export default {
    name: "ShareView",
    components: {EventBlock, PostBlock},
    data () {
        return {
            type: "",
            id: "",
            data: null,
        }
    },
    async mounted () {
        this.type = this.$route.query.type;
        this.id = this.$route.query.id;

        await axios.post(config.backend + this.type + "/" + this.id, {
            initData: window.Telegram.WebApp.initData,
        }).then((result) => {
           this.data = result.data;
        }).catch((error) => {
            if (error.response) {
                alert(error.response.data);
            }
        });
    }
}
</script>

<template>
    <div class="share" v-if="data">
        <PostBlock :object="data" :type="type" v-if="['post', 'service'].includes(type)"/>
        <EventBlock :event="data" v-else-if="['event'].includes(type)" />
    </div>
</template>

<style scoped>

</style>