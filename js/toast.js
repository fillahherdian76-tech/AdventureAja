
function tampilkanToast(pesan, tipe = "success") {
    const toast = document.getElementById("toast");
    const toastMessage = document.getElementById("toastMessage");

    if (!toast || !toastMessage) return;

    const ikon = toast.querySelector("i");

    toastMessage.textContent = pesan;

    if (tipe === "error") {
        ikon.className = "bx bx-error-circle";
        ikon.style.color = "#ff5e5e";
        toast.style.borderColor = "rgba(255, 94, 94, 0.5)";
    } else {
        ikon.className = "bx bx-check-circle";
        ikon.style.color = "#5be1a1";
        toast.style.borderColor = "rgba(91, 225, 161, 0.4)";
    }

    toast.classList.add("show");

    clearTimeout(window.toastTimer);

    window.toastTimer = setTimeout(() => {
        toast.classList.remove("show");
    }, 3000);
}
