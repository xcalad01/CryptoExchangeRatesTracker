<template>
  <div>
    <div class="button_row">
        <div class="btn-group btn-group-toggle float-right">
            <label id="0" class="btn btn-sm btn-primary btn-simple">
                <input v-on:click="realtime_click" type="radio" name="options" autocomplete="off"> Realtime </input>
            </label>
            <label id="1" class="btn btn-sm btn-primary btn-simple">
                <input v-on:click="quick_stats_click" type="radio" name="options" autocomplete="off"> Quick Stats & About</input>
            </label>
        </div>
    </div>
    <realtime_charts v-if="render_realtime"></realtime_charts>
    <quick_stats v-else></quick_stats>

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
