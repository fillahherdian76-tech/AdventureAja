// DOM 1: Mengubah isi teks elemen header secara dinamis saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
  let heroTitle = document.getElementById("heroTitle");
  if (heroTitle) {
    heroTitle.innerHTML = "Malang Mbois Ilakes<br />Bareng AdventureAja.";
  }
});

// Fungsi proteksi menu Katalog (Belum Login / Daftar)
function checkCatalogAccess(event) {
  event.preventDefault(); // Mencegah pindah halaman
  
  // DOM 2: Mengubah style elemen kontainer secara dinamis saat akses ditolak
  let mainPage = document.getElementById("mainPage");
  mainPage.style.transition = "border 0.3s ease";
  mainPage.style.border = "2px solid #e74c3c";
  
  setTimeout(() => {
    mainPage.style.border = "none";
  }, 600);

  // DOM 3: Membuat elemen pemberitahuan tambahan secara dinamis (info box)
  let container = document.getElementById("dynamicContainer");
  if (!document.getElementById("warningAlert")) {
    let alertBox = document.createElement("div");
    alertBox.id = "warningAlert";
    alertBox.style.background = "rgba(231, 76, 60, 0.15)";
    alertBox.style.border = "1px solid #e74c3c";
    alertBox.style.padding = "12px 16px";
    alertBox.style.borderRadius = "10px";
    alertBox.style.color = "#ff8080";
    alertBox.style.fontSize = "14px";
    alertBox.innerHTML = "<b>Akses Terbatas:</b> Silakan <a href='login.html' style='color:#ffc107; text-decoration:underline;'>Masuk</a> atau <a href='register.html' style='color:#ffc107; text-decoration:underline;'>Daftar</a> terlebih dahulu untuk melihat katalog.";
    container.append(alertBox);
  }

  // Panggil Snackbar / Toast
  showToast("Akses ditolak! Harap login atau daftar terlebih dahulu.");
}

// Implementasi Toast / Snackbar murni JavaScript (Tanpa Framework)
function showToast(message) {
  let oldToast = document.getElementById("snackbarToast");
  if (oldToast) {
    oldToast.remove();
  }

  let toast = document.createElement("div");
  toast.id = "snackbarToast";
  toast.innerText = message;

  // Styling Snackbar mengambang
  toast.style.position = "fixed";
  toast.style.bottom = "30px";
  toast.style.left = "50%";
  toast.style.transform = "translateX(-50%)";
  toast.style.backgroundColor = "#222";
  toast.style.color = "#ffc107";
  toast.style.padding = "12px 24px";
  toast.style.borderRadius = "8px";
  toast.style.boxShadow = "0 6px 20px rgba(0,0,0,0.4)";
  toast.style.fontWeight = "600";
  toast.style.fontSize = "14px";
  toast.style.zIndex = "9999";
  toast.style.opacity = "0";
  toast.style.transition = "opacity 0.3s ease-in-out";

  document.body.append(toast);

  // Efek muncul
  setTimeout(() => {
    toast.style.opacity = "1";
  }, 10);

  // Hilang otomatis setelah 3,5 detik
  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => {
      toast.remove();
    }, 300);
  }, 3500);
}