<template>
    <div class="line-chart" v-if="showData">
        <canvas :id="id"></canvas>
        <img :id="`img-${id}`" />
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
                chart: null,
                chartData,
                showData: this.data?.datasets && this.data?.labels,
                options: {
                    "responsive": true,
                    "legend": {
                        "display": false
                    },
                    "animation": {
                        "onComplete": this.onComplete
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

                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: this.data,
                    options: this.options,
                });
            }
        },

        methods: {
            onComplete: function () {
                const url = this.chart.toBase64Image();

                $(`img#img-${this.id}`).attr('src', url);
            }
        }
    }
</script>