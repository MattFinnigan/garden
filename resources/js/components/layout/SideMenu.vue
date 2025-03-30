
<template>
  <aside :class="['side-menu', { closed }]">
    <nav>
      <div class="nav-toggle">
        <Button class="sm" @click="closed = !closed">
          {{ !closed ? '&#8592;' : '&#8594;' }}
        </Button>
      </div>
      <Select v-model.number="month" :options="monthOptionsLong"/>
      <div class="buttons-contain">
        <Button icon :class="['icon', { accent: viewing === 'plants' }]" @click="viewing = 'plants'"><Icon colour="black" name="seedling" maskSize="22px" size="1.4em"></Icon>Plants</Button>
        <Button icon :class="['icon', { accent: viewing === 'crops' }]" @click="viewing = 'crops'"><Icon colour="black" name="view" maskSize="22px" size="1.4em"></Icon>Crops</Button>
      </div>
      <div class="list-items">
        <ListItem
          v-show="viewing === 'plants'"
          v-for="plant in plants"
          :key="plant.id"
          :highlight="plant.id === currentPlant?.id"
          @click="selectPlant(plant)">
          <template #image>
            <img :src="'./images/upload/' + plant.image" alt="Plant icon">
          </template>
          <template #title>
            {{ plant.name }}
          </template>
          <template #description>
            <p><em>{{ plant.variety }}</em></p>
            <p>Sow {{ monthsShort[plant.sow_from] }} - {{ monthsShort[plant.sow_to] }}</p>
            <p>Harvest: {{ plant.days_to_harvest }} Days</p>
          </template>
          <template #actions>
            <Button classes="sm icon transparent" @click="editPlant(plant)"><Icon name="edit" colour="black"/></Button>
            <Button classes="sm icon transparent" @click="deletePlant(plant)"><Icon name="delete" colour="black"/></Button>
          </template>
        </ListItem>
        <div class="add-button">
          <Button class="sm icon primary" @click="createPlant"><Icon name="plus"/></Button>
        </div>
      </div>
    </nav>
    <Modal v-if="editingPlant" @close="cancelPlant">
      <template #header>
        <h5>Create a new Plant </h5>
        <p>Let's add a Plant with it's first Entry</p>
      </template>
      <template #content>
        <PlantForm @done="plantSubmitted" @cancel="cancelPlant">
          <template #buttons>
            <Button class="primary" type="submit" >Done</Button>
          </template>
        </PlantForm>
      </template>
    </Modal>
  </aside>
</template>

<script>
import { monthsShort, monthOptionsLong } from '../../utils/consts'
import { fetchPlants, deletePlant, createPlant } from '../../utils/api'
import ListItem from '../layout/ListItem.vue'
import PlantForm from '../forms/PlantForm.vue'

export default {
  components: {
    PlantForm,
    ListItem
  },
  data () {
    return {
      monthOptionsLong: monthOptionsLong(),
      monthsShort: monthsShort(),
      closed: true,
      viewing: 'plants',
      mode: 'view'
    }
  },
  mounted () {
    this.month = new Date().getMonth() + 1
    fetchPlants(this)
  },
  computed: {
    currentPlant () {
      return this.$store.state.plants.current
    },
    plants () {
      return this.$store.state.plants.list
    },
    editingPlant () {
      return this.viewing === 'plants' && this.mode === 'edit'
    },
    month: {
      get () {
        return this.$store.state.maps.month
      },
      set (value) {
        return this.$store.commit('maps/setMapMonth', value)
      }
    }
  },
  watch: {
    $route () {
      this.closed = true
    }
  },
  methods: {
    selectPlant (plant) {
      this.$store.commit('plants/setCurrentPlant', plant)
    },
    cancelPlant () {
      this.mode = 'view'
      this.$store.commit('plants/setCurrentPlant', null)
    },
    createPlant () {
      this.$store.commit('plants/setCurrentPlant', null)
      this.mode = 'edit'
    },
    plantSubmitted (plant) {
      fetchPlants(this).then(() => {
        this.$store.commit('plants/setCurrentPlant', plant)
        this.mode = 'view'
      })
    },
    editPlant (plant) {
      this.$store.commit('plants/setCurrentPlant', plant)
      this.mode = 'edit'
    },
    deletePlant (plant) {
      deletePlant(this, plant.id).then(() => {
        fetchPlants(this)
      })
    }
  }
}
</script>
<style lang="scss" scoped>
aside {
  transition: all 0.3s;
  height: calc(100vh - 60px);
  position: relative;
  nav {
    border: 1px solid $grey-200;
    width: 100%;
    height: 100%;
    transition: all 0.3s;
    .nav-toggle {
      font-size: 1em;
    }
    .buttons-contain {
      display: flex;
      border: 1px solid $grey-200;
      border-top-right-radius: 10px;
      border-top-left-radius: 10px;
      button {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: $textColour;
        gap: 2px;
        flex: 1;
        border-radius: 0;
        &:first-child {
          border-top-left-radius: 10px;
        }
        &:last-child {
          border-top-right-radius: 10px;
        }
      }
    }
    .list-items {
      margin-bottom: 10px;
      max-height: 375px;
      overflow-y: scroll;
      .add-button {
        display: flex;
        justify-content: flex-end;
        width: 100%;
        position: absolute;
        bottom: 0;
        padding: 10px 0;
        button {
          margin-right: 10px;
        }
      }
    }
  }
  &.closed {
    flex: 0;
    nav {
      left: -100%;
    }
    .nav-toggle {
      position: absolute;
      left: 100%;
    }
  }
  @include device (desktop, 'all') {
    &.closed {
      flex: 0 0 200px;
      nav {
        left: unset;
      }
    }
    nav {
      width: 250px;
      .nav-toggle {
        display: none;
      }
    }
  }
}
</style>
