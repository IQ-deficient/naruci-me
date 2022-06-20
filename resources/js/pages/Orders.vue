<template>
  <admin-layout>
    <v-banner class="mb-4">
      <div class="d-flex flex-wrap justify-space-between">
        <h5 class="text-h5 font-weight-bold">Istorija porudzbina</h5>
      </div>
    </v-banner>
    <div class="d-flex flex-wrap align-center">
      <!--      <v-text-field-->
      <!--        v-model="search"-->
      <!--        prepend-inner-icon="mdi-magnify"-->
      <!--        label="Pretraga"-->
      <!--        single-line-->
      <!--        dense-->
      <!--        clearable-->
      <!--        hide-details-->
      <!--        class="py-4"-->
      <!--        solo-->
      <!--        style="max-width: 300px"-->
      <!--      />-->
      <v-spacer/>
    </div>
    <v-data-table
      :items="items.data"
      :headers="headers"
      :options.sync="options"
      :server-items-length="items.total"
      :loading="isLoadingTable"
      class="elevation-1"
      :no-data-text="noDataText"
      :footer-props="{
        'items-per-page-text':'Broj prikaza na strani',
        'items-per-page-options': items_per_page
      }"
    >
      <template #[`item.index`]="{ index }">
        {{ (options.page - 1) * options.itemsPerPage + index + 1 }}
      </template>
      <template #[`item.action`]="{ item }">
        <v-btn color="primary" @click="infoItem(item)">
          <v-icon small> mdi-playlist-check</v-icon>
          Sastav
        </v-btn>
      </template>
    </v-data-table>
    <v-dialog v-model="dialogInfo" max-width="800px" scrollable>
      <v-card>
        <v-toolbar dense dark color="primary" class="text-h6  mb-4">
          Izvjestaj za izabranu porudzbinu
        </v-toolbar>
        <v-card-text class="pt-4">
          <v-data-table
            :items="products"
            :headers="headers_items"
            :loading="isLoadingTable"
            class="elevation-1"
            :no-data-text="noDataText"
            :footer-props="{
            'items-per-page-text':'Broj prikaza na strani',
            'items-per-page-options': items_per_page
            }"
          />
          <v-row class="pt-2">
            <v-col>
              <v-text-field
                v-model="form.bill"
                label="Ukupna cijena porudzbine"
                prefix="€"
                filled
                readonly
              />
            </v-col>
            <v-col>
              <v-text-field
                v-model="form.created_at"
                label="Prvi unos u porudzbinu"
                prefix="📅"
                filled
                readonly
              />
            </v-col>
            <v-col>
              <v-text-field
                v-model="form.updated_at"
                label="Poslednja izmjena"
                prefix="📅"
                filled
                readonly
              />
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-btn :disabled="form.processing" text color="error" @click="dialogInfo = false">Nazad</v-btn>
          <v-spacer/>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </admin-layout>
</template>

<script>
import AdminLayout from "../layouts/AdminLayout.vue";
import axios from "axios";

export default {
  name: "Orders",
  props: ["items", "cart_data"],
  components: {AdminLayout},
  data() {
    return {
      products: [],
      noDataText: 'Nema podataka za prikaz',
      dialogInfo: false,
      items_per_page: [5, 10, 15],
      headers: this.customHeaders(),
      headers_items: [
        {text: "Cijena za proizvod u €", value: "product_price", sortable: false},
        {text: "Naziv proizvoda", value: 'product_name'},
        {text: "Broj porcija", value: "quantity"},
      ],
      dialog: false,
      isLoading: false,
      isLoadingTable: false,
      options: {},
      search: null,
      params: {},
      form: this.$inertia.form({
        order_id: null,
        bill: null,
        created_at: null,
        updated_at: null,
      }),
    };
  },
  watch: {
    options: function (val) {
      this.params.page = val.page;
      this.params.page_size = val.itemsPerPage;
      if (val.sortBy.length != 0) {
        this.params.sort_by = val.sortBy[0];
        this.params.order_by = val.sortDesc[0] ? "desc" : "asc";
      } else {
        this.params.sort_by = null;
        this.params.order_by = null;
      }
      this.updateData();
    },
    search: function (val) {
      this.params.search = val;
      this.updateData();
    },
    dialogInfo: function (val) {
      if (val === false) {
        this.form.reset()
        this.products = []
      }
    }
  },
  computed: {
    loadCartData() {
    }
  },
  methods: {
    infoItem(item) {
      // console.log(item)
      this.isLoadingTable = true
      this.form.order_id = item.id;
      this.form.bill = item.bill;
      this.form.created_at = item.created_at;
      this.form.updated_at = item.updated_at;
      for (let i = 0; i < this.cart_data.length; i++) {
        if (this.cart_data[i].order_id === item.id) {
          this.products.push(this.cart_data[i])
        }
      }
      this.dialogInfo = true;
      this.isLoadingTable = false
      // console.log(this.$inertia.page.props)
    },
    customHeaders() {
      if (this.$inertia.page.props.auth.user.type == 'Admin') {
        return [
          {text: "Index", value: "index", sortable: false},
          {text: "Korisnicki ID", value: "user_id", sortable: false},
          {text: "Ukupni racun u €", value: "bill"},
          {text: "Vrijeme kompletiranja porudzbine", value: "updated_at"},
          {text: "Dodatne informacije", value: "action", sortable: false},
        ]
      }
      return [
        {text: "Index", value: "index", sortable: false},
        {text: "Ukupni racun u €", value: "bill"},
        {text: "Vrijeme kompletiranja porudzbine", value: "updated_at"},
        {text: "Dodatne informacije", value: "action", sortable: false},
      ]
    },
    updateData() {
      this.isLoadingTable = true
      this.$inertia.get("/orders", this.params, {
        preserveState: true,
        preverseScroll: true,
        onSuccess: () => {
          this.isLoadingTable = false
        },
      });
    },
  },
  created() {
    //this.$inertia.page.props.auth.user.name
    // console.log(this.items)
    // console.log(this.cart_data, ' cart_data ')
  }
};
</script>
