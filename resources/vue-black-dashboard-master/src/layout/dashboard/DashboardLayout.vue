<template>
  <div class="wrapper">
    <side-bar>
      <div :class="{'has-logo':showLogo}">
        <logo v-if="showLogo" :collapse="isCollapse" />
        <el-scrollbar wrap-class="scrollbar-wrapper">
          <el-menu
            :default-active="activeMenu"
            :unique-opened="false"
            :collapse-transition="false"
            mode="vertical"
          >
            <sidebar-item v-for="route in routes" :key="route.path" :item="route" :base-path="route.path" />
          </el-menu>
        </el-scrollbar>
      </div>
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
import MobileMenu from "./MobileMenu"

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
