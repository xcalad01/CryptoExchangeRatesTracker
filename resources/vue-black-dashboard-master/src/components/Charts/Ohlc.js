import ApexCharts from "apexcharts";

export default {
  data(){
    return {
      content:null,
      ohlc_chart:null,
      post:{}
    }
  },

  methods: {
    create_update_ohlc_chart(data){
      var new_data = data['data'].map(function (item) {
        return {x:(item['x'] - 3600) * 1000, y:item["y"]}
      });

      if (this.ohlc_chart != null){
        this.ohlc_chart.updateSeries([{data: new_data}]);
        return
      }
      var chartOptions = {
        chart: {
          type: 'candlestick',
          height: 350
        },
        title: {
          text: 'CandleStick Chart',
          align: 'left'
        },
        xaxis: {
          type: 'category',
          labels: {
            formatter: function(val) {
              return new Date(val).toLocaleString();
            }
          }
        },
        tooltip: {
          enabled: true,
        },
        yaxis: {
          tooltip: {
            enabled: true
          }
        },
        series: [{
          data: new_data
        }],
      };

      if (this.$refs.chart) {
        this.ohlc_chart = new ApexCharts(this.$refs.chart, chartOptions);
        this.ohlc_chart.render();
      }
    },
  }


}
