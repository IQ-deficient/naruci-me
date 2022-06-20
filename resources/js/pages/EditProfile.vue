<template>
  <admin-layout>
    <v-banner class="mb-15">
      <div class="d-flex flex-wrap justify-space-between">
        <h5 class="text-h5 font-weight-bold">Izmjena korisnickog naloga</h5>
      </div>
    </v-banner>
    <v-card :loading="isLoadingTable"
            elevation="24"
            max-width="800"
            class="mx-auto pt-4">
      <v-card-text class="pt-4">
        <v-row class="ma-3">
          <v-col cols="12">
            <v-text-field
              prepend-inner-icon="mdi-account"
              v-model="form.name"
              label="Ime i Prezime"
              outlined
              dense
              type="text"
              :error-messages="form.errors.name"
            />
            <v-text-field
              v-model="form.email"
              prepend-inner-icon="mdi-email"
              type="email"
              label="Email"
              outlined
              dense
              :error-messages="form.errors.email"
            />
            <v-text-field
              v-model="form.phone_number"
              prepend-inner-icon="mdi-numeric-9-box-multiple-outline"
              label="Broj Telefona"
              type="number"
              outlined
              dense
              :error-messages="form.errors.phone_number"
            />
            <v-text-field
              prepend-inner-icon="mdi-lock"
              v-model="form.password"
              label="Nova lozinka"
              outlined
              dense
              :error-messages="form.errors.password"
              :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              :type="showPassword ? 'text' : 'password'"
              @click:append="showPassword = !showPassword"
            />
            <v-text-field
              prepend-inner-icon="mdi-lock"
              v-model="form.password_confirmation"
              label="Potvrdite novu lozinku"
              outlined
              dense
              :error-messages="form.errors.password_confirmation"
              :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              :type="showPassword ? 'text' : 'password'"
              @click:append="showPassword = !showPassword"
            />
            <v-btn
              class="mt-3"
              :loading="form.processing"
              color="primary"
              @click="updateProfileData()"
              block>
              Izmijeni Podatke
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions></v-card-actions>
    </v-card>
  </admin-layout>
</template>

<script>
import AdminLayout from "../layouts/AdminLayout.vue";
import axios from 'axios';

export default {
  name: "EditProfile",
  props: ["items"],
  components: {AdminLayout},
  data() {
    return {
      showPassword: false,
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
        id: this.items.id,
        name: this.items.name,
        email: this.items.email,
        password: '',
        password_confirmation: '',
        phone_number: this.items.phone_number = '' ? null : this.items.phone_number,
      }),
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
    updateProfileData() {
      this.itemId = this.items.id
      this.form.put(route("editProfile.update", this.itemId), {
        preserveScroll: true,
        onSuccess: () => {
          this.itemId = null;
          // this.form.reset();
          // this.updateData();
        },
      });
    },
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
      this.$inertia.get("/editProfile", this.params, {
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
      this.form.delete(route("editProfile.destroy", this.itemId), {
        preserveScroll: true,
        onSuccess: () => {
          this.dialogDelete = false;
          this.itemId = null;
        },
      });
    },
    submit() {
      if (this.isUpdate) {
        this.form.put(route("editProfile.update", this.itemId), {
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
        this.form.post(route("editProfile.store"), {
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
    console.log(this.items)
  }
}
</script>
