
<template>
  <aside :class="['side-menu', { closed }]">
    <nav>
      <div class="nav-toggle">
        <Button class="sm" @click="closed = !closed">
          {{ !closed ? '&#8592;' : '&#8594;' }}
        </Button>
      </div>
      <Select
        v-model.number="month"
        :options="[
          { label: 'January', value: 1 },
          { label: 'February', value: 2 },
          { label: 'March', value: 3 },
          { label: 'April', value: 4 },
          { label: 'May', value: 5 },
          { label: 'June', value: 6 },
          { label: 'July', value: 7 },
          { label: 'August', value: 8 },
          { label: 'September', value: 9 },
          { label: 'October', value: 10 },
          { label: 'November', value: 11 },
          { label: 'December', value: 12 }
        ]"/>
      <div class="buttons-contain">
        <Button icon :class="['icon', { primary: viewing === 'plants' }]" @click="viewing = 'plants'"><Icon colour="black" name="seedling" maskSize="16px" size="1.1em"></Icon></Button>
        <Button icon :class="['icon', { primary: viewing === 'crops' }]" @click="viewing = 'crops'"><Icon colour="black" name="view" maskSize="16px" size="1.1em"></Icon></Button>
      </div>
      <div class="list-items">
        <ListItem
          v-show="viewing === 'plants'"
          v-for="plant in plants"
          :key="plant.id"
          :highlight="plant.id === currentPlant?.id"
          @click="selectPlant(plant.id)">
          <template #image>
            <img :src="'./images/upload/' + plant.image" alt="Plant icon">
          </template>
          <template #title>
            {{ plant.name }}
          </template>
          <template #description>
            {{ plant.variety }}
          </template>
          <template #actions>
            <Button classes="sm icon secondary" @click="editPlant(plant.id)"><Icon name="edit"/></Button>
            <Button classes="sm icon danger" @click="deletePlant(plant.id)"><Icon name="delete"/></Button>
          </template>
        </ListItem>
        <div class="add-button">
          <Button class="sm icon primary" @click="createNewPlant"><Icon name="plus"/></Button>
        </div>
      </div>
    </nav>
    <Modal v-if="currentPlant && mode === 'edit'" @close="cancelPlant">
      <template #header>
        <h5>Create a new Plant </h5>
        <p>Let's add a Plant with it's first Entry</p>
      </template>
      <template #content>
        <PlantForm @done="plantSubmitted()">
          <template #buttons>
            <Button class="primary" type="submit" >Done</Button>
          </template>
        </PlantForm>
      </template>
    </Modal>
  </aside>
</template>

<script>
import { defaultPlant } from '../../utils/consts'
import { fetchPlants, deletePlant } from '../../utils/api'
import ListItem from '../layout/ListItem.vue'
import PlantForm from '../forms/PlantForm.vue'

export default {
  components: {
    PlantForm,
    ListItem
  },
  data () {
    return {
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
    selectPlant (id) {
      this.$store.commit('plants/setCurrentPlant', this.plants.find(plant => plant.id === id))
    },
    cancelPlant () {
      this.mode = 'view'
      this.$store.commit('plants/setCurrentPlant', null)
    },
    plantSubmitted () {
      fetchPlants(this).then(() => {
        this.mode = 'view'
      })
    },
    createNewPlant () {
      this.mode = 'edit'
      this.$store.commit('plants/setCurrentPlant', {
        name: '',
        variety: '',
        description: '',
        spacing: null,
        sow_from: null,
        sow_to: null,
        days_to_harvest: null,
        image: ''
      })
    },
    editPlant (id) {
      this.mode = 'edit'
      this.$store.commit('plants/setCurrentPlant', this.plants.find(plant => plant.id === id))
    },
    deletePlant (id) {
      deletePlant(this, id).then(() => {
        fetchPlants(this)
      })
    }
  }
}
</script>
<style lang="scss" scoped>
aside {
  transition: all 0.3s;
  height: 500px;
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
      button {
        flex: 1;
        border-radius: 0;
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
      width: unset;
      .nav-toggle {
        display: none;
      }
    }
  }
}
</style>
