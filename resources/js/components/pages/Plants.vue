<template>
  <div class="plants-page">
    <div class="header-contain">
      <h1>Plants</h1>
      <Button classes="sm" @click="currPlant = {}">Add Plant</Button>
    </div>
    <div v-if="loading">Loading...</div>
    <div v-else>
      <div v-for="plant in plants" :key="plant.id">
        <Card :title="plant.name + ' (' + plant.variety + ')'" :description="plant.description" :image="plant.image">
          <template #actions>
            <Button classes="sm" @click="editPlant(plant)">Edit</Button>
            <Button classes="sm" @click="handleDelete(plant.id)">Delete</Button>
          </template>
        </Card>
      </div>
    </div>
    <PlantForm
      v-if="currPlant"
      :val="currPlant"
      @add="p => plants.push(p)"
      @patch="p => {
        const index = plants.findIndex(plant => plant.id === p.id)
        plants.splice(index, 1, p)
      }"
      @close="currPlant = null"/>
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
      loading: true,
      plants: [],
      currPlant: null
    }
  },
  mounted () {
    this.getPlants()
  },
  methods: {
    getPlants () {
      fetchPlants().then(response => {
        this.plants = response.data
      }).finally(() => {
        this.loading = false
      })
    },
    handleDelete (id) {
      this.loading = true
      deletePlant(id).then(response => {
        this.plants = this.plants.filter(plant => plant.id !== id)
      }).finally(() => {
        this.loading = false
      })
    },
    editPlant (plant) {
      this.currPlant = clone(plant)
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