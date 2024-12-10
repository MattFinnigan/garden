<template>
  <div class="crop-page">
    <div v-if="loading || !current">Loading...</div>
    <div v-else-if="!currentEntry">
      <div class="header-contain">
        <h1>{{ cropTitle }}</h1>
        <div>
          <Button classes="sm" @click="newEntry">+</Button>
          <Button classes="sm" @click="deleteCrop" :disabled="current.crop_entries.length === 1" del></Button>
        </div>
      </div>
      <div>
        <Table
          :headers="headers"
          :rows="cropEntriesMapped"
          :actions="{ edit: true, delete: cropEntriesMapped.length > 1 }"
          @edit="(cropEntry) => editCropEntry(cropEntry.id)"
          @delete="(cropEntry) => deleteCropEntry(cropEntry.id)">
        </Table>
      </div>
    </div>
    <CropEntryForm v-else :title="currentEntry.id ? 'Edit Entry' : 'New Entry'" @close="done"/>
  </div>
</template>

<script>
import Card from '../common/Card.vue'
import CropEntryForm from '../forms/CropEntryForm.vue';
import { fetchCrop, fetchLocations, fetchPlants, deleteCrop, deleteCropEntry } from '../../utils/api'
import { clone } from '../../utils/helpers'

export default {
  name: 'Crops',
  components: {
    Card,
    CropEntryForm
  },
  beforeDestroy () {

  },
  beforeRouteLeave (to, from, next) {
    this.$store.commit('crops/setCurrentCrop', null)
    this.$store.commit('crop_entries/setCurrentCropEntry', null)
    next()
  },
  data () {
    return {
      loading: true,
      headers: [{ label: '#', key: 'num' }, { label: 'Location', key: 'curr_loc' }, { label: 'Stage', key: 'stage' }, { label: 'Action', key: 'action' }, { label: 'Date & Time', key: 'datetimestamp' }, { label: 'Notes', key: 'notes' }]
    }
  },
  mounted () {
    fetchCrop(this, this.$route.params.id).then(() => { this.loading = false })
  },
  computed: {
    current () {
      return this.$store.state.crops.current
    },
    currentEntry () {
      return this.$store.state.crop_entries.current
    },
    locations () {
      return this.$store.state.locations.list
    },
    plants () {
      return this.$store.state.plants.list
    },
    cropTitle () {
      return `Crop #${this.current.id } ${this.current.plant.name}`
    },
    cropEntriesMapped () {
      return this.current.crop_entries.map(cropEntry => {
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
    newEntry () {
      const promises = []
      if (!this.plants.length) {
        promises.push(fetchPlants(this))
      }
      if (!this.locations.length) {
        promises.push(fetchLocations(this))
      }
      Promise.all(promises).then(() => {
        this.$store.commit('crop_entries/setCurrentCropEntry', {
          ...this.current.latest_entry,
          id: null,
          image: null
        })
      })
    },
    deleteCrop () {
      this.loading = true
      deleteCrop(this, this.current.id).then(response => { this.$router.push('/crops') })
    },
    deleteCropEntry (id) {
      this.loading = true
      deleteCropEntry(this, id).then(response => { this.loading = false })
    },
    editCropEntry (id) {
      const promises = []
      if (!this.plants.length) {
        promises.push(fetchPlants(this))
      }
      if (!this.locations.length) {
        promises.push(fetchLocations(this))
      }
      Promise.all(promises).then(() => {
        this.$store.commit('crop_entries/setCurrentCropEntry', clone(this.current.crop_entries.find(cropEntry => cropEntry.id === id)))
      })
    },
    done () {
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
      this.loading = true
      fetchCrop(this, this.$route.params.id).then(() => { this.loading = false })
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