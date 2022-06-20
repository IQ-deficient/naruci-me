<template>
  <v-app style="background-color: #f5f5f5">
    <v-navigation-drawer
      :mini-variant.sync="miniVariant"
      clipped
      v-model="drawer"
      fixed
      app
    >
      <v-list nav>
        <v-list-item-group :value="indexMenu">
          <v-list-item
            v-for="(item, i) in items"
            :key="i"
            @click="goToPage(item.to)"
          >
            <v-list-item-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title v-text="item.title"/>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>
    <v-app-bar color="primary" clipped-left fixed dark app>
      <v-app-bar-nav-icon
        v-if="$vuetify.breakpoint.smAndDown"
        @click.stop="drawer = !drawer"
      />
      <v-app-bar-nav-icon v-else @click.stop="miniVariant = !miniVariant"/>
      <v-toolbar-title v-text="appName"/>
      <v-spacer/>
      <div class="d-flex align-center">
        <v-menu
          transition="slide-y-transition"
          bottom
          offset-y
        >
          <template v-slot:activator="{ on }">
            <v-btn
              color="white"
              v-on="on"
              outlined
            >
              <v-icon dark class="mr-2">mdi-account</v-icon>
              {{ user.name }}
            </v-btn>
          </template>
          <v-list>
            <v-list-item
              link
              @click="goToPage('editProfile.index')"
            >
              <v-icon class="mr-2"> mdi-account-edit-outline</v-icon>
              <v-list-item-title v-text="edit_profile"/>
            </v-list-item>
            <v-list-item
              link
              @click="logout"
            >
              <v-icon class="mr-2"> mdi-logout-variant</v-icon>
              <v-list-item-title v-text="log_out"/>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>
    </v-app-bar>
    <v-main>
      <v-container>
        <slot/>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import ApplicationLogo from "../components/ApplicationLogo.vue";

export default {
  components: {ApplicationLogo},
  data() {
    return {
      log_out: 'Odjava',
      edit_profile: 'Izmjena naloga',
      user_type: null,
      drawer: !this.$vuetify.breakpoint.smAndDown,
      items: this.scaleItems(),
      miniVariant: false,
    };
  },
  computed: {
    appName() {
      return this.$page.props.appName;
    },
    user() {
      return this.$page.props.auth.user;
    },
    indexMenu() {
      const inertiaUrl = this.$inertia.page.url.split("?")[0];
      const index = this.items.findIndex((item) => {
        const windowUrl = this.route(item.to);
        return windowUrl.includes(inertiaUrl);
      });
      return index;
    },
  },
  watch: {
    $page: {
      handler() {
        const message = this.$page.props.flash.message;
        if (message != null) {
          switch (message.type) {
            case "success":
              this.$toast.success(message.text);
              break;
            case "error":
              this.$toast.error(message.text);
              break;
          }
        }
      },
    },
  },
  methods: {
    scaleItems() {
      if (this.user_type == 'Admin') {
        return [
          {icon: "mdi-store", title: "Pocetna", to: "home"},
          {icon: "mdi-silverware-fork-knife", title: "Restorani", to: "restaurants.index"},
          {icon: "mdi-face-agent", title: "Korisnici", to: "users.index"},
          {icon: "mdi-history", title: "Narudzbine", to: "orders"},
        ]
      } else {
        return [
          {icon: "mdi-store", title: "Pocetna", to: "home"},
          {icon: "mdi-silverware-fork-knife", title: "Restorani", to: "restaurants.index"},
          {icon: "mdi-cart-variant", title: "Korpa", to: "cart.index"},
          {icon: "mdi-history", title: "Narudzbine", to: "orders"},
        ]
      }
    },
    logout() {
      this.$inertia.post("/logout");
    },
    goToPage(page) {
      this.$inertia.visit(this.route(page));
    },
  },
  created() {
    this.user_type = this.$page.props.auth.user.type
    this.items = this.scaleItems()
  }
};
</script>
