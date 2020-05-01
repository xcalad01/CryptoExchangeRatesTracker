import {Card, BaseButton } from "./../components";
/**
 * You can register global components here and use them as a plugin in your main Vue instance
 */

const GlobalComponents = {
  install(Vue) {
    Vue.component(Card.name, Card);
    Vue.component(BaseButton.name, BaseButton);
  }
};

export default GlobalComponents;
