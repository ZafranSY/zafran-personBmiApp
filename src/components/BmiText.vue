<template>
    <div>
        <h3>Statistics / Analysis</h3>

        <div v-if="!personList.length" id="bmiStats">
            No data yet.
        </div>

        <div v-else id="bmiStats">
            <div v-for="(count, category) in categories" :key="category">
                <strong>{{ capitalize(category) }}</strong>: {{ count }} ({{ getPercent(count) }}%)
                <div class="bar-container">
                    <div class="bar" :class="category" :style="{ width: getPercent(count) + '%' }">
                        {{ getPercent(count) }}%
                    </div>
                </div>
            </div>
        </div>

        <canvas id="bmiChart" width="400" height="300" ref="chartCanvas" class="chart-canvas"></canvas>
    </div>
</template>

<script>
import { ref, watch, onMounted } from 'vue';
import Chart from 'chart.js/auto';

export default {
    name: 'BmiStatsAnalysis',
    props: {
        personList: {
            type: Array,
            required: true
        }
    },
    setup(props) {
        const categories = ref({
            underweight: 0,
            normal: 0,
            overweight: 0,
            obese: 0
        });

        const chartCanvas = ref(null);
        let bmiChart = null;

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function calculateCategories() {
            const counts = {
                underweight: 0,
                normal: 0,
                overweight: 0,
                obese: 0
            };
            props.personList.forEach(p => {
                const bmi = parseFloat(p.bmi);
                if (bmi < 18.5) counts.underweight++;
                else if (bmi < 24.9) counts.normal++;
                else if (bmi < 29.9) counts.overweight++;
                else counts.obese++;
            });
            categories.value = counts;
        }

        function getPercent(count) {
            if (props.personList.length === 0) return 0;
            return ((count / props.personList.length) * 100).toFixed(1);
        }

        function drawChart() {
            if (!chartCanvas.value) return;

            const data = {
                labels: Object.keys(categories.value).map(capitalize),
                datasets: [{
                    data: Object.values(categories.value),
                    backgroundColor: ['#2196f3', '#4caf50', '#ff9800', '#f44336']
                }]
            };

            if (bmiChart) bmiChart.destroy();

            bmiChart = new Chart(chartCanvas.value.getContext('2d'), {
                type: 'pie',
                data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        watch(() => props.personList, () => {
            calculateCategories();
            drawChart();
        }, { immediate: true });

        onMounted(() => {
            calculateCategories();
            drawChart();
        });

        return {
            categories,
            capitalize,
            getPercent,
            chartCanvas
        };
    }
};
</script>