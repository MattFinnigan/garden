<template>
  <div v-if="!loading" class="crops-page">
    <div v-if="!current">
      <div class="header-contain">
        <Button classes="sm icon primary" @click="newCrop" :disabled="!plants.length"><Icon name="plus"></Icon></Button>
      </div>
      <div v-if="loading">Loading...</div>
      <div v-else>
        <Table
          :headers="headers"
          :rows="cropsMapped"
          :actions="{ view: true, delete: true }"
          @view="(crop) => { viewCrop(crop) }"
          @delete="(crop) => handleDelete(crop.id)">
        </Table>
      </div>
    </div>
    <CropForm v-else-if="mode === 'edit'"/>
    <div v-else-if="embedded">
      <div class="back-button">
        <Button classes="sm primary" @click="deselect">Back</Button>
      </div>
      <Crop :embedded="embedded"></Crop>
    </div>
  </div>
</template>

<script>
import CropForm from '../forms/CropForm.vue';
import { fetchCrops, deleteCrop, fetchPlants } from '../../utils/api'
import { clone } from '../../utils/helpers'
import { defaultCrop, defaultCropEntry } from '../../utils/consts'
import Crop
 from './Crop.vue';
export default {
  name: 'Crops',
  props: {
    embedded: { type: Boolean, default: false }
  },
  components: {
    CropForm,
    Crop
  },
  data () {
    return {
      loading: true,
      headers: [{ label: '#', key: 'id' }, { label: 'Plant', key: 'plant_details' }, { label: 'Action', key: 'action' }, { label: 'Time', key: 'datetimestamp', width: '80px'}, { label: 'Notes', key: 'notes' }],
      mode: 'view'
    }
  },
  mounted () {
    const promises = []
    if (!this.plants.length) {
      promises.push(fetchPlants(this))
    }
    Promise.all(promises).then(() => {
      if (!this.maps) {
        fetchCrops(this).then(response => {
          this.loading = false
        })
      }
    })
  },
  computed: {
    maps () {
      return this.$store.state.maps.list
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
          action: crop.latest_entry.action,
          datetimestamp: new Date(crop.latest_entry.datetimestamp).toLocaleString(),
          notes: crop.latest_entry.notes
        }
      })
    }
  },
  methods: {
    deselect () {
      this.$store.commit('crops/setCurrentCrop', null)
      this.mode = 'view'
    },
    viewCrop (crop) {
      if (!this.embedded) {
        this.$router.push(`/crops/${crop.id}`)
        return
      }
      this.$store.commit('crops/setCurrentCrop', clone(crop))
      this.mode = 'view'
    },
    newCrop () {
      const e = defaultCropEntry()
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
  justify-content: flex-end;
  align-items: center;
  margin-bottom: 1em;
  h1 {
    margin: 0;
  }
}
</style>