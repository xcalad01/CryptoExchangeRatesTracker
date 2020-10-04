<!--
 * Exchange_dashboard.vue
 *
 * Exchange Dashboard
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 -->

<template>
  <div>
    <div class="button_row">
    </div>
    <realtime_charts></realtime_charts>

      <ohlc_chart></ohlc_chart>
      <value_chart></value_chart>
  </div>
</template>
<script>
  import OhlcChart from './Dashboard/OhlcChart';
  import ValueChart from './Dashboard/ValueChart';
  import RealtimeCharts from './Dashboard/RealtimeCharts';
  import QuickStats from './Dashboard/QuickStats'

  export default {
    components: {
      ohlc_chart: OhlcChart,
      value_chart: ValueChart,
      realtime_charts: RealtimeCharts,
      quick_stats: QuickStats
    },
    data() {
      return {
          render_realtime: true,
      }
    },

    methods: {
        realtime_click(){
            this.render_realtime = true;
        },

        quick_stats_click(){
            this.render_realtime = false;
        },
    },

    mounted() {

    },
  };
</script>
<style>
    .button_row {
        display: -webkit-box;
        display: flex;
        flex-wrap: wrap;
        margin-left: -15px;
        -webkit-box-pack: center;
        justify-content: right;
        margin-right: 10px;
    }
</style>
