<template>
  <div class="crop-page">
    <div v-if="loading">Loading...</div>
    <div v-else-if="!currCropEvent">
      <div class="header-contain">
        <h1>{{ cropTitle }}</h1>
        <div>
          <Button classes="sm" @click="newEvent">+</Button>
          <Button classes="sm" @click="deleteCrop" :disabled="crop.crop_history.length === 1">-</Button>
        </div>
      </div>
      <div>
        <Table
          :headers="headers"
          :rows="cropEventsMapped"
          :actions="{ edit: true, delete: true }"
          @edit="(cropEvent) => editCropEvent(cropEvent.id)"
          @delete="(cropEvent) => deleteCropEvent(cropEvent.id)">
        </Table>
      </div>
    </div>
    <CropEventForm
      v-if="currCropEvent && locations.length && plants.length"
      :title="currCropEvent.id ? 'Edit Event' : 'New Event'"
      :val="currCropEvent"
      :plants="plants"
      :locations="locations"
      :crop="crop"
      @add="ev => addCropEvent(ev)"
      @patch="updateCropEvent"
      @close="currCropEvent = null"/>
  </div>
</template>

<script>
import Card from '../common/Card.vue'
import CropEventForm from '../forms/CropEventForm.vue';
import { fetchCrop, fetchPlots, fetchPlants, deleteCrop, deleteCropEvent, updateCrop } from '../../utils/api'
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
      currCropEvent: null,
      headers: [{ label: '#', key: 'num' }, { label: 'Location', key: 'curr_loc' }, { label: 'Stage', key: 'stage' }, { label: 'Action', key: 'action' }, { label: 'Date & Time', key: 'datetimestamp' }, { label: 'Notes', key: 'notes' }]
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
      return `Crop #${this.crop.id } ${this.crop.plant.name}`
    },
    cropEventsMapped () {
      return this.crop.crop_history.map(cropEvent => {
        return {
          id: cropEvent.id,
          curr_loc: cropEvent.location.name + (cropEvent.bed ? ` (${cropEvent.bed.name})` : ''),
          stage: `x${cropEvent.qty} ${cropEvent.stage}`,
          action: cropEvent.action,
          datetimestamp: new Date(cropEvent.datetimestamp).toLocaleDateString(),
          notes: cropEvent.notes
        }
      })
    }
  },
  methods: {
    addCropEvent (ev) {
      this.crop.crop_history.unshift(ev)
      // sort by date desc
      this.crop.crop_history.sort((a, b) => new Date(b.datetimestamp) - new Date(a.datetimestamp))
    },
    newEvent () {
      if (!this.plants.length || !this.locations.length) {
        this.loading = true
        fetchPlants().then(response => {
          this.plants = response.data
          fetchPlots().then(response => {
            this.locations = response.data
            this.currCropEvent =  {
              ...this.cropLastEntry(),
              id: null,
              notes: null,
              image: null
            }
            this.loading = false
          })
        })
      } else {
        this.currCropEvent =  {
          ...this.cropLastEntry(),
          id: null,
          notes: null,
          image: null
        }
      }
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
    editCropEvent (id) {
      if (!this.plants.length || !this.locations.length) {
        this.loading = true
        fetchPlants().then(response => {
          this.plants = response.data
          fetchPlots().then(response => {
            this.locations = response.data
            this.currCropEvent = clone(this.crop.crop_history.find(cropEvent => cropEvent.id === id))
            this.loading = false
          })
        })
      } else {
        this.currCropEvent = clone(this.crop.crop_history.find(cropEvent => cropEvent.id === id))
      }
    },
    cropLastEntry () {
      return this.crop.crop_history[0] 
    },
    updateCropEvent (ev) {
      const index = this.crop.crop_history.findIndex(cropEvent => cropEvent.id === ev.id)
      this.crop.crop_history.splice(index, 1, ev)
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