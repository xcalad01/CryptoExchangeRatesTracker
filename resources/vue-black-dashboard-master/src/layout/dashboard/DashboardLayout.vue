<template>
  <div class="wrapper">
    <side-bar>
      <template slot="links">
        <sidebar-link to="/kraken" :name="$t('kraken')" icon="https://assets.coingecko.com/markets/images/23/small/fe290a14-ac8f-4c90-9aed-5e72abf271f0.jpeg?1527171545"/>
        <sidebar-link to="/gdax" :name="$t('gdax')" icon="tim-icons icon-gdax"/>
        <sidebar-link to="/bitfinex" :name="$t('bitfinex')" icon="tim-icons icon-gdax"/>
        <sidebar-link to="/gemini" :name="$t('gemini')" icon="tim-icons icon-gdax"/>
        <sidebar-link to="/bitstamp" :name="$t('bitstamp')" icon="tim-icons icon-gdax"/>
        <sidebar-link to="/poloniex" :name="$t('poloniex')" icon="tim-icons icon-gdax"/>
        <sidebar-link to="/binance" :name="$t('binance')" icon="tim-icons icon-gdax"/>
        <sidebar-link to="/bittrex" :name="$t('bittrex')" icon="tim-icons icon-gdax"/>
        <sidebar-link to="/hitbtc" :name="$t('hitbtc')" icon="tim-icons icon-gdax"/>
        <sidebar-link to="/bitbay" :name="$t('bitbay')" icon="tim-icons icon-gdax"/>
        <sidebar-link to="/okcoin" :name="$t('okcoin')" icon="tim-icons icon-gdax"/>
        <sidebar-link to="/okex" :name="$t('okex')" icon="tim-icons icon-gdax"/>
      </template>
    </side-bar>
    <div class="main-panel">
      <top-navbar></top-navbar>

      <dashboard-content @click.native="toggleSidebar">

      </dashboard-content>

      <content-footer></content-footer>
    </div>
  </div>
</template>
<style lang="scss">
</style>
<script>
import TopNavbar from "./TopNavbar.vue";
import ContentFooter from "./ContentFooter.vue";
import DashboardContent from "./Content.vue";
import MobileMenu from "./MobileMenu";
export default {
  components: {
    TopNavbar,
    ContentFooter,
    DashboardContent,
    MobileMenu
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
