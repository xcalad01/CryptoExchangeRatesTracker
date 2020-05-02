<template>
  <div class="wrapper">
    <div class="main-panel">
      <top-navbar></top-navbar>
        <side-bar class="sidebar-container"></side-bar>
      <dashboard-content>

      </dashboard-content>

      <content-footer></content-footer>
    </div>
  </div>
</template>
<style lang="scss">
</style>

<script>
import DashboardContent from "./Content.vue";
import MobileMenu from "./MobileMenu"
import Highcharts from "highcharts";
import Highstock from "highcharts/highstock";

Highcharts.createElement('link', {
    href: 'https://fonts.googleapis.com/css?family=Dosis:400,600',
    rel: 'stylesheet',
    type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
    colors: ['#7cb5ec', '#f7a35c', '#90ee7e', '#7798BF', '#aaeeee', '#ff0066',
        '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
    chart: {
        backgroundColor: null,
        style: {
            fontFamily: 'Dosis, sans-serif'
        }
    },
    title: {
        style: {
            fontSize: '16px',
            fontWeight: 'bold',
            textTransform: 'uppercase',
            color: '#f1f1e6'
        }
    },
    tooltip: {
        borderWidth: 0,
        backgroundColor: 'rgba(219,219,216,0.8)',
        shadow: false
    },
    legend: {
        backgroundColor: '#F0F0EA',
        itemStyle: {
            fontWeight: 'bold',
            fontSize: '13px'
        }
    },
    xAxis: {
        gridLineWidth: 0,
        labels: {
            style: {
                fontSize: '12px'
            }
        }
    },
    yAxis: {
        title: {
            style: {
                textTransform: 'uppercase'
            }
        },
        labels: {
            style: {
                fontSize: '12px'
            }
        }
    },
    plotOptions: {
        candlestick: {
            lineColor: '#404048'
        }
    }
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);
Highstock.setOptions(Highcharts.theme);
import TopNavbar from "./TopNavbar.vue";

export default {
  // components: { SidebarItem },
  computed: {
    routes() {
      console.log(this.$router.options.routes[0]);
      return this.$router.options.routes
    },
    activeMenu() {
      const route = this.$route
      const { meta, path } = route
      // if set path, the sidebar will highlight the path you set
      if (meta.activeMenu) {
        return meta.activeMenu
      }
      return path
    },
  },

  components: {
    DashboardContent,
    MobileMenu,
    TopNavbar
  },
  methods: {
    toggleSidebar() {
      if (this.$sidebar.showSidebar) {
        this.$sidebar.displaySidebar(false);
      }
    }
  }
};
</script>
