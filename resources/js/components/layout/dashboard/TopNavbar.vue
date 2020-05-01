<template>
  <nav class="navbar navbar-expand-lg navbar-absolute" style="justify-content: center"
       :class="{'bg-white': showMenu, 'navbar-transparent': !showMenu, 'navbar-wrapper': true}">
    <a style="font-size: 250%; padding-right: 2%">{{routeName}}</a>

    <cryptoicon v-if="isasset" v-bind:symbol="asset" size="50"/>
    <i v-else v-bind:class="icon_class"></i>

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
        searchQuery: ''
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
