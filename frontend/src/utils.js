import router from "@/router.js";

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

export function toLink (query, id = null) {
    if (id) router.push({ query: { s: query, id: id }});
    else router.push({ query: { s: query }});
}