<template>
  <div class="crop-page">
    <div v-if="loading">Loading...</div>
    <div v-else-if="!currCropEvent">
      <div class="header-contain">
        <h1>History of {{ cropTitle }}</h1>
        <div>
          <Button classes="sm" @click="newEvent">Add Event</Button>
          <Button classes="sm" @click="deleteCrop" :disabled="crop.crop_history.length === 1">Delete Crop</Button>
        </div>
      </div>
      <div>
        <div v-for="cropEvent in crop.crop_history" :key="crop.id">
          <Card :title="new Date(cropEvent.created_at)" :description="cropEventDescription(cropEvent)" :image="crop.image">
            <template #actions>
              <Button classes="sm" @click="editCropEvent(cropEvent)">Edit</Button>
              <Button classes="sm" @click="deleteCropEvent(cropEvent.id)">Delete</Button>
            </template>
          </Card>
        </div>
      </div>
    </div>
    <CropEventForm
      v-if="currCropEvent && locations.length && plants.length"
      :title="currCropEvent.id ? 'Edit Event' : 'New Event'"
      :val="currCropEvent"
      :plants="plants"
      :locations="locations"
      :crop="crop"
      @add="ev => crop.crop_history.push(ev)"
      @patch="ev => crop.crop_history = crop.crop_history.map(cropEvent => cropEvent.id === ev.id ? ev : cropEvent)"
      @close="currCropEvent = null"/>
  </div>
</template>

<script>
import Card from '../common/Card.vue'
import CropEventForm from '../forms/CropEventForm.vue';
import { fetchCrop, fetchPlots, fetchPlants, deleteCrop, deleteCropEvent } from '../../utils/api'
import { clone } from '../../utils/helpers'

export default {
  name: 'Crops',
  components: {
    Card,
    CropEventForm
  },
  data () {
    return {
      loading: true,
      crop: null,
      plants: [],
      locations: [],
      currCropEvent: null
    }
  },
  mounted () {
    fetchCrop(this.$route.params.id).then(response => {
      this.crop = response.data
    }).finally(() => {
      this.loading = false
    })
  },
  computed: {
    cropTitle () {
      return `Crop #${this.crop.id } ${this.crop.plant.name} - ${this.cropLastEntry(this.crop).location.name}`
    }
  },
  methods: {
    newEvent () {
      if (!this.plants.length || !this.locations.length) {
        this.loading = true
        fetchPlants().then(response => {
          this.plants = response.data
          fetchPlots().then(response => {
            this.locations = response.data
            this.currCropEvent = {}
            this.loading = false
          })
        })
      } else {
        this.currCropEvent = {}
      }
    },
    cropEventDescription (cropEvent) {
      return `Qty: ${cropEvent.qty}<br/>Stage: ${cropEvent.stage}<br/>Action: ${cropEvent.action}<br/>Notes: ${cropEvent.notes || ''}`
    },
    deleteCrop () {
      this.loading = true
      deleteCrop(this.crop.id).then(response => {
        this.$router.push('/crops')
      }).finally(() => {
        this.loading = false
      })
    },
    deleteCropEvent (id) {
      this.loading = true
      deleteCropEvent(id).then(response => {
        this.crop.crop_history = this.crop.crop_history.filter(cropEvent => cropEvent.id !== id)
      }).finally(() => {
        this.loading = false
      })
    },
    editCropEvent (cropEvent) {
      if (!this.plants.length || !this.locations.length) {
        this.loading = true
        fetchPlants().then(response => {
          this.plants = response.data
          fetchPlots().then(response => {
            this.locations = response.data
            this.currCropEvent = clone(cropEvent)
            this.loading = false
          })
        })
      } else {
        this.currCropEvent = clone(cropEvent)
      }
    },
    cropLastEntry () {
      return this.crop.crop_history[this.crop.crop_history.length - 1] 
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