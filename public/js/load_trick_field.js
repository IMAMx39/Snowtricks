function loadImg(img, idInput, index) {
    let buttonAdd = document.querySelector('#add-picture');
    let input = document.getElementById(idInput);
    input.addEventListener("change", () => {
        createFrame('#trick_form_images_', index, "img", "rounded img-");
        createButtonSupp('#trick_form_images_', index, "Supprimer l'image", "#add-picture");
        let image = document.querySelector(img);
        image.src = URL.createObjectURL(input.files[0]);
        buttonAdd.disabled = false;
    });
}

function loadIframe(iframe, idInput) {
    let buttonAdd = document.querySelector('#add-video');
    let frame = document.querySelector(iframe);
    let input = document.getElementById(idInput);
    input.addEventListener("change", () => {
        frame.src = input.value.replace('watch?v=', 'embed/');
        buttonAdd.disabled = false;
    });
}

function createFrame(selector, index, tagName, className) {
    let div = document.querySelector(selector + index)
    let frame = document.createElement(tagName);
    frame.src = "";
    frame.className = className + index;
    div.append(frame);
}

function createButtonSupp(selector, index, text, buttonSelector) {

    let div = document.querySelector(selector + index)
    let buttonSupp = document.createElement("button");
    buttonSupp.type = "button";
    buttonSupp.className = "btn btn-danger d-flex my-2 delete-" + index;
    buttonSupp.textContent = text;
    div.append(buttonSupp);
    buttonSupp.addEventListener("click", function () {
        let buttonAdd = document.querySelector(buttonSelector);
        buttonAdd.disabled = false;
        this.previousElementSibling.parentElement.parentElement.remove();
    });
}

function addFieldPicture() {

    let divPicture = document.querySelector('#pictures');
    let prototype = divPicture.dataset.prototype;
    let index = divPicture.querySelectorAll("div").length;
    prototype = prototype.replace(/__name__/g, index);
    divPicture.insertAdjacentHTML("beforeend", prototype);

    loadImg("img.img-" + index + "", "trick_form_images_" + index + "_file", index);
}

function addFieldVideo() {

    let divVideo = document.querySelector('#videos');
    let prototype = divVideo.dataset.prototype;
    let index = divVideo.querySelectorAll("div").length;

    prototype = prototype.replace(/__name__/g, index);
    divVideo.insertAdjacentHTML("beforeend", prototype);
    createFrame('#trick_form_videos_', index, "iframe", "rounded iframe-");
    createButtonSupp('#trick_form_videos_', index, "Supp video", "#add-video");
    loadIframe("iframe.iframe-" + index + "", "trick_form_videos_" + index + "_filename")

}



document.addEventListener('DOMContentLoaded', function () {

    let buttonAddPicture = document.querySelector('#add-picture');
    let buttonAddVideo = document.querySelector('#add-video');

    buttonAddPicture.addEventListener("click", function () {
        buttonAddPicture.disabled = true;
        addFieldPicture();
    });

    buttonAddVideo.addEventListener("click", function () {
        buttonAddVideo.disabled = true;
        addFieldVideo();
    });
});
