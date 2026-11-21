// Konfirmasi sebelum menghapus data.
var confirmButtons = document.querySelectorAll('[data-confirm]');
confirmButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
        var message = button.dataset.confirm || 'Lanjutkan?';

        if (!window.confirm(message)) {
            event.preventDefault();
        }
    });
});

// Menampilkan cover baru sebelum form dikirim.
var coverInputs = document.querySelectorAll('[data-cover-preview]');
coverInputs.forEach(function (input) {
    input.addEventListener('change', function () {
        var file = input.files[0];
        var preview = document.querySelector(input.dataset.coverPreview);

        if (file && preview) {
            preview.src = URL.createObjectURL(file);
        }
    });
});

// Tombol mata: melihat atau menyembunyikan password.
var passwordButtons = document.querySelectorAll('[data-password-toggle]');
passwordButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        var input = document.getElementById(button.dataset.passwordToggle);

        if (!input) {
            return;
        }

        if (input.type == 'password') {
            input.type = 'text';
            button.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
            button.setAttribute('aria-label', 'Sembunyikan password');
            button.setAttribute('title', 'Sembunyikan password');
        } else {
            input.type = 'password';
            button.innerHTML = '<i class="fa-solid fa-eye"></i>';
            button.setAttribute('aria-label', 'Tampilkan password');
            button.setAttribute('title', 'Tampilkan password');
        }
    });
});

// Membuka menu samping pada layar kecil.
var menuButton = document.querySelector('[data-sidebar-toggle]');
if (menuButton) {
    menuButton.addEventListener('click', function () {
        var sidebar = document.querySelector('#sidebar');

        if (sidebar) {
            sidebar.classList.toggle('open');
        }
    });
}
