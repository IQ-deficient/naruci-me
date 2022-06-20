<template>
  <admin-layout>
    <v-banner class="mb-4">
      <div class="d-flex flex-wrap justify-space-between">
        <h5 class="text-h5 font-weight-bold">Sadrzaj Vase korpe</h5>
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
      <v-btn class="mb-3 mr-2" color="primary" @click="goToRestaurants()">
        <v-icon dark left> mdi-plus</v-icon>
        {{ Array.isArray(this.items.data) && this.items.data.length ? 'Dodajte jos nesto?' : 'Pogledajte ponudu' }}
      </v-btn>
      <v-btn class="mb-3 darken-2" color="teal" dark @click="infoItem()"
             v-if="Array.isArray(this.items.data) && this.items.data.length">
        <v-icon dark left> mdi-moped-outline</v-icon>
        Porucite odmah!
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
      <template v-slot:item.product_id="{ item }">
        <v-chip
          outlined
          color="teal darken-3"
        >
          {{ getProductNameById(item.product_id) }}
        </v-chip>
      </template>
      <template #[`item.action`]="{ item }">
        <v-btn color="primary" @click="deleteItem(item)" outlined>
          <v-icon small> mdi-basket-remove-outline</v-icon>
        </v-btn>
      </template>

    </v-data-table>
    <v-dialog v-model="dialogDelete" max-width="500">
      <v-card>
        <v-toolbar dense dark color="primary" class="text-h6  mb-4">Uklanjanje predmeta iz korpe</v-toolbar>
        <v-card-text class="text-h6 mt-4"> Da li ste sigurni da zelite da uklonite ovaj proizvod?</v-card-text>
        <v-card-actions>
          <v-spacer/>
          <v-btn :disabled="form.processing" text color="error" @click="dialogDelete = false">Odustani</v-btn>
          <v-btn :loading="form.processing" text color="primary" @click="destroy">Prihvati</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogInfo" max-width="800" scrollable>
      <v-card>
        <v-toolbar dense dark color="primary" class="text-h6 mb-4">
          Rezime narudzbine
        </v-toolbar>
        <v-card-text class="pt-4">
          <v-text-field
            v-model="form.address"
            :error-messages="form.errors.address"
            label="Adresa za dostavu"
            outlined
            dense
          />
          <v-row>
            <v-col cols="4">
              <v-autocomplete
                :error-messages="form.errors.type"
                outlined
                dense
                v-model="form.type"
                :items="payment_type"
                item-text="label"
                label="Nacin placanja"
              />
            </v-col>
          </v-row>
          <v-card v-if="form.type == 'card'" class="pt-5">
            <v-card-text>
              <v-row>
                <v-col cols="8">
                  <v-text-field
                    v-cardformat:formatCardNumber
                    v-model="form.card_number"
                    :error-messages="form.errors.card_number"
                    label="Broj kartice"
                    outlined
                    dense
                  />
                </v-col>
                <v-col cols="4">
                  <v-text-field
                    v-cardformat:formatCardCVC
                    v-model="form.cvc"
                    :error-messages="form.errors.cvc"
                    label="CVC"
                    outlined
                    dense
                  />
                </v-col>
              </v-row>
              <v-row style="margin-top: -25px">
                <v-col cols="8">
                  <v-text-field
                    v-model="form.card_holder"
                    :error-messages="form.errors.card_holder"
                    label="Ime vlasnika kartice"
                    outlined
                    dense
                  />
                </v-col>
                <v-col cols="4">
                  <v-text-field
                    v-cardformat:formatCardExpiry
                    v-model="form.exp_date"
                    :error-messages="form.errors.exp_date"
                    label="Datum isteka kartice"
                    outlined
                    dense
                  />
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-card-text>
        <v-card-actions>
          <v-btn :disabled="form.processing" text color="error" @click="dialogInfo = false; form.reset()">Nazad</v-btn>
          <v-spacer/>
          <v-text-field
            v-model="form.bill"
            prefix="€"
            label="Ukupna cijena narudzbine"
            :error-messages="form.errors.bill"
            class="mr-2 shrink mt-3"
            readonly
          />
          <v-btn
            :disabled="orderDisabled()"
            v-if="user_type != 'Admin'"
            :loading="form.processing"
            color="primary"
            @click="orderItems()">
            Potvrdi porudzbinu
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </admin-layout>
</template>

<script>
import AdminLayout from "../layouts/AdminLayout.vue";
import axios from 'axios';
import {VuePaycard} from "vue-paycard";
import VueCardFormat from 'vue-credit-card-validation';

export default {
  name: "Cart",
  props: ["items", "products", "bill"],
  components: {AdminLayout},
  data() {
    return {
      user_type: null,
      tableData: [],
      noDataText: 'Nema Podataka Za Prikaz',
      payment_type:
        [
          {value: 'cash', label: 'Gotovinom'},
          {value: 'card', label: 'Karticom'}
        ],
      items_per_page: [5, 10, 15],
      headers: [
        {text: "Proizvod koji porucujete", value: "product_id"},
        {text: "Broj porcija za proizvod", value: "quantity", align: 'center'},
        {text: "Ukloni iz porudzbine", value: "action", sortable: false, align: 'center'},
        {text: "Vrijeme dodavanja u korpu", value: "created_at"},
      ],
      dialog: false,
      dialogDelete: false,
      isUpdate: false,
      dialogInfo: false,
      isLoading: false,
      isLoadingTable: false,
      itemId: null,
      options: {},
      search: null,
      params: {},
      form: this.$inertia.form({
        product_id: null,
        quantity: null,
        order_id: null,
        user_id: null,
        address: '',
        bill: this.bill.bill,
        type: '',
        card_number: '',
        cvc: '',
        card_holder: '',
        exp_date: '',
      }),
    }
  },
  computed: {},
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
    orderDisabled() {
      if (this.form.address == '' || this.form.type == '')
        return true
      return this.form.type == 'card' &&
        (this.form.card_number == '' || this.form.cvc == '' || this.form.card_holder == '' || this.form.exp_date == '');
    },
    infoItem() {
      this.form.user_id = this.$page.props.auth.user.id;
      this.dialogInfo = true;
    },
    orderItems() {
      this.form.post(route("orderCartItems"), {
        preserveScroll: true,
        onSuccess: () => {
          this.isLoading = false;
          this.dialogInfo = false;
          this.form.reset();
        },
      });
    },
    getProductNameById(product_id) {
      return this.products.find(x => x.id === product_id)?.name + ' - ' + this.products.find(x => x.id === product_id)?.price + '€'
    },
    goToRestaurants() {
      this.isLoadingTable = true
      this.$inertia.get("/restaurants", this.params, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          this.isLoadingTable = false
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
      this.$inertia.get("/cart", this.params, {
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
      this.itemId = item.id;
      this.dialogDelete = true;
      this.form.product_id = item.product_id;
      this.form.quantity = item.quantity;
    },
    destroy() {
      this.form.delete(route("cart.destroy", this.itemId), {
        preserveScroll: true,
        onSuccess: () => {
          this.dialogDelete = false;
          this.itemId = null;
          this.form.reset();
          location.reload();
        },
      });
    },
    submit() {
      if (this.isUpdate) {
        this.form.put(route("cart.update", this.itemId), {
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
        this.form.post(route("cart.store"), {
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
      axios.get('/getCartForUser')
        .then((response) => {
          console.log(response.data);
        });
    }
  },
  created() {
    // console.log(this.items)
    this.user_type = this.$page.props.auth.user.type
    // console.log(this.$inertia, ' INERTIA ')   // complete inertia background rendering
    // console.log(this.$page.props.auth.user, ' PAGE ')         // inertia page data
    // this.loadTableData();
    // this.axiosTest()
  }
}
</script>
