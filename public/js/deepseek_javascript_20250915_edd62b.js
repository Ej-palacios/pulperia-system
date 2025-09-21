document.addEventListener('DOMContentLoaded', function() {
    // Initialize sales chart
    initSalesChart();
    
    // Initialize chart period controls
    initChartControls();
    
    // Initialize dashboard animations
    initDashboardAnimations();
    
    // Initialize real-time updates (if needed)
    initRealTimeUpdates();
});

// Initialize sales chart
function initSalesChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Gradient for chart area
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(0, 120, 212, 0.3)');
    gradient.addColorStop(1, 'rgba(0, 120, 212, 0.05)');
    
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Ventas',
                data: [1200, 1900, 3000, 5000, 2000, 3000, 4500],
                borderColor: '#0078d4',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0078d4',
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
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1f2937',
                    bodyColor: '#374151',
                    borderColor: '#e5e7eb',
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
                        display: false
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
        
        switch(period) {
            case '7d':
                newData = [1200, 1900, 3000, 5000, 2000, 3000, 4500];
                window.salesChart.data.labels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                break;
            case '30d':
                newData = Array.from({length: 30}, () => Math.floor(Math.random() * 5000) + 1000);
                window.salesChart.data.labels = Array.from({length: 30}, (_, i) => (i + 1).toString());
                break;
            case '90d':
                newData = Array.from({length: 12}, () => Math.floor(Math.random() * 15000) + 5000);
                window.salesChart.data.labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                break;
        }
        
        window.salesChart.data.datasets[0].data = newData;
        window.salesChart.update();
        
        // Remove loading state
        chartContainer.classList.remove('loading');
        
        // Show success message
        PulperiaUtils.showToast(`Datos actualizados para ${period}`, 'success');
    }, 1000);
}

// Initialize dashboard animations
function initDashboardAnimations() {
    // Add animation classes to elements
    const statCards = document.querySelectorAll('.stat-card');
    const contentCards = document.querySelectorAll('.content-card');
    
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
}

// Initialize real-time updates
function initRealTimeUpdates() {
    // Check if we should enable real-time updates
    const enableRealTime = document.body.getAttribute('data-real-time') === 'true';
    
    if (enableRealTime) {
        // Set up periodic updates (every 60 seconds)
        setInterval(() => {
            updateDashboardStats();
        }, 60000);
    }
}

// Update dashboard stats via AJAX
function updateDashboardStats() {
    // Show loading indicator
    const statsGrid = document.querySelector('.stats-grid');
    statsGrid.classList.add('updating');
    
    // Make AJAX request to get updated stats
    fetch('/api/dashboard/stats', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update stat values with animation
        updateStatValue('.stat-card:nth-child(1) .stat-value', data.ventasHoy);
        updateStatValue('.stat-card:nth-child(2) .stat-value', data.totalCreditos);
        updateStatValue('.stat-card:nth-child(3) .stat-value', data.stockBajoCount);
        updateStatValue('.stat-card:nth-child(4) .stat-value', data.valorInventario);
        
        // Remove updating class
        statsGrid.classList.remove('updating');
        
        // Show success message
        PulperiaUtils.showToast('Datos actualizados', 'success');
    })
    .catch(error => {
        console.error('Error updating dashboard stats:', error);
        statsGrid.classList.remove('updating');
        PulperiaUtils.showToast('Error al actualizar datos', 'error');
    });
}

// Animate stat value updates
function updateStatValue(selector, newValue) {
    const element = document.querySelector(selector);
    if (!element) return;
    
    const currentValue = parseFloat(element.textContent.replace(/[^0-9.-]+/g, ""));
    const isCurrency = element.classList.contains('currency');
    
    // Format the value
    const formattedValue = isCurrency ? 
        new Intl.NumberFormat('es-NI', { style: 'currency', currency: 'NIO' }).format(newValue) :
        newValue.toLocaleString();
    
    // Add animation class
    element.classList.add('updating');
    
    // Update value after short delay
    setTimeout(() => {
        element.textContent = formattedValue;
        element.classList.remove('updating');
    }, 300);
}

// Refresh dashboard function
function refreshDashboard() {
    PulperiaUtils.showLoading('Actualizando dashboard...');
    
    // Refresh all dashboard data
    updateDashboardStats();
    
    // Update chart with latest 7-day data
    updateChartData('7d');
    
    // Simulate additional API calls
    setTimeout(() => {
        PulperiaUtils.hideLoading();
        PulperiaUtils.showToast('Dashboard actualizado completamente', 'success');
    }, 2000);
}

// Export functions for global access
window.refreshDashboard = refreshDashboard;
window.updateChartData = updateChartData;