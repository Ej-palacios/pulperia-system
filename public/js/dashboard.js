document.addEventListener('DOMContentLoaded', function() {
    // Initialize sales chart
    initSalesChart();
    
    // Initialize clients chart
    initClientsChart();
    
    // Initialize chart period controls
    initChartControls();
    
    // Initialize product filter
    initProductFilter();
    
    // Initialize dashboard animations
    initDashboardAnimations();
});

// Initialize sales chart
function initSalesChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Ventas',
                data: [1200, 1900, 3000, 5000, 2000, 3000, 4500],
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3498db',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#e5e7eb',
                    borderColor: '#3498db',
                    borderWidth: 1,
                    padding: 12,
                    boxPadding: 6,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            return 'C$ ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'C$ ' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
    
    // Store chart instance for later updates
    window.salesChart = salesChart;
}

// Initialize clients chart
function initClientsChart() {
    const ctx = document.getElementById('clientsChart').getContext('2d');
    
    const clientsChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Registrados', 'No Registrados'],
            datasets: [{
                data: [70, 30], // Estos valores deberían venir del controlador
                backgroundColor: [
                    'rgba(39, 174, 96, 0.8)',
                    'rgba(243, 156, 18, 0.8)'
                ],
                borderColor: [
                    'rgba(39, 174, 96, 1)',
                    'rgba(243, 156, 18, 1)'
                ],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#e5e7eb',
                    borderColor: '#3498db',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
    
    window.clientsChart = clientsChart;
}

// Initialize chart period controls
function initChartControls() {
    const chartBtns = document.querySelectorAll('.chart-btn');
    
    chartBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            chartBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Update chart data based on period
            const period = this.dataset.period;
            updateChartData(period);
        });
    });
}

// Update chart data based on selected period
function updateChartData(period) {
    // Show loading state
    const chartContainer = document.querySelector('.chart-container');
    chartContainer.classList.add('loading');
    
    // Simulate API call
    setTimeout(() => {
        // This would typically make an AJAX call to get new data
        let newData;
        let newLabels;
        
        switch(period) {
            case '7d':
                newData = [1200, 1900, 3000, 5000, 2000, 3000, 4500];
                newLabels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                break;
            case '30d':
                newData = Array.from({length: 30}, () => Math.floor(Math.random() * 5000) + 1000);
                newLabels = Array.from({length: 30}, (_, i) => (i + 1).toString());
                break;
            case '90d':
                newData = Array.from({length: 12}, () => Math.floor(Math.random() * 15000) + 5000);
                newLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                break;
        }
        
        window.salesChart.data.datasets[0].data = newData;
        window.salesChart.data.labels = newLabels;
        window.salesChart.update();
        
        // Remove loading state
        chartContainer.classList.remove('loading');
        
        // Show success message
        showToast(`Datos actualizados para ${period}`, 'success');
    }, 1000);
}

// Initialize product filter
function initProductFilter() {
    const productFilter = document.getElementById('productFilter');
    const mostSoldProducts = document.getElementById('mostSoldProducts');
    const lowStockProducts = document.getElementById('lowStockProducts');
    
    if (productFilter) {
        productFilter.addEventListener('change', function() {
            const value = this.value;
            
            switch(value) {
                case 'most-sold':
                    mostSoldProducts.style.display = 'block';
                    lowStockProducts.style.display = 'none';
                    break;
                case 'low-stock':
                    mostSoldProducts.style.display = 'none';
                    lowStockProducts.style.display = 'block';
                    break;
                case 'all':
                    mostSoldProducts.style.display = 'block';
                    lowStockProducts.style.display = 'block';
                    break;
            }
        });
    }
}

// Initialize dashboard animations
function initDashboardAnimations() {
    // Add animation classes to elements
    const statCards = document.querySelectorAll('.stat-card');
    const contentCards = document.querySelectorAll('.content-card');
    const miniCards = document.querySelectorAll('.mini-card');
    
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe all cards
    statCards.forEach(card => observer.observe(card));
    contentCards.forEach(card => observer.observe(card));
    miniCards.forEach(card => observer.observe(card));
}

// Refresh dashboard function
function refreshDashboard() {
    showLoading('Actualizando dashboard...');
    
    // Refresh all dashboard data
    updateDashboardStats();
    
    // Update chart with latest 7-day data
    updateChartData('7d');
    
    // Update clients chart
    updateClientsChart();
    
    // Simulate additional API calls
    setTimeout(() => {
        hideLoading();
        showToast('Dashboard actualizado completamente', 'success');
    }, 2000);
}

// Update dashboard stats via AJAX
function updateDashboardStats() {
    // Show updating indicator
    const statsGrid = document.querySelector('.stats-grid');
    statsGrid.classList.add('updating');
    
    // Simulate API call
    setTimeout(() => {
        // Remove updating class
        statsGrid.classList.remove('updating');
    }, 1000);
}

// Update clients chart
function updateClientsChart() {
    if (window.clientsChart) {
        // Simulate new data
        const newRegistered = Math.floor(Math.random() * 20) + 60;
        const newUnregistered = 100 - newRegistered;
        
        window.clientsChart.data.datasets[0].data = [newRegistered, newUnregistered];
        window.clientsChart.update();
        
        // Update stats text
        document.querySelector('.client-stat .stat-label').textContent = 
            `Registrados: ${Math.floor(newRegistered * 0.8)} (${newRegistered}%)`;
        
        document.querySelectorAll('.client-stat .stat-label')[1].textContent = 
            `No Registrados: ${Math.floor(newUnregistered * 0.8)} (${newUnregistered}%)`;
    }
}

// Helper functions
function showLoading(message) {
    // Implement your loading indicator here
    console.log('Loading:', message);
}

function hideLoading() {
    // Implement your loading indicator hide here
    console.log('Loading complete');
}

function showToast(message, type) {
    // Implement your toast notification here
    console.log('Toast:', type, message);
}

// Export functions for global access
window.refreshDashboard = refreshDashboard;
window.updateChartData = updateChartData;