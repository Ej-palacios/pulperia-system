// dashboard.js - Scripts específicos para el dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de ventas semanales
    const initSalesChart = () => {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        // Datos de ejemplo para el gráfico
        const salesData = {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Ventas Diarias (C$)',
                data: [1200, 1900, 1500, 2100, 1800, 2500, 2200],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                tension: 0.4
            }]
        };

        new Chart(ctx, {
            type: 'line',
            data: salesData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Ventas de la Semana'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'C$ ' + value;
                            }
                        }
                    }
                }
            }
        });
    };

    // Gráfico de categorías de productos
    const initCategoriesChart = () => {
        const ctx = document.getElementById('categoriesChart');
        if (!ctx) return;

        const categoriesData = {
            labels: ['Abarrotes', 'Bebidas', 'Lácteos', 'Dulces', 'Limpieza'],
            datasets: [{
                label: 'Ventas por Categoría',
                data: [12000, 8000, 6000, 4000, 3000],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderWidth: 1
            }]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: categoriesData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    };

    // Actualizar datos en tiempo real
    const updateRealTimeData = () => {
        // Simular actualización de datos
        setInterval(() => {
            document.querySelectorAll('.real-time-data').forEach(element => {
                const randomChange = Math.random() * 100;
                element.textContent = formatCurrency(randomChange + 1000);
            });
        }, 30000);
    };

    // Inicializar todos los componentes
    initSalesChart();
    initCategoriesChart();
    updateRealTimeData();

    // Botones de acción rápida
    document.querySelectorAll('.quick-action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            switch(action) {
                case 'new-sale':
                    window.location.href = '/ventas/pos';
                    break;
                case 'new-client':
                    window.location.href = '/clientes/create';
                    break;
                case 'inventory-check':
                    window.location.href = '/productos';
                    break;
            }
        });
    });
});