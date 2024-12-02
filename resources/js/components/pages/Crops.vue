<template>
  <div class="crops-page">
    <div v-if="!newCrop">
      <div class="header-contain">
        <h1>Crops</h1>
        <Button classes="sm" @click="newCrop = {}" :disabled="!plants.length || !locations.length">Add Crop</Button>
      </div>
      <div v-if="loading">Loading...</div>
      <div v-else>
        <div v-for="crop in crops" :key="crop.id">
          <Card :title="cropTitle(crop)" :description="cropDescription(crop)" :image="crop.image">
            <template #actions>
              <Link classes="button sm" :to="'/crop/' + crop.id">View</Link>
              <Button classes="sm" @click="handleDelete(crop.id)">Delete</Button>
            </template>
          </Card>
        </div>
      </div>
    </div>
    <CropForm
      v-else
      :val="newCrop"
      :plants="plants"
      :locations="locations"
      @add="p => crops.push(p)"
      @close="newCrop = null"/>
  </div>
</template>

<script>
import Card from '../common/Card.vue'
import CropForm from '../forms/CropForm.vue';
import { fetchCrops, deleteCrop, fetchPlants, fetchPlots } from '../../utils/api'
import { clone } from '../../utils/helpers'

export default {
  name: 'Crops',
  components: {
    Card,
    CropForm
  },
  data () {
    return {
      loading: true,
      crops: [],
      newCrop: null,
      plants: [],
      locations: []
    }
  },
  mounted () {
    fetchCrops().then(response => {
      this.crops = response.data
      fetchPlants().then(response => {
        this.plants = response.data
        fetchPlots().then(response => {
          this.locations = response.data
          this.loading = false
        })
      })
    })
  },
  methods: {
    cropTitle (crop) {
      const str = `Crop #${crop.id } ${crop.plant.name} - ${this.cropLastEntry(crop).location.name}`
      if (this.cropLastEntry(crop).bed) {
        return `${str} (${this.cropLastEntry(crop).bed.name})`
      }
      return str
    }, 
    cropLastEntry (crop) {
      return crop.crop_history[crop.crop_history.length - 1] 
    },
    cropDescription (crop) {
      const latestEntry = this.cropLastEntry(crop)
      return `${latestEntry.qty} ${crop.plant.name} (${crop.plant.variety})<br/>Current stage: ${latestEntry.stage}<br/>Last action: ${latestEntry.action} on ${new Date(latestEntry.created_at)}`
    },
    handleDelete (id) {
      this.loading = true
      deleteCrop(id).then(response => {
        this.crops = this.crops.filter(crop => crop.id !== id)
      }).finally(() => {
        this.loading = false
      })
    },
    editCrop (crop) {
      this.newCrop = clone(crop)
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