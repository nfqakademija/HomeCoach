var barChartData = {
    //TODO pakeisti i datas kada buvo padaryti svorio irasai 
    labels: ["January", "February", "March", "April", "May", "June", "July"],
    //TODO kiekvienam svorio irasui sukurti atskira dataseto irasa ir kiekvienam priskirti workouta (pagal tai ir uzvadinti "label". skirtingiems labeliams uzdeti skirtingas spalvas
    datasets: [{
        label: 'Vandamo rytmetinis',
        backgroundColor: "rgba(151,187,205,0.5)",
        //TODO reiksme nustatyti pagal svorio vidurki, kuris buvo kai buvo aktyvi sita programa
        data: [5],
        borderColor: 'white',
        borderWidth: 0,
    }, {
        //TODO sita "line" grafika isvis istrinti, jei naudosim ta metoda, kuri aprasiau auksciau
        type: 'line',
        label: 'Svoris (kg)',
        backgroundColor: "rgba(220,220,220,0.5)",
        //TODO sudeti masyva su svorio irasais
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

