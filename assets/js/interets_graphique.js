const rawData = [
    { month: '2024-01', interest: 150 },
    { month: '2024-02', interest: 180 },
    { month: '2024-03', interest: 200 },
    { month: '2024-04', interest: 220 },
    { month: '2024-05', interest: 250 },
    { month: '2024-06', interest: 270 },
    { month: '2024-07', interest: 300 },
];

const tableBody = document.getElementById('table-body');
const startDateInput = document.getElementById('start-date');
const endDateInput = document.getElementById('end-date');
const filterBtn = document.getElementById('filter-btn');
const ctx = document.getElementById('interests-chart').getContext('2d');

let chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'Intérêts (€)',
            data: [],
            backgroundColor: 'rgba(30, 58, 138, 0.7)',
            borderColor: 'rgba(30, 58, 138, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                mode: 'index',
                intersect: false,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(229, 231, 235, 0.5)'
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

function formatDate(dateStr) {
    const [year, month] = dateStr.split('-');
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                       'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    return `${monthNames[parseInt(month) - 1]} ${year}`;
}

function updateDisplay(data) {
    tableBody.innerHTML = '';
    const labels = [];
    const interestData = [];

    data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${formatDate(item.month)}</td>
            <td>${item.interest.toFixed(2)} €</td>
        `;
        tableBody.appendChild(row);

        labels.push(formatDate(item.month));
        interestData.push(item.interest);
    });

    chart.data.labels = labels;
    chart.data.datasets[0].data = interestData;
    chart.update();
}

function filterData() {
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    if (!startDate || !endDate) {
        alert('Veuillez sélectionner une plage de dates valide.');
        return;
    }

    const filteredData = rawData.filter(item => {
        return item.month >= startDate && item.month <= endDate;
    });

    updateDisplay(filteredData);
}

filterBtn.addEventListener('click', filterData);

document.addEventListener('DOMContentLoaded', () => {
    // Définir les dates par défaut (6 derniers mois)
    const today = new Date();
    const sixMonthsAgo = new Date();
    sixMonthsAgo.setMonth(today.getMonth() - 6);
    
    endDateInput.value = today.toISOString().slice(0, 7);
    startDateInput.value = sixMonthsAgo.toISOString().slice(0, 7);
    
    updateDisplay(rawData);
});