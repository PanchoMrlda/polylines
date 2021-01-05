const MAX_PRICE = 0.185840;
const MIN_PRICE = 0.087344;

function setChartWidth() {
    // Multiply width by 0.99 because desktop screens are smaller than real screen
    let screenWidth = window.innerWidth * 0.99;
    if (screenWidth < 768 && window.matchMedia("(orientation: portrait)").matches) {
        screenWidth = window.innerHeight;
    } else if (screenWidth < 768 && window.matchMedia("(orientation: landscape)").matches) {
        screenWidth = 768;
    }
    return screenWidth;
}

function generateChart(chartId, columnValues1, columnValues2 = [], title = "", label = "") {
    let yGrid;
    let chartDateFormat = (columnValues1[0].length > 1440) ? "%Y-%m-%d %H:%M" : "%H:%M";
    let chartsData = {
        x: "times",
        xFormat: "%Y-%m-%d %H:%M:%S",
        columns: columnValues1,
        type: "bar"
    };
    let screenWidth = setChartWidth();
    if (columnValues1[0].length === 1 && columnValues2[0].length === 1) {
        chartsData.columns = [];
    } else if (columnValues1[0].length !== 1 && columnValues2[0].length === 1) {
        chartsData.columns = columnValues1;
    }

    yGrid = {
        show: true,
        // lines: [
        //     {value: 3.27, text: "", position: "start", class: "grid800"}
        // ]
    };

    return c3.generate({
        bindto: chartId,
        size: {
            height: 480,
            width: screenWidth
        },
        data: chartsData,
        title: {
            text: title
        },
        // color: {
        //     pattern: ["#1f77b4", "#ff7f0e", "#629fca", "#ffa556"]
        // },
        line: {
            show: false
        },
        axis: {
            x: {
                type: "timeseries",
                tick: {
                    format: chartDateFormat, // how the date is displayed
                }
            },
            y: {
                label: {
                    text: label,
                    position: "outer-middle",
                    width: 100
                }
            }
        },
        grid: {
            y: yGrid
        },
        point: {
            show: true
        },
        onrendered: function () {
            let chartLabels = document.querySelectorAll(".c3-axis-y-label");
            Array.prototype.map.call(chartLabels, function (label) {
                label.setAttribute("transform", "rotate(0)");
                label.setAttribute("y", "60");
                label.setAttribute("x", "-40");
            });
        }
    });
}

function displaySensorsData(responseParams) {
    let energyData = responseParams.energyChart;
    let dates = energyData.map(e => {
        return e.date;
    });
    let consumed = energyData.map(e => {
        return Math.round(e.consumed * 100) / 100;
    });
    let produced = energyData.map(e => {
        return Math.round(e.produced * 100) / 100;
    });
    let totalEnergy = consumed.map((e, i) => {
        let total = e + produced[i];
        return Math.round(total * 100) / 100;
    });
    let consumedCost = consumed.map((e, i) => {
        let price = getPrice(dates[0], dates[i]);
        let cost = e * price;
        return Math.round(cost * 100) / 100;
    });
    let producedCost = produced.map((e, i) => {
        let price = getPrice(dates[0], dates[i]);
        let cost = e * price;
        return Math.round(cost * 100) / 100;
    });
    let finalCost = consumedCost.map((e, i) => {
        let cost = e + producedCost[i];
        let finalCost = cost >= 0 ? cost : 0;
        return Math.round(finalCost * 100) / 100;
    });
    let totalEnergyCombined = Math.round(totalEnergy.reduce((a, b) => a + b, 0) * 100) / 100;
    let totalConsumption = Math.round(consumed.reduce((a, b) => a + b, 0) * 100) / 100;
    let totalProduced = Math.round(produced.reduce((a, b) => a + b, 0) * 100) / 100;
    let totalFinalCost = Math.round(finalCost.reduce((a, b) => a + b, 0) * 100) / 100;
    let totalConsumedCost = Math.round(consumedCost.reduce((a, b) => a + b, 0) * 100) / 100;
    let totalProducedCost = Math.round(producedCost.reduce((a, b) => a + b, 0) * 100) / 100;
    document.querySelector("#maxValueLeft").value = totalEnergyCombined + " kW";
    document.querySelector("#maxValueCenter").value = totalConsumption + " kW";
    document.querySelector("#maxValueRight").value = totalProduced + " kW";
    document.querySelector("#minValueLeft").value = totalFinalCost + " €";
    document.querySelector("#minValueCenter").value = totalConsumedCost + " €";
    document.querySelector("#minValueRight").value = totalProducedCost + " €";
    document.querySelector("#date").value = filename;
    finalCost = consumedCost.map((e, i) => {
        let cost = e - producedCost[i];
        let finalCost = cost >= 0 ? cost : 0;
        return Math.round(finalCost * 100) / 100;
    });
    dates.unshift("times");
    consumed.unshift("Consumo");
    produced.unshift("Producido");
    totalEnergy.unshift("Total");
    consumedCost.unshift("Coste Consumo");
    producedCost.unshift("Coste Producido");
    finalCost.unshift("Coste Final");
    let energyChartData = [dates, consumed, produced, totalEnergy];
    let costChartData = [dates, consumedCost, producedCost, finalCost];
    generateChart("#voltageChart", energyChartData, [[], [], []], "Energía", "kW");
    generateChart("#batteryChart", costChartData, [[], [], []], "Coste", "€");
}

function getPrice(initDate, currentDate) {
    let beginningHour = new Date(initDate.slice(0, 10) + " 12:00:00");
    let endingHour = new Date(initDate.slice(0, 10) + " 22:00:00");
    let targetDate = new Date(currentDate);
    return targetDate >= beginningHour && targetDate <= endingHour ? MAX_PRICE : MIN_PRICE;
}

function retrieveSensorData() {
    let today = new Date();
    let date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
    filename = document.querySelector("#date").value || date;
    doRequest("GET", "/edp" + filename + ".json", displaySensorsData);
}

let filename;
retrieveSensorData();