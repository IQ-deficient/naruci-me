require("./bootstrap");
require("./plugins/vue-toastification");

import Vue from "vue";
import {
  App as InertiaApp,
  plugin as InertiaPlugin
} from "@inertiajs/inertia-vue";
import { Link } from "@inertiajs/inertia-vue";
import vuetify from "./plugins/vuetify";
import { InertiaProgress } from "@inertiajs/progress";
import VuePaycard from "vue-paycard";
import VueCardFormat from 'vue-credit-card-validation';

Vue.use(VueCardFormat);
Vue.use(VuePaycard);
Vue.use(InertiaPlugin);
Vue.component("Link", Link);
Vue.mixin({ methods: { route: window.route } });
const app = document.getElementById("app");

new Vue({
  vuetify,
  render: h =>
    h(InertiaApp, {
      props: {
        title: title => `${title} - My App`,
        initialPage: JSON.parse(app.dataset.page),
        resolveComponent: name => require(`./pages/${name}`).default
      }
    })
}).$mount(app);

InertiaProgress.init({ color: "#4B5563" });
