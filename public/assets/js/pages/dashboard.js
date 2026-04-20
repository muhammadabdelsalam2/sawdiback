document.addEventListener('DOMContentLoaded', function () {
    // Production Performance Chart
    const productionCanvas = document.getElementById('productionChart');
    if (productionCanvas) {
        const productionCtx = productionCanvas.getContext('2d');
        const labels = (window.dashboardData && window.dashboardData.production && window.dashboardData.production.labels.length > 0)
            ? window.dashboardData.production.labels
            : ['1', '2', '3', '4', '5', '6'];
        const data = (window.dashboardData && window.dashboardData.production && window.dashboardData.production.data.length > 0)
            ? window.dashboardData.production.data
            : [20, 15, 25, 40, 50, 20];

        new Chart(productionCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Milk Production',
                        data: data,
                        borderColor: '#168EFF',
                        backgroundColor: 'rgba(22, 142, 255, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#168EFF'
                    },
                    {
                        label: 'Target',
                        data: data.map(v => v * 0.9), // Generate a target based on actual data
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
                        beginAtZero: true,
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
        const labels = (window.dashboardData && window.dashboardData.herd && window.dashboardData.herd.labels.length > 0)
            ? window.dashboardData.herd.labels
            : ['Lactating', 'Dry', 'Pregnant'];
        const data = (window.dashboardData && window.dashboardData.herd && window.dashboardData.herd.data.length > 0)
            ? window.dashboardData.herd.data
            : [50, 15, 35];

        new Chart(herdCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#168EFF', '#DF1278', '#8B47D7', '#30914C', '#C87B00'],
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
        // Auto-open if child is active
        if (dropdown.querySelector('.dropdown-item.active')) {
            dropdown.classList.add('open');
        }

        const trigger = dropdown.querySelector('.has-dropdown');
        if (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                dropdown.classList.toggle('open');
            });
        }
    });
});
const input = $('#globalSearch');
const dropdown = $('#searchDropdown');

let timeout = null;
let lastQuery = '';
let hasOpenedOnce = false;

// Default modules
const defaultModules = [
    {
        name: 'Animals',
        url: window.appConfig.animalsUrl,
        icon: 'fa fa-paw'
    },
    {
        name: 'Orders',
        url: window.appConfig.ordersUrl,
        icon: 'fa fa-shopping-cart'
    },
    {
        name: 'Products',
        url: window.appConfig.productsUrl,
        icon: 'fa fa-box'
    }
];

// Render modules
function renderModules(modules) {

    let html = '';

    modules.forEach(m => {
        html += `
            <div class="search-item" onclick="window.location='${m.url}'">
                <i class="${m.icon}"></i> ${m.name}
            </div>
        `;
    });

    dropdown.html(html);
}

// Focus event
input.on('focus', function () {

    dropdown.removeClass('d-none').addClass('show');

    renderModules(defaultModules);

    if (!hasOpenedOnce) {
        hasOpenedOnce = true;
        console.log('Search opened first time');
    }
});

// Outside click
$(document).on('click', function (e) {

    const container = $('.search-container');

    if (!container.has(e.target).length) {
        dropdown.addClass('d-none').removeClass('show');
    }
});

// Input event
input.on('input', function () {

    clearTimeout(timeout);

    let q = $(this).val().trim();

    if (!q) {
        renderModules(defaultModules);
        return;
    }

    if (q === lastQuery) return;
    lastQuery = q;

    timeout = setTimeout(() => {
        search(q);
    }, 500);
});

// Search function (Axios)
function search(q) {

    dropdown.html(`
        <div class="search-item ai-loader">
            <div class="ai-spinner"></div>
            AI Searching...
        </div>
    `);

    dropdown.removeClass('d-none').addClass('show');

    axios.get(window.appConfig.searchUrl, {
        params: { q: q },
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + window.appConfig.token
        }
    })
        .then(res => {

            const data = res.data;

            dropdown.html('');

            if (!data || data.length === 0) {
                dropdown.html(`<div class="search-item">No results found</div>`);
                return;
            }

            let html = '';

            data.forEach(item => {
                html += `
                <div class="search-item" onclick="window.location='${item.url}'">
                    <strong>${item.type}</strong> - ${item.name}
                </div>
            `;
            });

            dropdown.html(html);
        })
        .catch(err => {

            console.error(err);

            dropdown.html(`
            <div class="search-item text-danger">
                Something went wrong
            </div>
        `);
        });
}