<template>
  <guest-layout>
    <v-app-bar color="primary" :clipped-left="false" fixed dark app>
      <v-toolbar-title v-text="appName"/>
      <v-spacer/>
      <Link v-if="$page.props.auth.user" :href="route('home')">
        <v-btn text>Pocetna</v-btn>
      </Link>
      <div v-else>
        <Link :href="route('login')">
          <v-btn text>Prijavite se</v-btn>
        </Link>
        <Link :href="route('register')">
          <v-btn text>Registracija</v-btn>
        </Link>
      </div>
    </v-app-bar>
    <v-main>
      <v-container>
        <v-card height="800px">
          <v-card-title class="flex justify-center">
            <v-chip outlined large class="text-lg-h2 mt-4 pa-15">Dobrodosli na Naruci.me 👋🏻</v-chip>
          </v-card-title>
          <v-card-text class="pa-5">
            <v-row>
              <v-col>
                <v-card
                  class="mx-auto"
                  max-width="500"
                >
                  <v-img
                    src="https://www.eatthis.com/wp-content/uploads/sites/4/2020/03/male-food-delivery-home-face-mask-takeout.jpg?fit=1200%2C800&ssl=1"
                    height="200px"
                  ></v-img>
                  <v-card-title>
                    Dostava u najkracem mogucem roku
                  </v-card-title>
                  <v-card-subtitle>
                    Tim iskusnih vozaca ceka vasu porudzbinu
                  </v-card-subtitle>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                      icon
                      @click="show = !show"
                    >
                      <v-icon>{{ show ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                    </v-btn>
                  </v-card-actions>
                  <v-expand-transition>
                    <div v-show="show">
                      <v-divider></v-divider>
                      <v-card-text>
                        Mauris sodales justo a ex sodales iaculis. Quisque accumsan egestas mauris eget maximus.
                        Curabitur ut tellus lacinia, vestibulum ex eget, efficitur nunc. Morbi non faucibus turpis. Sed
                        eget arcu ut quam consectetur fringilla. Sed aliquam sollicitudin arcu eu dapibus. Sed congue
                        faucibus massa, a consequat tellus posuere at.
                      </v-card-text>
                    </div>
                  </v-expand-transition>
                </v-card>
              </v-col>
              <v-col>
                <v-card
                  class="mx-auto"
                  max-width="500"
                >
                  <v-img
                    src="https://www.timeoutsharjah.com/cloud/timeoutsharjah/2021/09/09/dK97efIc-food-delivery-arm-1200x800.jpg"
                    height="200px"
                  ></v-img>
                  <v-card-title>
                    Provjerena hrana na dohvat od ruke
                  </v-card-title>
                  <v-card-subtitle>
                    Saradjujemo samo sa najboljim restoranima
                  </v-card-subtitle>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                      icon
                      @click="show1 = !show1"
                    >
                      <v-icon>{{ show1 ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                    </v-btn>
                  </v-card-actions>
                  <v-expand-transition>
                    <div v-show="show1">
                      <v-divider></v-divider>
                      <v-card-text>
                        Sed aliquet vestibulum ligula sed dapibus. Curabitur nisl felis, viverra id vulputate eu,
                        tincidunt eu ipsum. Quisque porttitor ultrices gravida. Nunc eros dolor, mollis quis laoreet
                        varius, sollicitudin non lorem.
                      </v-card-text>
                    </div>
                  </v-expand-transition>
                </v-card>
              </v-col>
              <v-col>
                <v-card
                  class="mx-auto"
                  max-width="500"
                >
                  <v-img
                    src="http://www.pekarasicilia.me/images/Lokacije/Sicilia-2---Zabjelo.jpg"
                    height="200px"
                  ></v-img>
                  <v-card-title>
                    Kao primjer popularnijih restorana izdvajamo
                  </v-card-title>
                  <v-card-subtitle>
                    Sicilija - picerija / pekara / restoran
                  </v-card-subtitle>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                      icon
                      @click="show2 = !show2"
                    >
                      <v-icon>{{ show2 ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                    </v-btn>
                  </v-card-actions>
                  <v-expand-transition>
                    <div v-show="show2">
                      <v-divider></v-divider>
                      <v-card-text>
                        Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nunc at
                        sem vitae ipsum euismod interdum. Fusce feugiat nisl dapibus nunc sagittis cursus nec id nibh.
                        Duis scelerisque ultricies metus, non ultricies lacus convallis vel. Curabitur nisi augue,
                        aliquet a interdum in, iaculis eget metus. Sed interdum orci justo, a cursus eros ullamcorper
                        vitae. Phasellus volutpat ipsum vitae iaculis venenatis. Mauris at enim sodales, porttitor mi
                        quis, ultricies magna.
                      </v-card-text>
                    </div>
                  </v-expand-transition>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-container>
    </v-main>
  </guest-layout>
</template>

<script>
import ApplicationLogo from '../components/ApplicationLogo.vue'
import GuestLayout from '../layouts/GuestLayout.vue'

export default {
  components: {ApplicationLogo, GuestLayout},
  computed: {
    appName() {
      return this.$page.props.appName
    },
  },
  data() {
    return {
      show: false,
      show1: false,
      show2: false,
      images: {
        sample: require('../../../public/images/sample.jpg')
      }
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
}
</script>
