// ========================================
// SIDEBAR ADMIN
// ========================================

const sidebar = document.getElementById("sidebar");
const sidebarToggle = document.getElementById("sidebarToggle");
const mainContent = document.querySelector(".main-content");

if (sidebar && sidebarToggle && mainContent) {
    const backdrop = document.createElement("div");
    backdrop.className = "sidebar-backdrop";
    backdrop.hidden = true;
    document.body.appendChild(backdrop);

    function isMobileScreen() {
        return window.matchMedia("(max-width: 480px)").matches;
    }

    sidebarToggle.addEventListener("click", function () {
        if (isMobileScreen()) {
            const isOpen = sidebar.classList.toggle("open");
            backdrop.hidden = !isOpen;
            sidebarToggle.setAttribute("aria-expanded", String(isOpen));
        } else {
            const isClosed = sidebar.classList.toggle("close");
            mainContent.classList.toggle("sidebar-closed", isClosed);
            sidebarToggle.setAttribute("aria-expanded", String(!isClosed));
        }
    });

    backdrop.addEventListener("click", function () {
        sidebar.classList.remove("open");
        backdrop.hidden = true;
        sidebarToggle.setAttribute("aria-expanded", "false");
    });

    window.addEventListener("resize", function () {
        sidebar.classList.remove("open", "close");
        mainContent.classList.remove("sidebar-closed");
        backdrop.hidden = true;
        sidebarToggle.setAttribute("aria-expanded", "true");
    });
}


// ========================================
// CRUD KATEGORI
// ========================================

const STORAGE_KEY = "adventureAjaCategories";

// Data awal untuk pertama kali halaman dibuka.
const defaultCategories = [
    {
        id: "cat-1",
        name: "Tenda",
        description: "Perlengkapan tempat berteduh",
        status: "aktif"
    },
    {
        id: "cat-2",
        name: "Carrier",
        description: "Tas untuk membawa perlengkapan",
        status: "aktif"
    },
    {
        id: "cat-3",
        name: "Sleeping Bag",
        description: "Perlengkapan tidur di alam terbuka",
        status: "aktif"
    },
    {
        id: "cat-4",
        name: "Penerangan",
        description: "Lampu dan alat penerangan camping",
        status: "aktif"
    }
];

// Ambil data dari localStorage.
function getCategories() {
    const savedData = localStorage.getItem(STORAGE_KEY);

    if (savedData === null) {
        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(defaultCategories)
        );

        return [...defaultCategories];
    }

    try {
        const categories = JSON.parse(savedData);
        return Array.isArray(categories) ? categories : [];
    } catch (error) {
        console.error("Data kategori tidak valid:", error);
        return [];
    }
}

// Simpan data ke localStorage.
function saveCategories(categories) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(categories));
}

// Buat ID sederhana untuk setiap kategori.
function createCategoryId() {
    return "cat-" + Date.now() + "-" +
        Math.random().toString(36).slice(2, 7);
}


// ========================================
// READ: TAMPILKAN DATA KATEGORI
// ========================================

function renderCategories() {
    const tableBody = document.getElementById("categoryTableBody");
    const categoryCount = document.getElementById("categoryCount");

    // Halaman lain tidak perlu menjalankan render tabel.
    if (!tableBody) return;

    const categories = getCategories();
    tableBody.replaceChildren();

    categories.forEach(function (category, index) {
        const row = document.createElement("tr");

        const numberCell = document.createElement("td");
        numberCell.textContent = String(index + 1).padStart(2, "0");

        const nameCell = document.createElement("td");
        const nameWrapper = document.createElement("div");
        nameWrapper.className = "category-name";

        const symbol = document.createElement("div");
        symbol.className = "category-symbol";
        symbol.textContent = category.name.charAt(0).toUpperCase();

        const name = document.createElement("span");
        name.textContent = category.name;

        nameWrapper.append(symbol, name);
        nameCell.appendChild(nameWrapper);

        const descriptionCell = document.createElement("td");
        descriptionCell.textContent = category.description;

        const statusCell = document.createElement("td");
        const statusBadge = document.createElement("span");
        statusBadge.className = category.status === "aktif"
            ? "status-badge status-active"
            : "status-badge status-inactive";
        statusBadge.textContent = category.status === "aktif"
            ? "Aktif"
            : "Nonaktif";
        statusCell.appendChild(statusBadge);

        const actionCell = document.createElement("td");
        const actions = document.createElement("div");
        actions.className = "action-buttons";

        // Tombol edit.
        const editButton = document.createElement("a");
        editButton.href =
            "categories-entry.php?edit=" +
            encodeURIComponent(category.id);
        editButton.className = "action-button edit-button";
        editButton.textContent = "Edit";

        // Tombol hapus.
        const deleteButton = document.createElement("button");
        deleteButton.type = "button";
        deleteButton.className = "action-button delete-button";
        deleteButton.textContent = "Hapus";
        deleteButton.addEventListener("click", function () {
            deleteCategory(category.id);
        });

        actions.append(editButton, deleteButton);
        actionCell.appendChild(actions);

        row.append(
            numberCell,
            nameCell,
            descriptionCell,
            statusCell,
            actionCell
        );

        tableBody.appendChild(row);
    });

    if (categoryCount) {
        categoryCount.textContent =
            "Menampilkan " + categories.length + " kategori perlengkapan";
    }
}


// ========================================
// DELETE: HAPUS KATEGORI
// ========================================

function deleteCategory(id) {
    const categories = getCategories();
    const category = categories.find(function (item) {
        return item.id === id;
    });

    if (!category) return;

    const confirmed = confirm(
        'Yakin ingin menghapus kategori "' + category.name + '"?'
    );

    if (!confirmed) return;

    const updatedCategories = categories.filter(function (item) {
        return item.id !== id;
    });

    saveCategories(updatedCategories);
    renderCategories();
}


// ========================================
// CREATE & UPDATE: FORM KATEGORI
// ========================================

const categoryForm = document.getElementById("categoryForm");

if (categoryForm) {
    const idInput = document.getElementById("categoryId");
    const nameInput = document.getElementById("categoryName");
    const descriptionInput =
        document.getElementById("categoryDescription");
    const statusInput = document.getElementById("categoryStatus");
    const title = document.getElementById("categoryFormTitle");
    const submitButton =
        document.getElementById("categorySubmitButton");

    const params = new URLSearchParams(window.location.search);
    const editId = params.get("edit");

    // Jika URL berisi ?edit=..., isi form dengan data lama.
    if (editId) {
        const categories = getCategories();
        const category = categories.find(function (item) {
            return item.id === editId;
        });

        if (category) {
            idInput.value = category.id;
            nameInput.value = category.name;
            descriptionInput.value = category.description;
            statusInput.value = category.status;

            if (title) title.textContent = "Edit Kategori Perlengkapan";
            if (submitButton) submitButton.textContent = "Simpan Perubahan";
        } else {
            alert("Kategori tidak ditemukan.");
            window.location.href = "categories.php";
        }
    }

    categoryForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const name = nameInput.value.trim();
        const description = descriptionInput.value.trim();
        const status = statusInput.value;

        if (!name || !description) {
            alert("Nama dan deskripsi kategori wajib diisi.");
            return;
        }

        let categories = getCategories();

        // Cegah nama kategori duplikat.
        const duplicate = categories.some(function (item) {
            return item.name.toLowerCase() === name.toLowerCase()
                && item.id !== idInput.value;
        });

        if (duplicate) {
            alert("Nama kategori tersebut sudah tersedia.");
            return;
        }

        if (idInput.value) {
            // UPDATE: perbarui kategori yang dipilih.
            categories = categories.map(function (item) {
                if (item.id === idInput.value) {
                    return {
                        ...item,
                        name: name,
                        description: description,
                        status: status
                    };
                }

                return item;
            });

            alert("Kategori berhasil diperbarui.");
        } else {
            // CREATE: tambahkan kategori baru.
            categories.push({
                id: createCategoryId(),
                name: name,
                description: description,
                status: status
            });

            alert("Kategori berhasil ditambahkan.");
        }

        saveCategories(categories);
        window.location.href = "categories.php";
    });
}


// Tampilkan data saat halaman daftar kategori dibuka.
renderCategories();

// ========================================
// CRUD TRANSAKSI ADVENTUREAJA
// ========================================

const TRANSACTION_KEY = "adventureAjaTransactions";

const defaultTransactions = [
    {
        id: "TRX-001",
        name: "Rizky Pratama",
        phone: "081234567890",
        item: "Tenda 3 Layer",
        quantity: 1,
        total: 150000,
        startDate: "2026-10-20",
        endDate: "2026-10-23",
        status: "Diproses",
        address: "Malang",
        note: ""
    },
    {
        id: "TRX-002",
        name: "Dinda Putri",
        phone: "081234567891",
        item: "Carrier 60L",
        quantity: 2,
        total: 100000,
        startDate: "2026-10-21",
        endDate: "2026-10-24",
        status: "Selesai",
        address: "Malang",
        note: ""
    },
    {
        id: "TRX-003",
        name: "Andi Saputra",
        phone: "081234567892",
        item: "Sleeping Bag",
        quantity: 2,
        total: 50000,
        startDate: "2026-10-22",
        endDate: "2026-10-25",
        status: "Menunggu",
        address: "Malang",
        note: ""
    }
];

// Ambil transaksi yang tersimpan.
function getTransactions() {
    const saved = localStorage.getItem(TRANSACTION_KEY);

    if (saved === null) {
        localStorage.setItem(
            TRANSACTION_KEY,
            JSON.stringify(defaultTransactions)
        );
        return [...defaultTransactions];
    }

    try {
        const data = JSON.parse(saved);
        return Array.isArray(data) ? data : [];
    } catch (error) {
        console.error("Data transaksi tidak valid:", error);
        return [];
    }
}

// Simpan transaksi.
function saveTransactions(data) {
    localStorage.setItem(TRANSACTION_KEY, JSON.stringify(data));
}

// Buat kode transaksi otomatis.
function createTransactionId() {
    const transactions = getTransactions();
    let number = 1;

    while (transactions.some(function (item) {
        return item.id === "TRX-" + String(number).padStart(3, "0");
    })) {
        number++;
    }

    return "TRX-" + String(number).padStart(3, "0");
}

// Format tanggal ke tampilan Indonesia.
function formatTransactionDate(dateString) {
    if (!dateString) return "-";

    const date = new Date(dateString + "T00:00:00");

    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric"
    });
}

// Format nominal rupiah.
function formatRupiah(amount) {
    return "Rp" + Number(amount).toLocaleString("id-ID");
}

// Tentukan class status.
function getTransactionStatusClass(status) {
    const classes = {
        "Menunggu": "status-pending",
        "Diproses": "status-process",
        "Sedang Disewa": "status-process",
        "Selesai": "status-complete",
        "Dibatalkan": "status-inactive"
    };

    return classes[status] || "status-pending";
}

// ========================================
// READ: TAMPILKAN TRANSAKSI
// ========================================

function renderTransactions() {
    const tableBody = document.getElementById("transactionTableBody");

    if (!tableBody) return;

    const transactions = getTransactions();
    tableBody.replaceChildren();

    transactions.forEach(function (transaction) {
        const row = document.createElement("tr");

        function addCell(text, className = "") {
            const cell = document.createElement("td");
            cell.textContent = text;
            if (className) cell.className = className;
            row.appendChild(cell);
            return cell;
        }

        addCell(transaction.id, "transaction-id");

        const customerCell = document.createElement("td");
        const customerWrapper = document.createElement("div");
        customerWrapper.className = "customer-cell";

        const avatar = document.createElement("div");
        avatar.className = "customer-avatar";
        avatar.textContent = transaction.name.charAt(0).toUpperCase();

        const customerName = document.createElement("span");
        customerName.textContent = transaction.name;

        customerWrapper.append(avatar, customerName);
        customerCell.appendChild(customerWrapper);
        row.appendChild(customerCell);

        addCell(transaction.item);

        addCell(
            formatTransactionDate(transaction.startDate) +
            " – " +
            formatTransactionDate(transaction.endDate)
        );

        addCell(formatRupiah(transaction.total));

        const statusCell = document.createElement("td");
        const statusBadge = document.createElement("span");
        statusBadge.className =
            "status-badge " +
            getTransactionStatusClass(transaction.status);
        statusBadge.textContent = transaction.status;
        statusCell.appendChild(statusBadge);
        row.appendChild(statusCell);

        const actionCell = document.createElement("td");
        const actions = document.createElement("div");
        actions.className = "action-buttons";

        const editButton = document.createElement("a");
        editButton.href =
            "transaction-entry.php?edit=" +
            encodeURIComponent(transaction.id);
        editButton.className = "action-button edit-button";
        editButton.textContent = "Edit";

        const deleteButton = document.createElement("button");
        deleteButton.type = "button";
        deleteButton.className = "action-button delete-button";
        deleteButton.textContent = "Hapus";

        deleteButton.addEventListener("click", function () {
            deleteTransaction(transaction.id);
        });

        actions.append(editButton, deleteButton);
        actionCell.appendChild(actions);
        row.appendChild(actionCell);

        tableBody.appendChild(row);
    });

    // Perbarui jumlah transaksi.
    const count = document.getElementById("transactionCount");
    if (count) {
        count.textContent =
            "Menampilkan " + transactions.length + " transaksi";
    }

    // Perbarui ringkasan.
    const setCount = function (id, value) {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    };

    setCount("totalTransactions", transactions.length);

    setCount(
        "pendingTransactions",
        transactions.filter(item => item.status === "Menunggu").length
    );

    setCount(
        "activeTransactions",
        transactions.filter(item =>
            item.status === "Sedang Disewa"
        ).length
    );

    setCount(
        "completedTransactions",
        transactions.filter(item => item.status === "Selesai").length
    );
}

// ========================================
// DELETE: HAPUS TRANSAKSI
// ========================================

function deleteTransaction(id) {
    const transactions = getTransactions();
    const transaction = transactions.find(item => item.id === id);

    if (!transaction) return;

    if (!confirm(
        'Yakin ingin menghapus transaksi "' + id + '"?'
    )) {
        return;
    }

    const updated = transactions.filter(item => item.id !== id);

    saveTransactions(updated);
    renderTransactions();

    alert("Transaksi berhasil dihapus.");
}

// ========================================
// CREATE & UPDATE: FORM TRANSAKSI
// ========================================

const transactionForm = document.getElementById("transactionForm");

if (transactionForm) {
    const field = id => document.getElementById(id);

    const params = new URLSearchParams(window.location.search);
    const editId = params.get("edit");

    const idInput = field("transactionId");
    const codeInput = field("kodeTransaksi");
    const nameInput = field("namaPenyewa");
    const phoneInput = field("nomorTelepon");
    const itemInput = field("barang");
    const quantityInput = field("jumlah");
    const totalInput = field("totalBiaya");
    const startInput = field("tanggalSewa");
    const endInput = field("tanggalKembali");
    const statusInput = field("status");
    const addressInput = field("alamat");
    const noteInput = field("catatan");

    const pageTitle = field("transactionPageTitle");
    const submitButton = field("transactionSubmitButton");

    // Jika mode edit, isi form dengan data yang dipilih.
    if (editId) {
        const transaction = getTransactions().find(
            item => item.id === editId
        );

        if (!transaction) {
            alert("Transaksi tidak ditemukan.");
            window.location.href = "transaction.php";
        } else {
            idInput.value = transaction.id;
            codeInput.value = transaction.id;
            nameInput.value = transaction.name;
            phoneInput.value = transaction.phone;
            itemInput.value = transaction.item;
            quantityInput.value = transaction.quantity;
            totalInput.value = transaction.total;
            startInput.value = transaction.startDate;
            endInput.value = transaction.endDate;
            statusInput.value = transaction.status;
            addressInput.value = transaction.address;
            noteInput.value = transaction.note || "";

            if (pageTitle) pageTitle.textContent = "Edit Transaksi";
            if (submitButton) submitButton.textContent = "Simpan Perubahan";
        }
    } else {
        // Kode otomatis untuk transaksi baru.
        codeInput.value = createTransactionId();
        codeInput.readOnly = true;
    }

    transactionForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const name = nameInput.value.trim();
        const phone = phoneInput.value.trim();
        const item = itemInput.value;
        const quantity = Number(quantityInput.value);
        const total = Number(totalInput.value);
        const startDate = startInput.value;
        const endDate = endInput.value;
        const status = statusInput.value;
        const address = addressInput.value.trim();

        if (!name || !phone || !item || !address) {
            alert("Lengkapi semua data wajib.");
            return;
        }

        if (quantity < 1 || total < 0) {
            alert("Jumlah barang atau total biaya tidak valid.");
            return;
        }

        if (endDate < startDate) {
            alert("Tanggal pengembalian tidak boleh sebelum tanggal sewa.");
            return;
        }

        let transactions = getTransactions();

        const transactionData = {
            id: idInput.value || createTransactionId(),
            name: name,
            phone: phone,
            item: item,
            quantity: quantity,
            total: total,
            startDate: startDate,
            endDate: endDate,
            status: status,
            address: address,
            note: noteInput.value.trim()
        };

        if (idInput.value) {
            // UPDATE.
            transactions = transactions.map(item =>
                item.id === idInput.value ? transactionData : item
            );

            alert("Transaksi berhasil diperbarui.");
        } else {
            // CREATE.
            transactions.push(transactionData);
            alert("Transaksi berhasil ditambahkan.");
        }

        saveTransactions(transactions);
        window.location.href = "transaction.php";
    });
}

// Jalankan render hanya jika tabel transaksi ada.
renderTransactions();