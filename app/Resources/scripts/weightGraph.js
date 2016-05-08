$(function () {
    var obj = JSON.parse(data);
    console.dir(obj);
    var dates = [];
    var weightValues = [];
    //var datasets_arr = [];
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

    //for (var i = 0; i < datasets_arr.length; i++){
    //    console.log('datasets_arr['+i+'] = '+ datasets_arr[i]);
    //    if (datasets_arr[i].length > 1){
    for (var j = 0; j < dataset.length; j++){
        labels.push(dataset[j][0]);
        console.log("labelis: "+ dataset[j][0])
    }
    //}
    //}
    //console.log('datasets_arr[0][0] = '+ datasets_arr[0][0]);

    // console.log("dates = " + dates);
    // console.log("weightValues = " + weightValues);
    // console.dir("obj_arr = " + obj_arr);
    //console.dir("datasets_arr = " + datasets_arr);


    // obj_arr.sort(function(a, b) {
    //     a = a[1];
    //     b = b[1];
    //
    //     return a < b ? -1 : (a > b ? 1 : 0);
    // });
    //
    // for (var i = 0; i < obj_arr.length; i++) {
    //     var key = obj_arr[i][0];
    //     var value = obj_arr[i][1];
    //
    //
    // }

    var colors = ['white', 'green', 'red', 'blue'];

    //console.log("datasets_arr[1][1][1] =" + datasets_arr[1][1][1]);

    function rotateColors() {
        var first_color = colors.shift();
        colors.push(first_color);
        return "\""+colors[0]+"\"";
    }

    var datasets = [];

    var datasets = [];

    for (var i = 0; i < dataset.length; i++){
        datasets.push(dataset[i][1]);
    }
    function loadDatasets() {
        //gali tekti perdaryti kad ne tik datasetus loadintu bet ir visa barChartData
        //last_label loope updeitinsi pamates kad weight susideda is raidziu (vadinasi ten workouto pavadinimas) ir pagal tai uzdesi labeli.
        //taip pat padaryti nextColor() funkcija kuri kai keiciasi keiciasi labelis pakeistu ir spalva
        var last_label = '';
        var last_color = '';
        for (var i = 0; i < dataset.length; i++){
            datasets.push({
                label: "svoris",
                backgroundColor: "rgba(151,187,205,0.5)",
                //data: [80],
                // cia reikia grazinti masyva
                data: dataset[i][1],
                borderColor: rotateColors(),
                borderWidth: 0,
            });
        }
        return datasets;
    }

    //var barChartData = {
    //    //TODO pakeisti i datas kada buvo padaryti svorio irasai
    //    labels: labels,
    //    //TODO kiekvienam svorio irasui sukurti atskira dataseto irasa ir kiekvienam priskirti workouta (pagal tai ir uzvadinti "label". skirtingiems labeliams uzdeti skirtingas spalvas
    //    datasets:
    //        //loadDatasets()
    //    {
    //        label: [dataset[0][0]],
    //        backgroundColor: ["rgba(151,187,205,0.5)"],
    //        data: datasets,
    //        // cia reikia grazinti masyva
    //        // data: datasets_arr[i+1][j][1],
    //        borderColor: ['green'],
    //        borderWidth: [0],
    //    }
    //
    //
    //};

    var barCharData = {
        labels: labels,
        datasets: [
            {
                label: "My First dataset",
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
                data: datasets,
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

