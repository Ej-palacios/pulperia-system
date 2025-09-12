// usuarios.js - Scripts para gestión de usuarios
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario de usuarios
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');
            
            if (password.value && password.value !== passwordConfirmation.value) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                passwordConfirmation.focus();
            }
        });
    }

    // Búsqueda y filtrado de usuarios
    const userSearch = document.getElementById('userSearch');
    if (userSearch) {
        const searchHandler = PulperiaUtils.debounce(function() {
            const searchTerm = userSearch.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            
            rows.forEach(row => {
                const userName = row.cells[0].textContent.toLowerCase();
                const userEmail = row.cells[1].textContent.toLowerCase();
                const userRole = row.cells[2].textContent.toLowerCase();
                
                if (userName.includes(searchTerm) || userEmail.includes(searchTerm) || userRole.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }, 300);

        userSearch.addEventListener('input', searchHandler);
    }

    // Cambio de estado de usuario
    document.querySelectorAll('.user-status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const userId = this.getAttribute('data-user-id');
            const isActive = this.checked;
            
            fetch(`/api/usuarios/${userId}/estado`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ activo: isActive })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.querySelector(`.user-status-badge[data-user-id="${userId}"]`);
                    if (badge) {
                        badge.className = `badge bg-${isActive ? 'success' : 'danger'} user-status-badge`;
                        badge.textContent = isActive ? 'Activo' : 'Inactivo';
                    }
                } else {
                    this.checked = !isActive;
                    alert('Error al actualizar el estado');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !isActive;
                alert('Error al actualizar el estado');
            });
        });
    });

    // Exportar lista de usuarios
    const exportUsersBtn = document.getElementById('exportUsers');
    if (exportUsersBtn) {
        exportUsersBtn.addEventListener('click', function() {
            const format = this.getAttribute('data-format');
            window.location.href = `/usuarios/exportar?formato=${format}`;
        });
    }
});