<template>
    <div :class="{'has-logo':showLogo}">
        <el-scrollbar wrap-class="scrollbar-wrapper">
            <el-menu
                :default-active="activeMenu"
                :background-color="variables.menuBg"
                :text-color="variables.menuText"
                :active-text-color="variables.menuActiveText"
                :unique-opened="false"
                :collapse-transition="false"
                mode="vertical"
            >
                <sidebar-item v-for="route in routes" :key="route.path" :item="route" :base-path="route.path" />
            </el-menu>
        </el-scrollbar>
    </div>
</template>

<script>
  import SidebarItem from './SidebarItem'
  import variables from '../../../sass/variables.scss'

  export default {
    props: {
      title: {
        type: String,
        default: "Exchanges"
      },
      backgroundColor: {
        type: String,
        default: "vue"
      },
      activeColor: {
        type: String,
        default: "success",
        validator: value => {
          let acceptedValues = [
            "primary",
            "info",
            "success",
            "warning",
            "danger"
          ];
          return acceptedValues.indexOf(value) !== -1;
        }
      },
      sidebarLinks: {
        type: Array,
        default: () => []
      },
      autoClose: {
        type: Boolean,
        default: true
      }
    },
    provide() {
      return {
        autoClose: this.autoClose,
        addLink: this.addLink,
        removeLink: this.removeLink
      };
    },
    components: {
      SidebarItem
    },
    computed: {
      routes() {
        return this.$router.options.routes
      },

      arrowMovePx() {
        return this.linkHeight * this.activeLinkIndex;
      },

      shortTitle() {
        return this.title.split(' ')
          .map(word => word.charAt(0))
          .join('').toUpperCase();
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

        variables() {
            return variables
        },
    },
    data() {
      return {
        linkHeight: 65,
        activeLinkIndex: 0,
        windowWidth: 0,
        isWindows: false,
        hasAutoHeight: false,
        links: []
      };
    },
    methods: {
      findActiveLink() {
        this.links.forEach((link, index) => {
          if (link.isActive()) {
            this.activeLinkIndex = index;
          }
        });
      },
      addLink(link) {
        const index = this.$slots.links.indexOf(link.$vnode);
        this.links.splice(index, 0, link);
      },
      removeLink(link) {
        const index = this.links.indexOf(link);
        if (index > -1) {
          this.links.splice(index, 1);
        }
      }
    },
    mounted() {
      this.$watch("$route", this.findActiveLink, {
        immediate: true
      });
    }
  };
</script>


<style lang="scss">

</style>
