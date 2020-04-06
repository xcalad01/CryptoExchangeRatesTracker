import SideBar from "../components/SidebarPlugin";
import Notify from "../components/NotificationPlugin";
import GlobalComponents from "./globalComponents";
import GlobalDirectives from "./globalDirectives";
import RTLPlugin from "./RTLPlugin";

//css assets
import "/Users/filipcaladi/Sites/bakalarka/resources/vue-black-dashboard-master/src/assets/sass/black-dashboard.scss";
import "/Users/filipcaladi/Sites/bakalarka/resources/vue-black-dashboard-master/src/assets/css/nucleo-icons.css";
import "/Users/filipcaladi/Sites/bakalarka/resources/vue-black-dashboard-master/src/assets/demo/demo.css";

export default {
  install(Vue) {
    Vue.use(GlobalComponents);
    Vue.use(GlobalDirectives);
    Vue.use(SideBar);
    Vue.use(Notify);
    Vue.use(RTLPlugin);
  }
}
