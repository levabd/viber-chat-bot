<template>
    <!-- resources/assets/js/components -->
    <div>
        <canvas id="monthStatisticChart"></canvas>
    </div>
</template>

<script>
    import {Chart} from 'Chart.js';
    export default {
        props: {
            labels: String,
            dataProp: String
        },
        methods: {
            renderChart() {
                new Chart(document.getElementById('monthStatisticChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: this.labels,
                        datasets: [{
                            label: 'На текущий месяц',
                            data: this.dataProp,
                            backgroundColor: [
                                '#2ecc71',
                                '#e74c3c',
                                '#8e44ad',
                                '#d35400',
                                '#16a085'
                            ]
                        }]
                    },
                    options: {}
                });
            }
        },
        mounted() {
            this.renderChart();
        }
    }
</script>