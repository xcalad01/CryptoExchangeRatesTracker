<template>
  <nav class="navbar navbar-expand-lg navbar-absolute" style="position: absolute"
       :class="{'bg-white': showMenu, 'navbar-transparent': !showMenu, 'navbar-wrapper': true}">
    <a v-bind:href="this.exchange_links[this.$route.meta['title']]" style="font-size: 250%; padding-right: 2%; color: #f1f1e6; margin-left: 40%;">{{routeName}}</a>

    <a v-if="isasset" v-bind:href="this.crypto_links[this.$route.name]"><cryptoicon v-bind:symbol="asset" size="50"/></a>
      <a v-else v-bind:href="this.exchange_links[this.$route.meta['title']]"><i v-bind:class="icon_class"></i></a>

  </nav>
</template>
<script>
  import { CollapseTransition } from 'vue2-transitions';
  import Modal from './../../Modal';

  export default {
    components: {
      CollapseTransition,
      Modal
    },
    computed: {
      routeName() {
        const name = this.$route.meta['title'];
        return this.capitalizeFirstLetter(name);
      },
      isRTL() {
        return this.$rtl.isRTL;
      },
      asset() {
        return this.$route.name;
      },

      isasset(){
        return this.$route.path.startsWith("/asset/");
      },

      icon_class(){
        return "icon-custom-" + this.$route.meta['title'];
      }
    },
    data() {
      return {
        activeNotifications: false,
        showMenu: false,
        searchModalVisible: false,
        searchQuery: '',
          exchange_links: {
            'gemini': 'https://gemini.com/',
            'gdax': 'https://pro.coinbase.com/',
              'kraken': 'https://www.kraken.com/en-us',
              'bitstamp': 'https://www.bitstamp.net/',
              'bitfinex': 'https://www.bitfinex.com',
              'cex': 'https://cex.io/',
              'poloniex': 'https://poloniex.com/',
              'binance': 'https://www.binance.com/',
              'bittrex': 'https://bittrex.com/',
              'hitbtc': 'https://hitbtc.com/',
              'bitbay': 'https://bitbay.net/en',
              'okcoin': 'https://www.okcoin.com/',
              'okex': 'https://www.okex.com/'
          },

          crypto_links: {
            'btc': 'https://bitcoin.org/en/',
              'trx': 'https://tron.network/',
              'xmr': 'https://www.getmonero.org/',
              'bch': 'https://www.bitcoincash.org/',
              'ltc': 'https://litecoin.org/',
              'eos': 'https://eos.io/',
              'eth': 'https://ethereum.org/',
              'xrp': 'https://ripple.com/xrp/',
              'ada': 'https://www.cardano.org/',
              'xlm': 'https://www.stellar.org/',
              'xtz': 'https://tezos.com/',
              'neo': 'https://neo.org/',
              'dash': 'https://www.dash.org/',
              'etc': 'https://ethereumclassic.org/',
              'zec': 'https://z.cash/',
              'xem': 'https://nem.io/',
              'doge': 'https://dogecoin.com/',
              'qtum': 'https://qtum.org/en',
              'btg': 'https://bitcoingold.org/'


          }
      };
    },
    methods: {
      capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
      },
      toggleNotificationDropDown() {
        this.activeNotifications = !this.activeNotifications;
      },
      closeDropDown() {
        this.activeNotifications = false;
      },
      toggleSidebar() {
        this.$sidebar.displaySidebar(!this.$sidebar.showSidebar);
      },
      hideSidebar() {
        this.$sidebar.displaySidebar(false);
      },
      toggleMenu() {
        this.showMenu = !this.showMenu;
      }
    }
  };
</script>
<style>
  .material-icons {
    vertical-align: middle;
  }
</style>
