<template>
  <div v-if="!loading" class="crops-page">
    <div v-if="!current">
      <div class="header-contain">
        <h1>Crops</h1>
        <Button classes="sm" @click="newCrop" :disabled="!plants.length || !locations.length">+</Button>
      </div>
      <div v-if="loading">Loading...</div>
      <div v-else>
        <Table
          :headers="headers"
          :rows="cropsMapped"
          :actions="{ view: true, delete: true }"
          @view="(crop) => { $router.push(`/crop/${crop.id}`) }"
          @delete="(crop) => handleDelete(crop.id)">
        </Table>
      </div>
    </div>
    <CropForm v-else/>
  </div>
</template>

<script>
import CropForm from '../forms/CropForm.vue';
import { fetchCrops, deleteCrop, fetchPlants, fetchLocations } from '../../utils/api'
import { clone } from '../../utils/helpers'
import { defaultCrop, defaultCropEntry } from '../../utils/consts'

export default {
  name: 'Crops',
  components: {
    CropForm
  },
  data () {
    return {
      loading: true,
      headers: [{ label: '#', key: 'id' }, { label: 'Plant', key: 'plant_details' }, { label: 'Location', key: 'curr_loc' }, { label: 'Action', key: 'action' }, { label: 'Time', key: 'datetimestamp', width: '80px'}, { label: 'Notes', key: 'notes' }],
      mode: 'view'
    }
  },
  mounted () {
    const promises = []
    if (!this.locations.length) {
      promises.push(fetchLocations(this))
    }
    if (!this.plants.length) {
      promises.push(fetchPlants(this))
    }
    Promise.all(promises).then(() => {
      fetchCrops(this).then(response => {
        this.loading = false
      })
    })
  },
  computed: {
    locations () {
      return this.$store.state.locations.list
    },
    plants () {
      return this.$store.state.plants.list
    },
    crops () {
      return this.$store.state.crops.list
    },
    current () {
      return this.$store.state.crops.current
    },
    cropsMapped () {
      return this.crops.map(crop => {
        return {
          id: crop.id,
          plant_details: `
            x${crop.latest_entry.qty} ${crop.plant.name} (${crop.plant.variety})<br/>
            <em>${crop.latest_entry.stage}</em>
          `,
          qty: crop.latest_entry.qty,
          curr_loc: `${crop.latest_entry.location.name} ${crop.latest_entry.bed ? `<br/>(${crop.latest_entry.bed.name})` : ''}`,
          action: crop.latest_entry.action,
          datetimestamp: new Date(crop.latest_entry.datetimestamp).toLocaleString(),
          notes: crop.latest_entry.notes
        }
      })
    }
  },
  methods: {
    newCrop () {
      const e = defaultCropEntry(this.locations[0])
      const c = defaultCrop(this.plants[0])
      this.$store.commit('crops/setCurrentCrop', c)
      this.$store.commit('crop_entries/setCurrentCropEntry', e)
      this.mode = 'edit'
    },
    editCrop (id) {
      this.$store.commit('crops/setCurrentCrop', clone(this.crops.find(crop => crop.id === id)))
      this.mode = 'edit'
    },
    handleDelete (id) {
      this.loading = true
      deleteCrop(this, id).then(() => { this.loading = false; })
      
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