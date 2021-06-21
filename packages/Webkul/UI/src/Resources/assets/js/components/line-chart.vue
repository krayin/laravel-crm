<template>
    <div class="line-chart" v-if="showData">
        <canvas :id="id"></canvas>
    </div>
</template>

<script>
    import Chart from 'chart.js';

    export default {
        name: 'LineChart',

        props: ['data', 'id'],

        data: function () {
            let chartData = this.data;

            var maxData = 0;
            var stepSize = 10;
            var dataCount = chartData.labels?.length;

            chartData.datasets?.forEach(dataSet => {
                let maxDataSet = Math.max( ...dataSet.data );

                maxData = (maxDataSet > maxData) ? maxDataSet : maxData;
            });

            stepSize = Math.ceil(maxData / dataCount);
            
            return {
                chartData,
                showData: this.data?.datasets && this.data?.labels,
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
                                    "padding": 40,
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
            if (this.showData) {
                var ctx = document.getElementById(this.id).getContext('2d');

                new Chart(ctx, {
                    type: 'line',
                    data: this.data,
                    options: this.options,
                });
            }
        }
    }
</script>