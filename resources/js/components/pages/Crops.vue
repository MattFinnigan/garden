<template>
  <div class="crops-page">
    <div v-if="!newCrop">
      <div class="header-contain">
        <h1>Crops</h1>
        <Button classes="sm" @click="defaultNewCrop" :disabled="!plants.length || !locations.length">Add Crop</Button>
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
    <CropForm
      v-else
      :val="newCrop"
      :plants="plants"
      :locations="locations"
      @add="p => crops.unshift(p)"
      @close="newCrop = null"/>
  </div>
</template>

<script>
import CropForm from '../forms/CropForm.vue';
import { fetchCrops, deleteCrop, fetchPlants, fetchPlots } from '../../utils/api'

export default {
  name: 'Crops',
  components: {
    CropForm
  },
  data () {
    return {
      loading: true,
      crops: [],
      newCrop: null,
      plants: [],
      locations: [],
      headers: [{ label: '#', key: 'id' }, { label: 'Plant', key: 'plant_details' }, { label: 'Location', key: 'curr_loc' }, { label: 'Action', key: 'action' }, { label: 'Time', key: 'datetimestamp', width: '80px'}, { label: 'Notes', key: 'notes' }]
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
  computed: {
    cropsMapped () {
      return this.crops.map(crop => {
        const latestEntry = this.cropLastEntry(crop)
        return {
          id: crop.id,
          plant_details: `
            x${latestEntry.qty} ${crop.plant.name} (${crop.plant.variety})<br/>
            <em>${latestEntry.stage}</em>
          `,
          qty: latestEntry.qty,
          curr_loc: `${latestEntry.location.name} ${latestEntry.bed ? `<br/>(${latestEntry.bed.name})` : ''}`,
          action: latestEntry.action,
          datetimestamp: new Date(latestEntry.datetimestamp).toLocaleString(),
          notes: latestEntry.notes
        }
      })
    }
  },
  methods: {
    defaultNewCrop () {
      this.newCrop = {
        plant_id: this.plants[0].id,
        location_id: this.locations[0].id,
        action: 'Sowed',
        stage: 'Planned',
        qty: 1,
        notes: null,
        image: null,
        datetimestamp: new Date().toISOString().slice(0, 16)
      }
    },
    cropLastEntry (crop) {
      return crop.crop_entries[0] 
    },
    handleDelete (id) {
      this.loading = true
      deleteCrop(id).then(response => {
        this.crops = this.crops.filter(crop => crop.id !== id)
      }).finally(() => {
        this.loading = false
      })
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