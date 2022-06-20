<template>
  <admin-layout>
    <v-banner class="mb-4">
      <div class="d-flex flex-wrap justify-space-between">
        <h5 class="text-h5 font-weight-bold">Meni</h5>
        <!--        <v-breadcrumbs :items="breadcrumbs" class="pa-0"></v-breadcrumbs>-->
      </div>
    </v-banner>
    <div class="d-flex flex-wrap align-center">
      <v-text-field
        v-model="search"
        prepend-inner-icon="mdi-magnify"
        label="Pretraga"
        single-line
        dense
        clearable
        hide-details
        class="py-4"
        solo
        style="max-width: 300px"
      />
      <v-spacer/>
      <v-btn v-if="user_type == 'Admin'" color="primary" @click="create">
        <v-icon dark left> mdi-plus</v-icon>
        Dodaj novi proizvod
      </v-btn>
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
      <template #[`item.status`]="{ item }">
        <v-chip
          v-if="item.status"
          class="ma-2"
          color="success"
          outlined>
          <v-icon left>
            mdi-fire
          </v-icon>
          Dostupan
        </v-chip>
        <v-chip
          v-else
          class="ma-2"
          color="deep-purple accent-4"
          outlined
        >
          <v-icon left>
            mdi-wrench
          </v-icon>
          Nedostupan
        </v-chip>
      </template>
      <template #[`item.index`]="{ index }">
        {{ (options.page - 1) * options.itemsPerPage + index + 1 }}
      </template>
      <template #[`item.action`]="{ item }">
        <v-btn color="primary" @click="infoItem(item)" :disabled="disabledButtons(item)">
          <v-icon small> mdi-information-outline</v-icon>
          Info
        </v-btn>
        <v-btn v-if="user_type == 'Admin'" color="yellow" @click="editItem(item)">
          <v-icon small> mdi-pencil</v-icon>
        </v-btn>
        <v-btn v-if="user_type == 'Admin'" color="red" dark @click="deleteItem(item)">
          <v-icon small> mdi-delete</v-icon>
        </v-btn>
      </template>

    </v-data-table>
    <v-dialog v-model="dialog" max-width="500px" scrollable>
      <v-card>
        <v-toolbar dense dark color="primary" class="text-h6  mb-4">
          {{ formTitle }}
        </v-toolbar>
        <v-card-text class="pt-4">
          <v-text-field
            v-model="form.name"
            label="Ime proizvoda"
            :error-messages="form.errors.name"
            type="text"
            outlined
            dense
          />
          <v-textarea
            :error-messages="form.errors.description"
            outlined
            no-resize
            label="Opis"
            v-model="form.description"
          />
          <v-text-field
            v-model="form.price"
            label="Cijena"
            prefix="€"
            type="number"
            :min="0"
            :error-messages="form.errors.price"
            outlined
            dense
          />
          <v-autocomplete
            :error-messages="form.errors.status"
            outlined
            dense
            v-model="form.status"
            :items="statuses"
            item-text="label"
            label="Dostupnost"
            onkeyup="if(this.form.status<0)this.value=1"
          />
          <div class="d-flex"></div>
        </v-card-text>
        <v-card-actions>
          <v-btn :disabled="form.processing" text color="error" @click="dialog = false">Otkazi</v-btn>
          <v-spacer/>
          <v-btn :loading="form.processing" color="primary" @click="submit">Prihvati</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogDelete" max-width="500">
      <v-card>
        <v-toolbar dense dark color="primary" class="text-h6  mb-4">Brisanje Proizvoda</v-toolbar>
        <v-card-text class="text-h6 mt-4"> Da li ste sigurni da zelite da obrisete ovaj proizvod?</v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn :disabled="form.processing" text color="error" @click="dialogDelete = false">Odustani</v-btn>
          <v-btn :loading="form.processing" text color="primary" @click="destroy">Prihvati</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogInfo" max-width="800px" scrollable>
      <v-card>
        <v-toolbar dense dark color="primary" class="text-h6  mb-4">
          Porucivanje
        </v-toolbar>
        <v-card-text class="pt-4">
          <v-text-field
            v-model="form.name"
            label="Ime proizvoda"
            filled
            readonly
          />
          <v-textarea
            filled
            no-resize
            label="Opis"
            v-model="form.description"
            readonly
          />
          <v-text-field
            v-model="form.price"
            label="Cijena za jedan proizvod"
            prefix="€"
            filled
            readonly
          />
          <div class="d-flex"></div>
        </v-card-text>
        <v-card-actions>
          <v-btn :disabled="form.processing" text color="error" @click="dialogInfo = false">Nazad</v-btn>
          <v-spacer/>
          <v-text-field
            v-if="user_type != 'Admin'"
            v-model="form.quantity"
            prefix="Kolicina:"
            type="number"
            :min="1"
            :error-messages="form.errors.quantity"
            class="mr-2 shrink mt-3"
          />
          <v-btn
            v-if="user_type != 'Admin'"
            :loading="form.processing"
            color="primary"
            @click="addToCart()">
            Dodaj u korpu
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </admin-layout>
</template>

<script>
import AdminLayout from "../layouts/AdminLayout.vue";
import axios from 'axios';

export default {
  name: "Products",
  props: ["items", "restaurant_id"],
  components: {AdminLayout},
  data() {
    return {
      user_type: null,
      tableData: [],
      noDataText: 'Nema podataka za prikaz',
      statuses:
        [
          {value: true, label: 'Dostupan'},
          {value: false, label: 'Nedostupan'}
        ],
      items_per_page: [5, 10, 15],
      headers: [
        {text: "Naziv obroka", value: "name"},
        {text: "Opis", value: "description"},
        {text: "Cijena u €", value: "price", width: 150},
        {text: "Dostupnost", value: "status"},
        {text: "Akcije", value: "action", sortable: false, width: 300},
      ],
      dialog: false,
      dialogDelete: false,
      dialogInfo: false,
      isUpdate: false,
      isLoading: false,
      isLoadingTable: false,
      itemId: null,
      options: {},
      search: null,
      params: {},
      form: this.$inertia.form({
        name: null,
        description: null,
        price: null,
        user_id: null,
        status: null,
        restaurant_id: this.restaurant_id,
        quantity: 1,
        product_id: null
      }),
      breadcrumbs: [
        {
          text: "App",
          disabled: false,
          href: "/home",
        },
        {
          text: "Products",
          disabled: true,
          href: "/products",
        },
      ],
    }
  },
  computed: {
    formTitle() {
      return this.isUpdate ? "Izmijeni podatke proizvoda" : "Dodaj novi proizvoda";
    },
  },
  watch: {
    options: function (val) {
      this.params.restaurant_id = this.restaurant_id
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
  },
  methods: {
    disabledButtons(item) {
      return item.status == 0 && this.user_type != 'Admin';
    },
    test(item) {
      console.log(item)
    },
    loadTableData() {
      this.tableData = this.$page.props.products
    },
    updateData() {
      this.isLoadingTable = true
      this.$inertia.get("/products", this.params, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          this.isLoadingTable = false
        },
      });
    },
    create() {
      this.dialog = true;
      this.form.reset();
      this.form.clearErrors();
      this.form.user_id = this.$page.props.auth.user.id;
    },
    infoItem(item) {
      this.form.product_id = item.id;
      this.form.name = item.name;
      this.form.status = item.status;
      this.form.description = item.description;
      this.form.price = item.price;
      this.form.user_id = this.$page.props.auth.user.id;
      this.itemId = item.id;
      this.dialogInfo = true;
    },
    editItem(item) {
      this.form.clearErrors();
      this.form.name = item.name;
      this.form.status = item.status;
      this.form.description = item.description;
      this.form.price = item.price;
      this.form.user_id = this.$page.props.auth.user.id;
      this.isUpdate = true;
      this.itemId = item.id;
      this.dialog = true;
    },
    deleteItem(item) {
      this.itemId = item.id;
      this.dialogDelete = true;
    },
    destroy() {
      this.form.delete(route("products.destroy", this.itemId), {
        preserveScroll: true,
        onSuccess: () => {
          this.dialogDelete = false;
          this.itemId = null;
        },
      });
    },
    submit() {
      if (this.isUpdate) {
        this.form.put(route("products.update", this.itemId), {
          preserveScroll: true,
          onSuccess: () => {
            this.isLoading = false;
            this.dialog = false;
            this.isUpdate = false;
            this.itemId = null;
            this.form.reset();
          },
        });
      } else {
        this.form.post(route("products.store"), {
          preserveScroll: true,
          onSuccess: () => {
            this.isLoading = false;
            this.dialog = false;
            this.form.reset();
          },
        });
      }
    },
    addToCart() {
      this.form.quantity = parseInt(this.form.quantity)
      this.form.post(route("addToCart"), {
        preserveScroll: true,
        onSuccess: () => {
          this.isLoading = false;
          this.dialogInfo = false;
          this.form.reset();
        },
      });
      // axios.post('/addToCart', {
      //   data: 'xd',
      // })
      //   .then((response) => {
      //     console.log(response);
      //   }, (error) => {
      //     console.log(error);
      //   });
    },
    axiosTest() {
      axios.get('/users')
        .then((response) => {
          console.log(response.data);
        });
    }
  },
  created() {
    this.user_type = this.$page.props.auth.user.type
    this.form.restaurant_id = parseInt(this.restaurant_id)
  }
}
</script>
