<template>
  <div>
      <br>
      <quickstats></quickstats>
      <br>
      <br>
      <br>
      <valuecomponent></valuecomponent>
      <br>
      <br>
      <avwpcomponent></avwpcomponent>
  </div>
</template>

<script>
  import ValueComponent from './Dashboard2/Value';
  import AvwpComponent from './Dashboard2/AvwpChart';
  import QuickStats from './Dashboard2/QuickStats';

  export default {
    components: {
      valuecomponent: ValueComponent,
      avwpcomponent: AvwpComponent,
        quickstats: QuickStats

    },

    data() {
      return {
      }
    },

    mounted() {

    },

    methods:{
    }
  }
</script>

<style>

</style>

