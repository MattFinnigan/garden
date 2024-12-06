<template>
  <div class="plants-page">
    <div class="header-contain">
      <h1>Plants</h1>
      <Button classes="sm" @click="newPlant">Add Plant</Button>
    </div>
    <div v-if="loading">Loading...</div>
    <div v-else>
      <div v-for="plant in plants" :key="plant.id">
        <Card size="sm" :title="plant.name + ' (' + plant.variety + ')'" :description="plant.description" :image="plant.image">
          <template #actions>
            <Button classes="sm" @click="editPlant(plant.id)">Edit</Button>
            <Button classes="sm" @click="handleDelete(plant.id)">Delete</Button>
          </template>
        </Card>
      </div>
    </div>
    <PlantForm v-if="current"/>
  </div>
</template>

<script>
import Card from '../common/Card.vue'
import PlantForm from '../forms/PlantForm.vue';
import { fetchPlants, deletePlant } from '../../utils/api'
import { clone } from '../../utils/helpers'

export default {
  name: 'Plants',
  components: {
    Card,
    PlantForm
  },
  data () {
    return {
      loading: true
    }
  },
  mounted () {
    if (!this.plants.length) {
      this.getPlants()
    } else {
      this.loading = false
    }
  },
  computed: {
    plants () {
      return this.$store.state.plants.list
    },
    current () {
      return this.$store.state.plants.current
    },
  },
  methods: {
    getPlants () {
      fetchPlants(this).then(() => { this.loading = false })
    },
    handleDelete (id) {
      deletePlant(this, id).then(() => { this.loading = false })
    },
    newPlant () {
      this.$store.commit('plants/setCurrentPlant', { name: '', description: '', variety: '', image: '' })
    },
    editPlant (id) {
      this.$store.commit('plants/setCurrentPlant', clone(this.plants.find(p => p.id === id)))
    }
  }
}
</script>

<style scoped lang="scss">
.header-contain {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1em;
  h1 {
    margin: 0;
  }
}
</style>