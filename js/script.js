// Interaksi sidebar admin digunakan bersama di halaman admin.
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const mainContent = document.querySelector('.main-content');

if (sidebar && sidebarToggle && mainContent) {
    const backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    backdrop.hidden = true;
    document.body.appendChild(backdrop);

    const isMobileScreen = () => window.matchMedia('(max-width: 480px)').matches;

    sidebarToggle.addEventListener('click', function () {
        if (isMobileScreen()) {
            const isOpen = sidebar.classList.toggle('open');
            backdrop.hidden = !isOpen;
            sidebarToggle.setAttribute('aria-expanded', String(isOpen));
            return;
        }

        const isClosed = sidebar.classList.toggle('close');
        mainContent.classList.toggle('sidebar-closed', isClosed);
        sidebarToggle.setAttribute('aria-expanded', String(!isClosed));
    });

    backdrop.addEventListener('click', function () {
        sidebar.classList.remove('open');
        backdrop.hidden = true;
        sidebarToggle.setAttribute('aria-expanded', 'false');
    });

    window.addEventListener('resize', function () {
        sidebar.classList.remove('open', 'close');
        mainContent.classList.remove('sidebar-closed');
        backdrop.hidden = true;
        sidebarToggle.setAttribute('aria-expanded', 'true');
    });
}
