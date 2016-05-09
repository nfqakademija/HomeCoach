$(function () {
    var obj = JSON.parse(data);
    var dataset = [];
    var is_workout = false;
    //this will be used for looping through json fields
    for (var key in obj) {
        if (obj.hasOwnProperty(key)) {
            if (is_workout) {
                if (/^[0-9]{2,3}$/.test(obj[key])){
                    dataset.push([dataset.pop()[1] + " --- " + key , obj[key]]);
                    is_workout = false;
                } else {
                    dataset.push([key, obj[key]]);
                    is_workout = true;
                }
            } else {
                if (/^[0-9]{2,3}$/.test(obj[key])){
                    dataset.push([key, obj[key]]);
                    is_workout = false;
                } else {
                    dataset.push([key, obj[key]]);
                    is_workout = true;
                }

            }
        }
    }

    var labels = []

    for (var j = 0; j < dataset.length; j++){
        labels.push(dataset[j][0]);
        console.log("labelis: "+ dataset[j][0])
    }
    var weights_arr = [];

    for (var i = 0; i < dataset.length; i++){
        weights_arr.push(dataset[i][1]);
    }

    var barCharData = {
        labels: labels,
        datasets: [
            {
                label: "Svoris",
                fill: false,
                lineTension: 0.1,
                backgroundColor: "rgba(75,192,192,0.4)",
                borderColor: "rgba(75,192,192,1)",
                borderCapStyle: 'butt',
                borderDash: [],
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                pointBorderColor: "rgba(75,192,192,1)",
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(75,192,192,1)",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointHoverBorderWidth: 2,
                pointRadius: 1,
                pointHitRadius: 10,
                data: weights_arr,
            }
        ]
    };

    window.onload = function () {
        var ctx = document.getElementById("myChart");
        window.myBar = new Chart(ctx, {
            type: 'line',
            data: barCharData,
            options: {
                responsive: true,
            }
        });
    };
})

