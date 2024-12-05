<template>
  <div class="crop-page">
    <div v-if="loading">Loading...</div>
    <div v-else-if="!currCropEntry">
      <div class="header-contain">
        <h1>{{ cropTitle }}</h1>
        <div>
          <Button classes="sm" @click="newEntry">+</Button>
          <Button classes="sm" @click="deleteCrop" :disabled="crop.crop_entries.length === 1">-</Button>
        </div>
      </div>
      <div>
        <Table
          :headers="headers"
          :rows="cropEntriesMapped"
          :actions="{ edit: true, delete: true }"
          @edit="(cropEntry) => editCropEntry(cropEntry.id)"
          @delete="(cropEntry) => deleteCropEntry(cropEntry.id)">
        </Table>
      </div>
    </div>
    <CropEntryForm
      v-if="currCropEntry && locations.length && plants.length"
      :title="currCropEntry.id ? 'Edit Entry' : 'New Entry'"
      :val="currCropEntry"
      :plants="plants"
      :locations="locations"
      :crop="crop"
      @add="ev => addCropEntry(ev)"
      @patch="updateCropEntry"
      @close="currCropEntry = null"/>
  </div>
</template>

<script>
import Card from '../common/Card.vue'
import CropEntryForm from '../forms/CropEntryForm.vue';
import { fetchCrop, fetchPlots, fetchPlants, deleteCrop, deleteCropEntry } from '../../utils/api'
import { clone } from '../../utils/helpers'

export default {
  name: 'Crops',
  components: {
    Card,
    CropEntryForm
  },
  data () {
    return {
      loading: true,
      crop: null,
      plants: [],
      locations: [],
      currCropEntry: null,
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
    cropEntriesMapped () {
      return this.crop.crop_entries.map(cropEntry => {
        return {
          id: cropEntry.id,
          curr_loc: cropEntry.location.name + (cropEntry.bed ? ` (${cropEntry.bed.name})` : ''),
          stage: `x${cropEntry.qty} ${cropEntry.stage}`,
          action: cropEntry.action,
          datetimestamp: new Date(cropEntry.datetimestamp).toLocaleDateString(),
          notes: cropEntry.notes
        }
      })
    }
  },
  methods: {
    addCropEntry (ev) {
      this.crop.crop_entries.unshift(ev)
      // sort by date desc
      this.crop.crop_entries.sort((a, b) => new Date(b.datetimestamp) - new Date(a.datetimestamp))
    },
    newEntry () {
      if (!this.plants.length || !this.locations.length) {
        this.loading = true
        fetchPlants().then(response => {
          this.plants = response.data
          fetchPlots().then(response => {
            this.locations = response.data
            this.currCropEntry =  {
              ...this.cropLastEntry(),
              id: null,
              notes: null,
              image: null
            }
            this.loading = false
          })
        })
      } else {
        this.currCropEntry =  {
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
    deleteCropEntry (id) {
      this.loading = true
      deleteCropEntry(id).then(response => {
        this.crop.crop_entries = this.crop.crop_entries.filter(cropEntry => cropEntry.id !== id)
      }).finally(() => {
        this.loading = false
      })
    },
    editCropEntry (id) {
      if (!this.plants.length || !this.locations.length) {
        this.loading = true
        fetchPlants().then(response => {
          this.plants = response.data
          fetchPlots().then(response => {
            this.locations = response.data
            this.currCropEntry = clone(this.crop.crop_entries.find(cropEntry => cropEntry.id === id))
            this.loading = false
          })
        })
      } else {
        this.currCropEntry = clone(this.crop.crop_entries.find(cropEntry => cropEntry.id === id))
      }
    },
    cropLastEntry () {
      return this.crop.crop_entries[0] 
    },
    updateCropEntry (ev) {
      const index = this.crop.crop_entries.findIndex(cropEntry => cropEntry.id === ev.id)
      this.crop.crop_entries.splice(index, 1, ev)
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