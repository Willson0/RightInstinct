<script>
import {toLink} from "@/utils.js";

export default {
    name: "NavComponent",
    methods: {toLink},
    computed: {
        user() {
            return this.$store.state.user;
        },
        hasNewMessage () {
            if (this.user) {
                for (let dialog in this.user.chat) {
                    if (this.user.chat[dialog].unreaded) return true;
                    console.log(this.user.chat[dialog]);
                }
            }
            return false;
        },
        hasNewNotification () {
            if (this.user) {
                for (let notify in this.user.notifications) {
                    if (this.user.notifications[notify].unreaded) return true;
                }
            }
            return false;
        }
    }
}
</script>

<template>
    <div class="nav">
        <div class="slot">
            <slot></slot>
        </div>
        <div class="footer">
            <div>
                <div @click="toLink('home')" :class="$route.query.s === 'home' ? 'active' : ''">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.5 10V22H19.5V10" stroke="#222B1B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2.5 12L5 9.5L12 3L19 9.5L21.5 12" stroke="#222B1B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div @click="toLink('profile')" :class="$route.query.s === 'profile' ? 'active' : ''">
                    <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.3333 10C14.2663 10 15.8333 8.433 15.8333 6.5C15.8333 4.56701 14.2663 3 12.3333 3C10.4003 3 8.83325 4.56701 8.83325 6.5C8.83325 8.433 10.4003 10 12.3333 10Z" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.33325 20.4V21H21.3333V20.4C21.3333 18.1598 21.3333 17.0397 20.8973 16.184C20.5138 15.4314 19.9019 14.8195 19.1492 14.436C18.2936 14 17.1735 14 14.9333 14H9.73325C7.49305 14 6.37295 14 5.5173 14.436C4.76464 14.8195 4.15272 15.4314 3.76923 16.184C3.33325 17.0397 3.33325 18.1598 3.33325 20.4Z" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div @click="toLink('chat')" :class="$route.query.s === 'chat' ? 'active' : ''">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 12C22 17.5229 17.5229 22 12 22C9.01327 22 2 22 2 22C2 22 2 14.5361 2 12C2 6.47715 6.47717 2 12 2C17.5229 2 22 6.47715 22 12Z" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 9H16" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 13H16" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 17H12" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div v-if="hasNewMessage" class="warning"></div>
                </div>
                <div @click="toLink('notifications')" :class="$route.query.s === 'notifications' ? 'active' : ''">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.3333 8.2C17.3333 6.82087 16.7714 5.49823 15.7712 4.52304C14.771 3.54786 13.4145 3 12 3C10.5855 3 9.22896 3.54786 8.22876 4.52304C7.22857 5.49823 6.66667 6.82087 6.66667 8.2C6.66667 14.2667 4 17 4 17H20C20 17 17.3333 14.2667 17.3333 8.2Z" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 20C13.7968 20.3042 13.505 20.5566 13.154 20.7321C12.803 20.9076 12.4051 21 12 21C11.5949 21 11.197 20.9076 10.846 20.7321C10.495 20.5566 10.2032 20.3042 10 20" stroke="#F1EBD8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div v-if="hasNewNotification" class="warning"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>