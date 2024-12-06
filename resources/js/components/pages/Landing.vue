<template>
  <div class="landing">
    <div class="header-contain">
      <h1>Dashboard</h1>
    </div>
  </div>
</template>

<script>
import Card from '../common/Card.vue'
import PlantForm from '../forms/PlantForm.vue';
import { fetchPlants, deletePlant } from '../../utils/api'
import { clone } from '../../utils/helpers'

export default {
  name: 'Landing',
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
      fetchPlants(this).then(response => {
        this.plants = response.data
      }).finally(() => {
        this.loading = false
      })
    },
    handleDelete (id) {
      this.loading = true
      deletePlant(this, id).then(response => {
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