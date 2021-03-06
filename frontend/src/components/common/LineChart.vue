<template>
    <VContainer class="chart-container">
        <Spinner
            v-if="isFetching"
        />
        <VCardTitle
            v-if="!this.data.length"
            primary-title
            class="grey--text"
        >
            There is no data to display!
        </VCardTitle>
        <GChart
            type="LineChart"
            v-else
            :data="chartData"
            :options="chartOptions"
            :units="units"
        />
    </VContainer>
</template>
<script>
    import { GChart } from 'vue-google-charts';
    import Spinner from '../utilites/Spinner';
    export default {
        components: {
            GChart,
            Spinner,
        },
        props: {
            data: {
                type: Array,
                required: true
            },
            isFetching: {
                type: Boolean,
                required: true,
            },
            units: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                chartOptions: {
                    chartArea: {
                        width: '93%',
                        height: '75%'
                    },
                    tooltip: {
                        isHtml: true,
                        ignoreBounds: true,
                        trigger: 'selection'
                    },
                    curveType: 'function',
                    legend: {
                        position: 'none'
                    },
                    lineWidth: 1,
                    pointSize: 10,
                    pointShape: { type: 'circle', fill: 'none' },
                    series: [
                        { targetAxisIndex: 0, visibleInLegend: false, pointSize: 0, lineWidth: 0, pointsVisible: false},
                        { targetAxisIndex: 1, color: '#3C57DE', pointsVisible: true},
                        {color: 'red'}
                    ],
                    vAxes: {
                        0: { textPosition: 'none' },
                        1: {}
                    },
                    hAxis: {
                        textStyle: {
                            color: '#b8bec3',
                            fontName: 'Gilroy',
                            fontSize: 12,
                            lineHeight: 14,
                            letterSpacing: 0.533333,
                        },
                    },
                    crosshair:
                        {
                            color: '#ededed',
                            trigger: 'focus',
                            orientation: 'vertical'
                        },
                    vAxis: {
                        viewWindow: {min: 0},
                        format: 'short',
                        count: 3,
                        minValue: 0,
                        baselineColor: 'none',
                        minorGridlines: {
                            count: 0
                        },
                        textStyle: {
                            color: '#b8bec3',
                            fontName: 'Gilroy',
                            fontSize: 12,
                            lineHeight: 14,
                            letterSpacing: 0.533333,
                        },
                        gridlines: {
                            color: '#edf2fa'
                        }
                    }
                },
            };
        },
        computed: {
            chartData(){
                if (!this.data.length) {
                    return [];
                }
                const tooltipObj = {'type': 'string', 'role': 'tooltip', 'p': {'html': true}};
                const pointStyle = 'point { stroke-color: #3C57DE; size: 5; shape-type: circle; fill-color: #FFFFFF; }';
                let tmpData = this.data.map( element  => {
                    return  [element.date, element.value, element.value, pointStyle, this.tooltip(element)];
                });
                tmpData.unshift([{type: 'string', name: 'date'}, '', 'yValue', {'type': 'string', 'role': 'style'}, tooltipObj]);
                return tmpData;
            }
        },
        methods: {
            tooltip(element) {
                return `<div class='custom-google-line-chart-tooltip white--text'>
                    <div class='tooltip-first primary lighten-1'>
                        ${element.value}${this.units}
                    </div>
                    <div class='tooltip-second'>
                        ${element.date}
                    </div>
                </div>`;
            },
        }
    };
</script>
<style lang="scss" scoped>
    .chart-container {
        height: 224px;
    }
    .v-card__title {
        display: flex;
        height: 100%;
        justify-content: center;
        align-content: center;
    }
    ::v-deep svg path {
        fill: none;
    }
    ::v-deep div.google-visualization-tooltip {
        margin-left: -100px;
        font-family: Gilroy;
        font-size: 18px;
        line-height: 21px;
        letter-spacing: 0.533333px;
        border: 0;
        border-radius: 6px;
        .custom-google-line-chart-tooltip {
            box-sizing: border-box;
            border-radius: 6px;
            min-width: 176px;
            height: 48px;
            display: flex;
            align-items: center;
            background: #3C57DE;
            .tooltip-first {
                flex: 1;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 6px 0 0 6px;
            }
            .tooltip-second {
                text-align: center;
                padding: 0 8px;
                flex: 1;
            }
        }
    }
</style>