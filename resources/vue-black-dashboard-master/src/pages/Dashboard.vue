<template>
  <div>
    <ohlc_chart></ohlc_chart>
    <value_chart></value_chart>
    <realtime_charts></realtime_charts>
  </div>
</template>
<script>
  import OhlcChart from './Dashboard/OhlcChart';
  import ValueChart from './Dashboard/ValueChart';
  import RealtimeCharts from './Dashboard/RealtimeCharts';

  export default {
    components: {
      ohlc_chart: OhlcChart,
      value_chart: ValueChart,
      realtime_charts: RealtimeCharts
    },
    data() {
      return {

      }
    },

    methods: {

    },

    mounted() {

    },
  };
</script>
<style>
</style>
