<template>
  <div>
    <valuecomponent></valuecomponent>
    <br>
    <br>
    <avwpcomponent></avwpcomponent>
  </div>
</template>

<script>
  import ValueComponent from './Dashboard2/Value';
  import AvwpComponent from './Dashboard2/AvwpChart';

  export default {
    components: {
      valuecomponent: ValueComponent,
      avwpcomponent: AvwpComponent
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

