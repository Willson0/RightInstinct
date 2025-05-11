import router from "@/router.js";
import config from "@/config.json";
import axios from "axios";

export function notify (text, error) {
    let notifyContainer = document.querySelector(".notification_container");
    let div = document.createElement("div");

    if (error) {
        div.innerHTML = `<div class="notification error">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    <div>
                                        ${text}
                                    </div>
                                </div>`
    } else {
        div.innerHTML = `<div class="notification success">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <div>
                                        ${text}
                                    </div>
                                </div>`
    }
    notifyContainer.appendChild(div);

    let height = div.querySelector(".notification").getBoundingClientRect().height + 10;
    div.style.visibility = "visible";
    div.style.transform = `translateY(-${height}px)`;

    requestAnimationFrame(() => {
        div.style.transition = "0.2s";
        div.style.transform = "";
        div.style.height = height + "px";
    });

    setTimeout(() => {
        div.style.opacity = '0';
        setTimeout (() => {
            div.remove();
        }, 200);
    }, 5000);
}

export function toLink (query, id = null, type = null) {
    document.body.style.overflow = "";

    if (id) router.push({ query: { s: query, id: id, type: type }});
    else router.push({ query: { s: query }});
}

export function levenshtein(a, b) {
    const matrix = [];

    for(let i = 0; i <= b.length; i++){
        matrix[i] = [i];
    }
    for(let j = 0; j <= a.length; j++){
        matrix[0][j] = j;
    }
    for(let i = 1; i <= b.length; i++){
        for(let j = 1; j <= a.length; j++){
            if(b.charAt(i-1) === a.charAt(j-1)){
                matrix[i][j] = matrix[i-1][j-1];
            } else {
                matrix[i][j] = Math.min(
                    matrix[i-1][j-1] + 1, // заменить
                    matrix[i][j-1] + 1,   // вставить
                    matrix[i-1][j] + 1    // удалить
                );
            }
        }
    }
    return matrix[b.length][a.length];
}

export function utcToLocalTime(utcString) {
    const date = new Date(utcString);

    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${hours}:${minutes}`;
}

export function showOverlay (cl) {
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
}
export function hideOverlay (cl) {
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
    }, 200);
}

export async function openList (event) {
    let select = event.target.closest(".store_input_select_container");
    document.querySelectorAll(".store_input_select_list").forEach(el => {
        if (el !== select.querySelector(".store_input_select_list"))
            el.classList.remove("active");
    })

    select.querySelector(".store_input_select_list").classList.toggle("active");
}
export async function hideList (event) {
    let el = event.target.closest(".store_input_select_container");
    el.querySelector(".store_input_select_list").classList.remove("active");
}

export function favourite (action, type, id, isLoading, user) {
    if (isLoading.status) return;

    isLoading.status = true;
    axios.post(config.backend + "favourite" + (action ? '' : '/delete'), {
        initData: window.Telegram.WebApp.initData,
        type: type,
        object_id: id,
    }).then((response) => {
        if (action) {
            notify("Успешно добавлено в избранное!");
            if (!user || !user.favourites || !Array.isArray(user.favourites[type])) user.favourites[type] = [];
            user.favourites[type].push(id);
        } else {
            notify("Успешно удалено из избранного!");
            user.favourites[type] = user.favourites[type].filter(el => el !== id);
        }
        this.$store.dispatch("updateUser", user);
    }).catch((error) => {
        if (error.response)
            notify(error.message, 1);
    }).finally(() => {
        isLoading.status = false;
    })
}

export function toLocalSimpleISO(date) {
    const pad = n => String(n).padStart(2, "0")
    return [
            date.getFullYear(),
            pad(date.getMonth() + 1),
            pad(date.getDate())
        ].join("-") + "T" +
        [
            pad(date.getHours()),
            pad(date.getMinutes()),
            pad(date.getSeconds())
        ].join(":") +
        "." +
        String(date.getMilliseconds()).padStart(3, "0")
}