<template>
    <div class="bar-chart" v-if="dataLength">
        <canvas :id="id"></canvas>
    </div>
</template>

<script>
    import Chart from 'chart.js';

    export default {
        name: 'BarChart',

        props: ['data', 'id'],

        data: function () {
            let chartData = this.data;

            var maxData = 0;
            var stepSize = 10;
            var dataCount = chartData.labels?.length;

            chartData.datasets?.forEach(dataSet => {
                let maxDataSet = Math.max( ...dataSet.data );

                maxData = maxDataSet > maxData ? maxDataSet : maxData;
            });

            stepSize = Math.ceil(maxData / dataCount);
            
            return {
                chartData,
                dataLength: Object.keys(this.data).length,
                options: {
                    "responsive": true,
                    "legend": {
                        "display": false
                    },
                    "scales": {
                        "xAxes": [
                            {
                                "gridLines": {
                                    "display": false
                                },
                                "ticks": {
                                    "fontColor": '#263238',
                                }
                            }
                        ],
                        "yAxes": [
                            {
                                "gridLines": {
                                    "display": false
                                },
                                "ticks": {
                                    "precision": 0,
                                    "beginAtZero": true,
                                    "stepSize": stepSize,
                                    "fontColor": '#263238',
                                },
                            },
                        ],
                    },
                }
            }
        },

        mounted: function () {
            if (this.dataLength) {
                var ctx = document.getElementById(this.id).getContext('2d');
    
                new Chart(ctx, {
                    type: 'bar',
                    data: this.data,
                    options: this.options,
                });
            }
        }
    }
</script>