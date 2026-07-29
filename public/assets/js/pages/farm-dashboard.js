window.FarmDashboardCharts = (function () {
    function hasUsefulData(values, allowSingleCategory) {
        const numericValues = (values || []).map(Number).filter(Number.isFinite);
        const positiveValues = numericValues.filter((value) => value > 0);

        if (positiveValues.length === 0) {
            return false;
        }

        return allowSingleCategory || positiveValues.length > 1;
    }

    function renderEmptyState(canvas, message) {
        const body = canvas.closest('.chart-card-body');
        if (!body) return;

        canvas.remove();
        body.classList.add('chart-empty-body');
        body.innerHTML = `
            <div class="chart-empty-state">
                <i class="bi bi-bar-chart-line"></i>
                <strong>${message}</strong>
            </div>
        `;
    }

    function makeChart(config) {
        if (typeof Chart === 'undefined') return;

        const canvas = document.getElementById(config.id);
        if (!canvas) return;

        const labels = config.labels || [];
        const values = config.values || [];
        const isDoughnut = config.type === 'doughnut';

        if (!labels.length || !hasUsefulData(values, !isDoughnut)) {
            renderEmptyState(canvas, config.emptyText);
            return;
        }

        const lineBarOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(15, 23, 42, 0.06)' },
                },
            },
        };

        const doughnutOptions = {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 16,
                    },
                },
            },
        };

        new Chart(canvas, {
            type: config.type,
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: config.color,
                    borderColor: config.color,
                    borderWidth: 2,
                    tension: 0.35,
                    fill: config.type === 'line',
                }],
            },
            options: isDoughnut ? doughnutOptions : lineBarOptions,
        });
    }

    function init(data, text) {
        makeChart({
            id: 'farmMilkChart',
            type: 'line',
            labels: data.milkLabels || [],
            values: data.milkValues || [],
            color: 'rgba(34, 197, 94, .72)',
            emptyText: text.noDataYet,
        });
        makeChart({
            id: 'farmOrdersChart',
            type: 'bar',
            labels: data.orderLabels || [],
            values: data.orderValues || [],
            color: 'rgba(59, 130, 246, .72)',
            emptyText: text.noDataYet,
        });
        makeChart({
            id: 'farmAnimalsChart',
            type: 'doughnut',
            labels: data.animalLabels || [],
            values: data.animalValues || [],
            color: ['#22c55e', '#f59e0b', '#ef4444', '#3b82f6'],
            emptyText: text.noDistributionYet,
        });
        makeChart({
            id: 'farmFinanceChart',
            type: 'doughnut',
            labels: data.financeLabels || [],
            values: data.financeValues || [],
            color: ['#22c55e', '#ef4444'],
            emptyText: text.noDistributionYet,
        });
    }

    return { init };
})();
