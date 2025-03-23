// dashboard_new.js
document.addEventListener("DOMContentLoaded", function() {
  const ctx = document.getElementById('transactionChart').getContext('2d');
  const transactionChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], // Example months
      datasets: [{
        label: 'Transactions',
        data: [12, 19, 5, 2, 10, 6], // Example data points
        backgroundColor: 'rgba(52, 152, 219, 0.2)',
        borderColor: 'rgba(52, 152, 219, 1)',
        borderWidth: 2,
        fill: true
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
});
