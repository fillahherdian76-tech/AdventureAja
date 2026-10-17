
/* =========================================
   DOM 1: MENGUBAH TEKS JUDUL LANDING PAGE
   ========================================= */
document.addEventListener("DOMContentLoaded", function () {
    const heroTitle = document.getElementById("heroTitle");

    if (heroTitle) {
        heroTitle.innerHTML =
            "Malang Mbois Ilakes<br>Bareng AdventureAja.";
    }

    /* =========================================
       DOM 2: EVENT KLIK TOMBOL DAFTAR
       Menampilkan respons saat tombol diklik
       ========================================= */
    const tombolDaftar = document.querySelector(
        'a[href="register.html"]'
    );

    if (tombolDaftar) {
        tombolDaftar.addEventListener("click", function () {
            sessionStorage.setItem(
                "pesanToast",
                "Yuk, daftar dan mulai petualanganmu!"
            );
        });
    }

    /* =========================================
       DOM 3: MENAMBAHKAN ELEMEN HTML DINAMIS
       ========================================= */
    const dynamicContainer =
        document.getElementById("dynamicContainer");

    if (dynamicContainer) {
        const info = document.createElement("p");

        info.textContent =
            "Siap menjelajahi alam? Temukan perlengkapan camping favoritmu bersama AdventureAja!";

        info.className = "hero-info";
        info.style.color = "#ffffff";
        info.style.fontSize = "14px";
        info.style.marginTop = "15px";

        dynamicContainer.appendChild(info);
    }
});


/* =========================================
   FUNGSI AKSES KATALOG
   ========================================= */
function checkCatalogAccess(event) {
    event.preventDefault();

    // Periksa status login jika sistem autentikasi sudah tersedia.
    const sudahLogin = localStorage.getItem("isLoggedIn") === "true";

    if (sudahLogin) {
        window.location.href = "katalog/katalog.html";
    } else {
        window.location.href = "login.html";
    }
}


/* =========================================
   TOGGLE SIDEBAR DASHBOARD
   Aman jika elemen sidebar tidak ada di halaman
   ========================================= */
const sidebar = document.querySelector(".sidebar");
const toggleBtn = document.querySelector("#sidebarToggle");

if (sidebar && toggleBtn) {
    toggleBtn.addEventListener("click", function () {
        sidebar.classList.toggle("close");

        if (sidebar.classList.contains("close")) {
            toggleBtn.classList.replace(
                "bx-menu",
                "bx-menu-alt-right"
            );
        } else {
            toggleBtn.classList.replace(
                "bx-menu-alt-right",
                "bx-menu"
            );
        }
    });
}
