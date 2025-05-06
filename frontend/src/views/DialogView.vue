<script>
export default {
    name: "DialogView",
}
</script>

<template>
    <div class="dialog">
        <div class="dialog_header">
            <div>
                <img src="/avatar_1.png" alt="">
                <div>Константин</div>
            </div>
        </div>
        <div class="dialog_main margin-all">
            <div class="dialog_main_from_user">
                <div class="dialog_main_text">Классная собака</div>
                <div class="dialog_main_footer">
                    <div class="dialog_main_footer_time">15:22</div>
                    <img src="/checked.svg" alt="">
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" viewBox="0 0 18 21" fill="none">
                    <path d="M17.7437 19.9604C12.5804 20.9705 7.33521 18.3175 5.59562 16.5532C7.14607 12.3905 -3.8542 2.87887 3.14003 2.59482C4.75731 2.52922 5.9668 -1.7634 11.9778 1.17863C12.0512 2.46355 12.2105 6.91513 12.2412 7.67289C12.6667 18.1643 18.7184 19.3171 17.7437 19.9604Z" fill="#F1EBD8"/>
                </svg>
            </div>
            <div class="dialog_main_to_user">
                <div class="dialog_main_text">Спасибо!</div>
                <div class="dialog_main_footer">
                    <div class="dialog_main_footer_time">15:35</div>
                    <img src="/checked.svg" alt="">
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" viewBox="0 0 18 21" fill="none">
                    <path d="M17.7437 19.9604C12.5804 20.9705 7.33521 18.3175 5.59562 16.5532C7.14607 12.3905 -3.8542 2.87887 3.14003 2.59482C4.75731 2.52922 5.9668 -1.7634 11.9778 1.17863C12.0512 2.46355 12.2105 6.91513 12.2412 7.67289C12.6667 18.1643 18.7184 19.3171 17.7437 19.9604Z" fill="#F1EBD8"/>
                </svg>
            </div>
        </div>
        <div class="dialog_input margin-side">
            <img src="/attach.svg" alt="">
            <img src="/smile.svg" alt="">
            <input type="text" placeholder="Введите сообщение...">
            <img src="/plane.svg" alt="">
        </div>
    </div>
</template>