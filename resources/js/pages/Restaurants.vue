<template>
  <admin-layout>
    <v-banner class="mb-4">
      <div class="d-flex flex-wrap justify-space-between">
        <h5 class="text-h5 font-weight-bold">Restorani i ponude</h5>
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
        Dodaj novi restoran
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
          Restoran je u funkciji
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
          Restoran trenutno nije u funkciji
        </v-chip>
      </template>
      <template #[`item.index`]="{ index }">
        {{ (options.page - 1) * options.itemsPerPage + index + 1 }}
      </template>
      <template #[`item.action`]="{ item }">
        <v-btn color="primary" @click="loadMenuForRestaurant(item)" :disabled="disabledButtons(item)" >
          <v-icon small> mdi-menu-open</v-icon>
          Meni
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
            label="Naziv restorana"
            :error-messages="form.errors.name"
            type="text"
            outlined
            dense
          />
          <v-text-field
            v-model="form.address"
            label="Adresa"
            :error-messages="form.errors.address"
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
            label="Operativnost"
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
        <v-toolbar dense dark color="primary" class="text-h6  mb-4">Brisanje Restorana</v-toolbar>
        <v-card-text class="text-h6 mt-4"> Da li ste sigurni da zelite da obrisete ovaj restoran? Ovim putem se brise cijeli njegov meni!</v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn :disabled="form.processing" text color="error" @click="dialogDelete = false">Odustani</v-btn>
          <v-btn :loading="form.processing" text color="primary" @click="destroy">Prihvati</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </admin-layout>
</template>

<script>
import AdminLayout from "../layouts/AdminLayout.vue";
import axios from 'axios';

export default {
  name: "Restaurants",
  props: ["items"],
  components: {AdminLayout},
  data() {
    return {
      user_type: null,
      tableData: [],
      noDataText: 'Nema Podataka Za Prikaz',
      statuses:
        [
          {value: true, label: 'U funkciji'},
          {value: false, label: 'Nije u funkciji'}
        ],
      items_per_page: [5, 10, 15],
      headers: [
        {text: "Naziv restorana", value: "name"},
        {text: "Adresa", value: "address"},
        {text: "Operativnost", value: "status"},
        {text: "Akcije", value: "action", sortable: false, width: 300},
      ],
      dialog: false,
      dialogDelete: false,
      isUpdate: false,
      isLoading: false,
      isLoadingTable: false,
      itemId: null,
      options: {},
      search: null,
      params: {},
      form: this.$inertia.form({
        id: null,
        name: null,
        address: null,
        user_id: null,
        status: null,
      }),
      breadcrumbs: [
        {
          text: "App",
          disabled: false,
          href: "/home",
        },
        {
          text: "Restaurants",
          disabled: true,
          href: "/restaurants",
        },
      ],
    }
  },
  computed: {
    formTitle() {
      return this.isUpdate ? "Izmijeni podatke restorana" : "Dodaj novi restoran";
    },
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
  },
  methods: {
    disabledButtons(item) {
      return item.status == 0 && this.user_type != 'Admin';
    },
    test(item) {
      console.log(item)
    },
    loadMenuForRestaurant(item) {
      this.params.status = item.status
      this.params.restaurant_id = item.id
      this.isLoadingTable = true
      this.$inertia.get("/products", this.params, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          this.isLoadingTable = false
        },
      });
    },
    loadTableData() {
      this.tableData = this.$page.props.restaurants
    },
    updateData() {
      this.isLoadingTable = true
      this.$inertia.get("/restaurants", this.params, {
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
    editItem(item) {
      this.form.clearErrors();
      this.form.name = item.name;
      this.form.status = item.status;
      this.form.address = item.address;
      this.form.user_id = this.$page.props.auth.user.id;
      this.isUpdate = true;
      this.itemId = item.id;
      this.dialog = true;
    },
    deleteItem(item) {
      this.form.id = item.id
      this.itemId = item.id;
      this.dialogDelete = true;
    },
    destroy() {
      this.form.delete(route("restaurants.destroy", this.itemId), {
        preserveScroll: true,
        onSuccess: () => {
          this.dialogDelete = false;
          this.itemId = null;
        },
      });
    },
    submit() {
      if (this.isUpdate) {
        this.form.put(route("restaurants.update", this.itemId), {
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
        this.form.post(route("restaurants.store"), {
          preserveScroll: true,
          onSuccess: () => {
            this.isLoading = false;
            this.dialog = false;
            this.form.reset();
          },
        });
      }
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
    // console.log(this.$inertia, ' INERTIA ')   // complete inertia background rendering
    // console.log(this.$page.props.auth.user, ' PAGE ')         // inertia page data
    // this.loadTableData();
    // this.axiosTest()
  }
}
</script>
