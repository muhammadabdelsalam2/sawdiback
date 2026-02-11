document.addEventListener('DOMContentLoaded', function () {
    // Production Performance Chart
    const productionCanvas = document.getElementById('productionChart');
    if (productionCanvas) {
        const productionCtx = productionCanvas.getContext('2d');
        new Chart(productionCtx, {
            type: 'line',
            data: {
                labels: ['1', '2', '3', '4', '5', '6'],
                datasets: [
                    {
                        label: 'Milk Production',
                        data: [20, 15, 25, 40, 50, 20],
                        borderColor: '#168EFF',
                        backgroundColor: 'rgba(22, 142, 255, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#168EFF'
                    },
                    {
                        label: 'Target',
                        data: [-20, -10, -5, 10, 15, 25],
                        borderColor: '#DF1278',
                        backgroundColor: 'rgba(223, 18, 120, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#DF1278'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        min: -60,
                        max: 60,
                        ticks: {
                            stepSize: 20
                        },
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Herd Composition Status Chart (Donut)
    const herdCanvas = document.getElementById('herdChart');
    if (herdCanvas) {
        const herdCtx = herdCanvas.getContext('2d');
        new Chart(herdCtx, {
            type: 'doughnut',
            data: {
                labels: ['Lactating', 'Dry', 'Pregnant'],
                datasets: [{
                    data: [50, 15, 35],
                    backgroundColor: ['#168EFF', '#DF1278', '#8B47D7'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20
                        }
                    }
                }
            }
        });
    }

    // Profitability by Cost Center (Grouped Bar - Starts from 0)
    const profitCanvas = document.getElementById('profitabilityChart');
    if (profitCanvas) {
        const profitCtx = profitCanvas.getContext('2d');
        new Chart(profitCtx, {
            type: 'bar',
            data: {
                labels: ['Milk Production', 'Livestock Sales', 'Crops Trading', 'Poltery'],
                datasets: [
                    {
                        label: 'Revenue',
                        data: [40, 50, 45, 55],
                        backgroundColor: '#30914C',
                        borderRadius: 4
                    },
                    {
                        label: 'Cost',
                        data: [20, 30, 25, 35], // Positive values for grouping beside revenue
                        backgroundColor: '#C87B00',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        stacked: true,
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 20
                        }
                    },
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Efficiency Mixed Chart (Animal Details)
    const efficiencyCanvas = document.getElementById('efficiencyChart');
    if (efficiencyCanvas) {
        const efficiencyCtx = efficiencyCanvas.getContext('2d');
        new Chart(efficiencyCtx, {
            data: {
                labels: Array.from({ length: 15 }, (_, i) => `Jan ${i * 2 + 1}`),
                datasets: [
                    {
                        type: 'line',
                        label: 'Daily Milk Yield (L)',
                        data: [22, 23, 24, 23.5, 25, 24.5, 26, 25.5, 24.8, 25.8, 25.2, 26.5, 25.8, 25.2, 25],
                        borderColor: '#005C4B',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        pointBackgroundColor: '#005C4B',
                        pointRadius: 4,
                        tension: 0.3,
                        yAxisID: 'y'
                    },
                    {
                        type: 'bar',
                        label: 'Feed Consumed (KG)',
                        data: [15, 17, 18, 17.5, 18.5, 18, 19, 18.2, 17.5, 18.5, 17.2, 19, 18.5, 17.5, 17],
                        backgroundColor: (context) => {
                            const index = context.dataIndex;
                            return index === 14 ? '#30914C' : '#758CA4';
                        },
                        borderRadius: 4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        max: 32,
                        title: {
                            display: true,
                            text: 'Milk Yield (L)',
                            color: '#556B82'
                        },
                        grid: {
                            color: '#F2F2F2'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        max: 16,
                        title: {
                            display: true,
                            text: 'Feed (KG)',
                            color: '#556B82'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Sidebar Toggle
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('sidebar-collapsed');
            }

            // Dispatch a window resize event to force charts to redraw if necessary
            window.dispatchEvent(new Event('resize'));
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function (e) {
        if (window.innerWidth <= 768 && sidebar.classList.contains('show')) {
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        }
    });

    // Sidebar Dropdown Toggle
    const dropdowns = document.querySelectorAll('.nav-dropdown');
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('.has-dropdown');
        if (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                dropdown.classList.toggle('open');
                const chevron = this.querySelector('.chevron');
                if (chevron) {
                    chevron.classList.toggle('rotate');
                }
            });
        }
    });
});
