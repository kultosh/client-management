import Vue from "vue";
import Router from "vue-router";
import ListClient from "@/views/client/ListClient.vue";

Vue.use(Router);

const router = new Router({
  mode: "history",
  routes: [
    {
      path: "/",
      component: ListClient,
    },
  ]
});

export default router;