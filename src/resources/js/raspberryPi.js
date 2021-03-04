const ENERGY_SYMBOL = " kW";
const CURRENCY_SYMBOL = " €";
const CONTRACTED_POWER = 6.928;
const MAX_PRICE = 0.208472;
const MIN_PRICE = 0.110386;

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
    let chartDateFormat = "%Y-%m-%d %H:%M";
    if (columnValues1[0].length <= 25) {
        chartDateFormat = "%H:%M";
        columnValues1[0] = columnValues1[0].map((e, i) => {
            if (i > 0) {
                return e.slice(11, 16);
            } else {
                return e;
            }
        });
    }
    let chartsData = {
        x: "times",
        xFormat: chartDateFormat,
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
    let dates = responseParams.map(e => {
        return e.date;
    });
    let consumed = responseParams.map(e => {
        return Math.round(parseFloat(e.consumed.replace(',', '.')) * 100) / 100;
    });
    let produced = responseParams.map(e => {
        return Math.round(parseFloat(e.produced.replace(',', '.')) * 100) / 100;
    });
    let totalEnergy = consumed.map((e, i) => {
        let total = e + produced[i];
        return Math.round(total * 100) / 100;
    });
    let consumedCost = consumed.map((e, i) => {
        let price = getPrice(dates[i]);
        let cost = e * price;
        return Math.round(cost * 100) / 100;
    });
    let producedCost = produced.map((e, i) => {
        let price = getPrice(dates[i]);
        let cost = e * price;
        return Math.round(cost * 100) / 100;
    });
    let finalCost = consumedCost.map((e, i) => {
        let cost = e - producedCost[i];
        let finalCost = cost >= 0 ? cost : 0;
        return Math.round(finalCost * 100) / 100;
    });
    let totalEnergyCombined = Math.round(totalEnergy.reduce((a, b) => a + b, 0) * 100) / 100;
    let totalConsumption = Math.round(consumed.reduce((a, b) => a + b, 0) * 100) / 100;
    let totalProduced = Math.round(produced.reduce((a, b) => a + b, 0) * 100) / 100;
    let totalFinalCost = Math.round(finalCost.reduce((a, b) => a + b, 0) * 100) / 100;
    let totalConsumedCost = Math.round(consumedCost.reduce((a, b) => a + b, 0) * 100) / 100;
    let totalProducedCost = Math.round(producedCost.reduce((a, b) => a + b, 0) * 100) / 100;
    document.querySelector("#maxValueLeft").value = totalEnergyCombined + ENERGY_SYMBOL;
    document.querySelector("#maxValueCenter").value = totalConsumption + ENERGY_SYMBOL;
    document.querySelector("#maxValueRight").value = totalProduced + ENERGY_SYMBOL;
    document.querySelector("#minValueLeft").value = totalFinalCost + CURRENCY_SYMBOL;
    document.querySelector("#minValueCenter").value = totalConsumedCost + CURRENCY_SYMBOL;
    document.querySelector("#minValueRight").value = totalProducedCost + CURRENCY_SYMBOL;
    document.querySelector("#powerContracted").value = CONTRACTED_POWER + ENERGY_SYMBOL;
    document.querySelector("#powerCost").value = calculatePowerCost(dates);
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

function getPrice(currentDate) {
    let beginningHour = new Date(currentDate.slice(0, 10) + " 12:00:00");
    let endingHour = new Date(currentDate.slice(0, 10) + " 22:00:00");
    let targetDate = new Date(currentDate);
    return targetDate >= beginningHour && targetDate <= endingHour ? MAX_PRICE : MIN_PRICE;
}

function calculatePowerCost(dates) {
    let numDays = Math.floor(dates.length / 24) - 1;
    let cost = Math.round(CONTRACTED_POWER * numDays * CONTRACTED_POWER * 100) / 100;
    return cost + CURRENCY_SYMBOL;
}

function retrieveSensorData() {
    let startDateElem = document.querySelector("[name=startDate]");
    let endDateElem = document.querySelector("[name=endDate]");
    let requestParams = {
        startDate: startDateElem.value
    };
    if (endDateElem.value !== undefined && startDateElem.value <= endDateElem.value) {
        requestParams.endDate = endDateElem.value;
    } else {
        requestParams.endDate = startDateElem.value;
    }
    let url = "/solar-panels";
    doRequest("GET", url, displaySensorsData, requestParams);
}
