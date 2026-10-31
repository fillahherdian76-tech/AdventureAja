/* ========================================
   ADVENTUREAJA - JAVASCRIPT USER
   Sidebar + 3 DOM + Toast/Snackbar
   ======================================== */

document.addEventListener("DOMContentLoaded", function () {
  /* ========================================
     BAGIAN 1: SIDEBAR DESKTOP DAN MOBILE
     ======================================== */

  const userSidebar = document.getElementById("userSidebar");
  const userSidebarToggle = document.getElementById("userSidebarToggle");
  const userMobileToggle = document.getElementById("userMobileToggle");
  const userSidebarOverlay = document.getElementById("userSidebarOverlay");

  function closeMobileSidebar() {
    document.body.classList.remove("user-sidebar-mobile-open");
  }

  // Toggle sidebar desktop
  if (userSidebar && userSidebarToggle) {
    userSidebarToggle.addEventListener("click", function () {
        document.body.classList.toggle("user-sidebar-collapsed");
    

      const isCollapsed = document.body.classList.contains(
        "user-sidebar-collapsed"
      );

      userSidebarToggle.setAttribute(
        "aria-label",
        isCollapsed ? "Buka sidebar" : "Ciutkan sidebar"
      );
    });
  }

  // Buka sidebar mobile
  if (userMobileToggle) {
    userMobileToggle.addEventListener("click", function () {
      document.body.classList.add("user-sidebar-mobile-open");
    });
  }

  // Tutup sidebar mobile melalui overlay
  if (userSidebarOverlay) {
    userSidebarOverlay.addEventListener("click", closeMobileSidebar);
  }

  // Tutup sidebar mobile dengan Escape
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      closeMobileSidebar();
    }
  });

  // Tutup sidebar mobile setelah memilih menu
  if (userSidebar) {
    const userNavLinks = userSidebar.querySelectorAll(".user-nav-link");

    userNavLinks.forEach(function (link) {
      link.addEventListener("click", closeMobileSidebar);
    });
  }

  /* ========================================
     BAGIAN 2: DOM 1 - TANGGAL
     ======================================== */

  const dateElement = document.getElementById("userDate");

  function updateUserDate() {
    if (!dateElement) return;

    dateElement.textContent = new Date().toLocaleDateString("id-ID", {
      weekday: "long",
      day: "numeric",
      month: "long",
      year: "numeric",
    });
  }

  updateUserDate();

  /* ========================================
     BAGIAN 3: DOM 2 - JAM REAL-TIME
     ======================================== */

  const clockElement = document.getElementById("userClock");

  function updateUserClock() {
    if (!clockElement) return;

    clockElement.textContent = new Date().toLocaleTimeString("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
      hour12: false,
    });
  }

  updateUserClock();

  if (clockElement) {
    setInterval(updateUserClock, 1000);
  }

  /* ========================================
     BAGIAN 4: DOM 3 - INFORMASI KATALOG
     ======================================== */

  const catalogSection = document.getElementById("katalog");

  if (catalogSection) {
    let catalogInfo = document.getElementById("userCatalogInfo");

    if (!catalogInfo) {
      catalogInfo = document.createElement("p");
      catalogInfo.id = "userCatalogInfo";

      const catalogHeading = catalogSection.querySelector("h2, h3");

      if (catalogHeading) {
        catalogHeading.insertAdjacentElement("afterend", catalogInfo);
      } else {
        catalogSection.prepend(catalogInfo);
      }
    }

    const productCards = catalogSection.querySelectorAll(
      ".product-card, .catalog-card"
    );

    if (productCards.length > 0) {
      catalogInfo.textContent =
        "Tersedia " + productCards.length +
        " pilihan perlengkapan untuk petualanganmu.";
    } else {
      catalogInfo.textContent =
        "Temukan perlengkapan untuk petualanganmu.";
    }
  }

  /* ========================================
     BAGIAN 5: TOAST / SNACKBAR
     ======================================== */

  const snackbar = document.getElementById("userSnackbar");
  const snackbarMessage = document.getElementById(
    "userSnackbarMessage"
  );
  const catalogButton = document.getElementById("userCatalogButton");

  let snackbarTimer;

  function showUserSnackbar(message) {
    if (!snackbar || !snackbarMessage) return;

    snackbarMessage.textContent = message;
    snackbar.classList.add("show");

    clearTimeout(snackbarTimer);

    snackbarTimer = setTimeout(function () {
      snackbar.classList.remove("show");
    }, 3000);
  }

  // Notifikasi tombol katalog
  if (catalogButton) {
    catalogButton.addEventListener("click", function () {
      showUserSnackbar(
        "Yuk, pilih perlengkapan camping favoritmu!"
      );
    });
  }

  // Notifikasi saat menu katalog dipilih
  if (userSidebar) {
    const catalogLink = userSidebar.querySelector(
      '.user-nav-link[href="#katalog"]'
    );

    if (catalogLink) {
      catalogLink.addEventListener("click", function () {
        showUserSnackbar("Katalog perlengkapan camping dibuka.");
      });
    }
  }
});


/* =====================================
   NAVIGASI LOGIN DAN DASHBOARD USER
   ===================================== */

document.addEventListener("DOMContentLoaded", function () {
  const sessionData = localStorage.getItem("adventureAjaSession");

  let session = null;

  try {
    session = sessionData ? JSON.parse(sessionData) : null;
  } catch (error) {
    console.error("Data sesi tidak valid:", error);
    localStorage.removeItem("adventureAjaSession");
  }

  // Hanya perlakukan sesi dengan role user sebagai login pengguna.
  const isLoggedIn =
    session &&
    session.role === "user" &&
    session.email;

  if (!isLoggedIn) return;

  // Ubah tautan login menjadi tautan dashboard.
  document.querySelectorAll('a[href="login.php"]').forEach(function (link) {
    link.href = "user-dashboard.php";

    if (/masuk|login/i.test(link.textContent)) {
      link.textContent = "Dashboard";
    } else if (/pesanan saya|profil/i.test(link.textContent)) {
      link.href = "user-dashboard.php";
    }
  });

  // Sembunyikan tautan pendaftaran saat pengguna sudah login.
  document.querySelectorAll('a[href="register.php"]').forEach(function (link) {
    link.style.display = "none";
  });
});


/* =====================================
   POP-UP BOXES DAN EVENTS
   AdventureAja - Halaman User
   ===================================== */

document.addEventListener("DOMContentLoaded", function () {
  // EVENT 1: CLICK
  // POP-UP BOX 1: confirm()
  const catalogButton = document.getElementById("userCatalogButton");

  if (catalogButton) {
    catalogButton.addEventListener("click", function (event) {
      const lanjut = confirm(
        "Mau melihat perlengkapan camping AdventureAja?"
      );

      if (!lanjut) {
        event.preventDefault();
      }
    });
  }

  // EVENT 2: CHANGE
  // POP-UP BOX 2: prompt()
  const kategori = document.getElementById("kategoriCamping");

  if (kategori) {
    kategori.addEventListener("change", function () {
      if (!kategori.value) return;

      const nama = prompt(
        "Kamu memilih kategori " +
        kategori.options[kategori.selectedIndex].text +
        ". Siapa nama kamu?"
      );

      if (nama !== null && nama.trim() !== "") {
        alert("Halo, " + nama.trim() + "! Selamat memilih perlengkapan camping.");
      }
    });
  }

  // EVENT 3: SUBMIT
  // Contoh untuk formulir yang tersedia di halaman.
  const form = document.querySelector("form");

  if (form) {
    form.addEventListener("submit", function (event) {
      const yakin = confirm("Yakin ingin mengirim formulir ini?");

      if (!yakin) {
        event.preventDefault();
      }
    });
  }
});

// ==========================================
// FITUR PROMISE DAN FETCH API
// BAB 4 - JavaScript II
// ==========================================

document.addEventListener("DOMContentLoaded", function () {
    const loadQuoteButton = document.getElementById("load-quote-btn");
    const quoteResult = document.getElementById("quote-result");

    // Pastikan fitur hanya berjalan pada halaman yang memiliki elemennya.
    if (!loadQuoteButton || !quoteResult) {
        return;
    }

    // Mengambil data kutipan menggunakan Fetch API dan Promise.
    function fetchQuote() {
        return new Promise(function (resolve, reject) {
            fetch("https://dummyjson.com/quotes/random")
                .then(function (response) {
                    // Periksa apakah respons HTTP berhasil.
                    if (!response.ok) {
                        throw new Error("Gagal mengambil data dari server.");
                    }

                    return response.json();
                })
                .then(function (data) {
                    // Pastikan data kutipan tersedia.
                    if (!data.quote || !data.author) {
                        reject(new Error("Data kutipan tidak tersedia."));
                        return;
                    }

                    resolve(data);
                })
                .catch(function (error) {
                    reject(error);
                });
        });
    }

    // Event click untuk memuat inspirasi.
    loadQuoteButton.addEventListener("click", function () {
        loadQuoteButton.disabled = true;
        loadQuoteButton.textContent = "Memuat...";
        quoteResult.textContent = "Sedang mengambil inspirasi...";

        // Promise: then menangani hasil, catch menangani error,
        // dan finally dijalankan setelah proses selesai.
        fetchQuote()
            .then(function (data) {
                quoteResult.textContent =
                    '"' + data.quote + '" — ' + data.author;
            })
            .catch(function (error) {
                console.error("Kesalahan Fetch API:", error);

                quoteResult.textContent =
                    "Inspirasi gagal dimuat. Periksa koneksi internet lalu coba lagi.";
            })
            .finally(function () {
                loadQuoteButton.disabled = false;
                loadQuoteButton.textContent = "Muat Inspirasi";
            });
    });
});