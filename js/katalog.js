
const STORAGE_KEY = "adventureAjaKatalog";

// DATA AWAL KATALOG
const dataAwal = [
    {
        id: 1,
        nama: "Tenda 3 Layer",
        deskripsi: "Perlengkapan tenda camping",
        harga: 40000,
        foto: "../assets/tenda.png"
    },
    {
        id: 2,
        nama: "Carrier 60L",
        deskripsi: "Tas untuk perjalanan mendaki",
        harga: 50000,
        foto: "../assets/carrier.png"
    },
    {
        id: 3,
        nama: "Sleeping Bag",
        deskripsi: "Kantong tidur untuk camping",
        harga: 25000,
        foto: "../assets/sleeping-bag.png"
    },
    {
        id: 4,
        nama: "Lampu Tenda",
        deskripsi: "Penerangan saat malam hari",
        harga: 7000,
        foto: "../assets/lampu.png"
    }
];


async function muatDataDenganFetch() {
    // Jika data sudah ada, pertahankan data CRUD pengguna
    const dataTersimpan = localStorage.getItem(STORAGE_KEY);

    if (dataTersimpan !== null) {
        return;
    }

    try {
        const response = await fetch("../data/katalog.json");

        if (!response.ok) {
            throw new Error("Gagal mengambil data katalog");
        }

        const data = await response.json();

        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch (error) {
        console.error("Fetch gagal:", error);

        // Cadangan jika Fetch gagal
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dataAwal));
    }
}


// WEB STORAGE: MENGAMBIL DATA DARI LOCAL STORAGE
function ambilData() {
    const data = localStorage.getItem(STORAGE_KEY);

    if (data === null) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dataAwal));
        return [...dataAwal];
    }

    try {
        const hasil = JSON.parse(data);
        return Array.isArray(hasil) ? hasil : [...dataAwal];
    } catch (error) {
        console.error("Data katalog tidak valid:", error);
        return [...dataAwal];
    }
}

// WEB STORAGE: MENYIMPAN DATA
function simpanData(data) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

// FORMAT HARGA KE RUPIAH
function formatRupiah(harga) {
    return "Rp" + Number(harga).toLocaleString("id-ID");
}

// MENCEGAH TEKS DATA DIBACA SEBAGAI HTML
function escapeHTML(teks) {
    return String(teks).replace(/[&<>"']/g, function (karakter) {
        const karakterHTML = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        };

        return karakterHTML[karakter];
    });
}

// READ: MENAMPILKAN DATA KATALOG
function tampilkanKatalog() {
    const tabel = document.getElementById("katalogTable");

    if (!tabel) return;

    const data = ambilData();

    if (data.length === 0) {
        tabel.innerHTML = `
            <tr>
                <td colspan="5" style="text-align:center">
                    Belum ada data perlengkapan.
                </td>
            </tr>
        `;
        return;
    }

    tabel.innerHTML = data.map(item => `
        <tr>
            <td>
                <img
                    src="${escapeHTML(item.foto)}"
                    alt="${escapeHTML(item.nama)}"
                    class="table-img"
                >
            </td>
            <td>${escapeHTML(item.nama)}</td>
            <td>${escapeHTML(item.deskripsi)}</td>
            <td>${formatRupiah(item.harga)}</td>
            <td>
                <div class="table-action">
                    <button
                        type="button"
                        class="btn-edit"
                        data-action="edit"
                        data-id="${Number(item.id)}"
                    >
                        Edit
                    </button>

                    <button
                        type="button"
                        class="btn-delete"
                        data-action="hapus"
                        data-id="${Number(item.id)}"
                    >
                        Hapus
                    </button>
                </div>
            </td>
        </tr>
    `).join("");
}

// UPDATE: MEMBUKA FORM EDIT
function editKatalog(id) {
    const data = ambilData();
    const item = data.find(perlengkapan => perlengkapan.id === id);

    if (!item) {
        tampilkanToast("Data perlengkapan tidak ditemukan!", "error");
        return;
    }

    window.location.href = `katalog-entry.html?id=${id}`;
}

// DELETE: MENGHAPUS DATA
function hapusKatalog(id) {
    const data = ambilData();
    const item = data.find(perlengkapan => perlengkapan.id === id);

    if (!item) {
        tampilkanToast("Data perlengkapan tidak ditemukan!", "error");
        return;
    }

    // POPUP BOX 1: Meminta konfirmasi sebelum menghapus
    const konfirmasi = confirm(
        `Yakin ingin menghapus "${item.nama}"?`
    );

    if (!konfirmasi) {
        tampilkanToast("Penghapusan dibatalkan.");
        return;
    }

    const dataBaru = data.filter(perlengkapan => perlengkapan.id !== id);

    try {
        simpanData(dataBaru);
        tampilkanKatalog();

        // POPUP BOX 2: Memberi informasi setelah penghapusan
        alert(`"${item.nama}" berhasil dihapus!`);

        tampilkanToast("Data perlengkapan berhasil dihapus!");
    } catch (error) {
        console.error("Gagal menghapus data:", error);
        tampilkanToast("Data gagal dihapus!", "error");
    }
}

// CREATE DAN UPDATE: MENYIMPAN FORM
function simpanFormKatalog(event) {
    event.preventDefault();

    const nama = document.getElementById("nama").value.trim();
    const deskripsi = document.getElementById("deskripsi").value.trim();
    const harga = Number(document.getElementById("harga").value);
    const inputFoto = document.getElementById("foto");

    if (!nama || !deskripsi || !Number.isFinite(harga) || harga <= 0) {
        tampilkanToast(
            "Nama, deskripsi, dan harga harus diisi dengan benar!",
            "error"
        );
        return;
    }

    const data = ambilData();
    const parameter = new URLSearchParams(window.location.search);
    const idEdit = Number(parameter.get("id")) || null;
    const fileFoto = inputFoto ? inputFoto.files[0] : null;

    const itemLama = idEdit
        ? data.find(item => item.id === idEdit)
        : null;

    if (idEdit && !itemLama) {
        tampilkanToast("Data yang akan diedit tidak ditemukan!", "error");
        return;
    }

    // CREATE: Menambahkan data baru atau UPDATE: memperbarui data
    function simpan(foto) {
        let dataBaru;

        if (idEdit) {
            dataBaru = data.map(item => {
                if (item.id !== idEdit) return item;

                return {
                    ...item,
                    nama,
                    deskripsi,
                    harga,
                    foto: foto || item.foto
                };
            });
        } else {
            const idBaru = data.length
                ? Math.max(...data.map(item => item.id)) + 1
                : 1;

            dataBaru = [
                ...data,
                {
                    id: idBaru,
                    nama,
                    deskripsi,
                    harga,
                    foto: foto || "../assets/tenda.png"
                }
            ];
        }

        try {
            // WEB STORAGE: menyimpan data katalog
            simpanData(dataBaru);

            // SESSION STORAGE: menyimpan pesan sementara
            sessionStorage.setItem(
                "pesanToast",
                idEdit
                    ? "Data berhasil diperbarui!"
                    : "Perlengkapan berhasil ditambahkan!"
            );

            window.location.href = "katalog.html";
        } catch (error) {
            console.error("Gagal menyimpan data:", error);

            tampilkanToast(
                "Gagal menyimpan data. Coba gunakan foto yang lebih kecil.",
                "error"
            );
        }
    }

    // Validasi foto hanya jika pengguna memilih foto baru
    if (fileFoto) {
        if (!fileFoto.type.startsWith("image/")) {
            tampilkanToast("Pilih file gambar yang valid!", "error");
            return;
        }

        if (fileFoto.size > 2 * 1024 * 1024) {
            tampilkanToast("Ukuran foto maksimal 2 MB!", "error");
            return;
        }

        // ASYNCHRONOUS: FileReader membaca foto tanpa menghentikan UI
        const reader = new FileReader();

        reader.onload = function () {
            simpan(reader.result);
        };

        reader.onerror = function () {
            tampilkanToast("Foto gagal dibaca!", "error");
        };

        reader.readAsDataURL(fileFoto);
    } else if (idEdit) {
        // Saat edit, foto lama tetap digunakan jika tidak memilih foto baru
        simpan(itemLama.foto);
    } else {
        // Foto bawaan untuk data baru jika pengguna tidak memilih foto
        simpan("../assets/tenda.png");
    }
}

// MENJALANKAN FUNGSI SESUAI HALAMAN
document.addEventListener("DOMContentLoaded", async function () {
    await muatDataDenganFetch();

    // Lanjutkan seluruh kode yang sudah ada di sini:
    tampilkanKatalog();
    isiFormEdit();

    // EVENT SUBMIT: menangani pengiriman form tambah/edit
    const form = document.querySelector(".form-card form");

    if (form) {
        form.addEventListener("submit", simpanFormKatalog);
    }

    // EVENT CLICK: menangani tombol Edit dan Hapus
    const tabel = document.getElementById("katalogTable");

    if (tabel) {
        tabel.addEventListener("click", function (event) {
            const tombol = event.target.closest("button[data-action]");

            if (!tombol || !tabel.contains(tombol)) return;

            const id = Number(tombol.dataset.id);

            if (!id) return;

            if (tombol.dataset.action === "edit") {
                editKatalog(id);
            } else if (tombol.dataset.action === "hapus") {
                hapusKatalog(id);
            }
        });
    }

    // EVENT CHANGE: mendeteksi pilihan foto
    const inputFoto = document.getElementById("foto");

    if (inputFoto) {
        inputFoto.addEventListener("change", function () {
            const file = inputFoto.files[0];

            if (!file) return;

            if (!file.type.startsWith("image/")) {
                tampilkanToast("Pilih file gambar yang valid!", "error");
                inputFoto.value = "";
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                tampilkanToast("Ukuran foto maksimal 2 MB!", "error");
                inputFoto.value = "";
                return;
            }

            tampilkanToast(`Foto "${file.name}" berhasil dipilih!`);
        });
    }

    // Menampilkan Toast setelah kembali dari form
    const pesan = sessionStorage.getItem("pesanToast");

    if (pesan) {
        sessionStorage.removeItem("pesanToast");
        tampilkanToast(pesan);
    }
});

// MENGISI FORM SAAT MODE EDIT
function isiFormEdit() {
    const form = document.querySelector(".form-card form");

    if (!form) return;

    const parameter = new URLSearchParams(window.location.search);
    const idEdit = Number(parameter.get("id"));

    if (!idEdit) return;

    const item = ambilData().find(data => data.id === idEdit);

    if (!item) {
        tampilkanToast("Data perlengkapan tidak ditemukan!", "error");

        setTimeout(() => {
            window.location.href = "katalog.html";
        }, 1500);

        return;
    }

    document.getElementById("nama").value = item.nama;
    document.getElementById("deskripsi").value = item.deskripsi;
    document.getElementById("harga").value = item.harga;

    const judul = document.querySelector(".section-heading h2");

    if (judul) {
        judul.textContent = "Edit Perlengkapan";
    }

    const tombol = form.querySelector('button[type="submit"]');

    if (tombol) {
        tombol.innerHTML = '<i class="bx bx-save"></i> Simpan Perubahan';
    }

    // Foto tidak diwajibkan saat edit karena foto lama bisa dipertahankan
    const inputFoto = document.getElementById("foto");

    if (inputFoto) {
        inputFoto.required = false;
    }
}
