var barChartData = {
    labels: ["January", "February", "March", "April", "May", "June", "July"],
    datasets: [{
        label: 'Dataset 1',
        backgroundColor: "rgba(151,187,205,0.5)",
        data: [1, 4, 5, 2, 10],
        borderColor: 'white',
        borderWidth: 2
    }, {
        label: 'Dataset 2',
        backgroundColor: "rgba(151,187,205,0.5)",
        data: [1, 4, 5, 2, 10],
        borderColor: 'white',
        borderWidth: 2
    }, {
        type: 'line',
        label: 'Dataset 3',
        backgroundColor: "rgba(220,220,220,0.5)",
        data: [1, 4, 5, 2, 10],
    }, ]

};

window.onload = function() {
    var ctx = document.getElementById("myChart");
    window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
            responsive: true,
        }
    });
};

