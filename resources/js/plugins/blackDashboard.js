import SideBar from "../components/SidebarPlugin";
import GlobalComponents from "./globalComponents";
import GlobalDirectives from "./globalDirectives";

//css assets
import "../../sass/black-dashboard.scss";
import "../../css/nucleo-icons.css";
import "../../sass/app.scss";

export default {
  install(Vue) {
    Vue.use(GlobalComponents);
    Vue.use(GlobalDirectives);
    Vue.use(SideBar);
  }
}
