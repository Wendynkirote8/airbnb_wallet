// dashboard.js

document.addEventListener("DOMContentLoaded", function() {
    // Initialize the Transaction Trends chart using Chart.js
    const ctx = document.getElementById('transactionChart').getContext('2d');
    const transactionChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June'], // Example labels
        datasets: [{
          label: 'Transactions',
          data: [12, 19, 3, 5, 2, 3], // Example data points
          backgroundColor: 'rgba(0, 123, 255, 0.2)',
          borderColor: 'rgba(0, 123, 255, 1)',
          borderWidth: 1,
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
  