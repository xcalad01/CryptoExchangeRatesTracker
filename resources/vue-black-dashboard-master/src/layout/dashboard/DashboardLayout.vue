<template>
  <div class="wrapper">
    <side-bar>
      <template slot="links">
        <sidebar-link to="/kraken" :name="$t('kraken')" icon="icon-custom-kraken"/>
        <sidebar-link to="/gdax" :name="$t('gdax')" icon="icon-custom-gdax"/>
        <sidebar-link to="/bitfinex" :name="$t('bitfinex')" icon="icon-custom-bitfinex"/>
        <sidebar-link to="/gemini" :name="$t('gemini')" icon="icon-custom-gemini"/>
        <sidebar-link to="/bitstamp" :name="$t('bitstamp')" icon="icon-custom-bitstamp"/>
        <sidebar-link to="/poloniex" :name="$t('poloniex')" icon="icon-custom-poloniex"/>
        <sidebar-link to="/binance" :name="$t('binance')" icon="icon-custom-binance"/>
        <sidebar-link to="/bittrex" :name="$t('bittrex')" icon="icon-custom-bittrex"/>
        <sidebar-link to="/hitbtc" :name="$t('hitbtc')" icon="icon-custom-hitbtc"/>
        <sidebar-link to="/bitbay" :name="$t('bitbay')" icon="icon-custom-bitbay"/>
        <sidebar-link to="/okcoin" :name="$t('okcoin')" icon="icon-custom-okcoin"/>
        <sidebar-link to="/okex" :name="$t('okex')" icon="icon-custom-okex"/>
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
import DashboardContent from "./Content.vue";
import MobileMenu from "./MobileMenu";
export default {
  components: {
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
