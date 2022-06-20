<template>
  <admin-layout>
    <v-banner class="mb-4">
      <div class="d-flex flex-wrap justify-space-between">
        <h5 class="text-h5 font-weight-bold">Dobrodosli</h5>
      </div>
    </v-banner>
    <v-card
      elevation="24"
      max-width="1200"
      class="mx-auto">
      <v-card-title class="flex justify-center text-lg-h5">Iz ponude izdvajamo sledece</v-card-title>
      <v-card-text>
        <v-carousel
          class="mb-4"
          cycle
          height="650"
          delimiter-icon="mdi-minus"
          hide-delimiter-background
          show-arrows-on-hover
        >
          <v-carousel-item
            v-for="(item, i) in items"
            :key="i"
            :src="item.src"
            link
            @click="loadMenuForRestaurant(item.restaurant_id)"
          >
          </v-carousel-item>
        </v-carousel>
      </v-card-text>
    </v-card>
  </admin-layout>
</template>

<script>
import AdminLayout from "../layouts/AdminLayout.vue";

export default {
  components: {AdminLayout},
  methods: {
    loadMenuForRestaurant(restaurant_id) {
      this.params.restaurant_id = restaurant_id
      this.isLoadingTable = true
      this.$inertia.get("/products", this.params, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          this.isLoadingTable = false
        },
      });
    },
  },
  data() {
    return {
      isLoadingTable: false,
      params: {},
      items: [
        {
          src: 'http://www.pekarasicilia.me/images/Lokacije/Sicilija-8-centar.jpg',
          restaurant_id: 1,
        },
        {
          src: 'https://interior-design.green/wp-content/uploads/2019/09/pizzeria-interior-design-14-1200x800.webp',
          restaurant_id: 2,
        },
        {
          src: 'https://www.vijesti.me/data/images/2019/07/16/00/4547913_20190716100752_f51a18f7b76c45799cbbb596f40c491fec4c8664ef7be76432f1e1b2f1481bbe_share.jpg',
          restaurant_id: 4,
        },
      ],
      breadcrumbs: [
        {
          text: "App",
          disabled: false,
          href: "/home",
        },
        {
          text: "Pocetna",
          disabled: true,
          href: "/home",
        },
      ],
    }
  },
  created() {
  },
  mounted() {
    if (localStorage.getItem('reloaded')) {
      // The page was just reloaded. Clear the value from local storage
      // so that it will reload the next time this page is visited.
      localStorage.removeItem('reloaded');
    } else {
      // Set a flag so that we know not to reload the page twice.
      localStorage.setItem('reloaded', '1');
      location.reload();
    }
  }
};
</script>
