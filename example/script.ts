let message: String;
message = "Button clicked";

window.addEventListener('DOMContentLoaded', _ => {
    let button: HTMLButtonElement = document.querySelector('button.alert');
    if (!button) return;

    button.addEventListener('click', _ => alert(`Message: ${message}`));
});


