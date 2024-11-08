<div style="widh: 300px; height: 300px;">
<canvas id="myChart"></canvas>
</div>

<script src="./node_modules/chart.js/dist/chart.umd.js"></script>

<script>
const ctx = document.getElementById('myChart');

new Chart(ctx, {
	type: 'doughnut',
	data: {
		labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
		datasets: [{
			//label: '# of Votes',
			data: [12, 19, 3, 5, 2, 3],
			borderWidth: 1
		}]
	},
	options: {
/*		scales: {
			y: {
				beginAtZero: true
			}
		}*/
	}
});
</script>
