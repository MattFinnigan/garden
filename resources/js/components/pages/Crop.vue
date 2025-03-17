<template>
  <div class="crop-page">
    <div v-if="loading || !current">Loading...</div>
    <div v-else-if="!currentEntry">
      <div class="header-contain">
        <h2 v-if="!embedded">{{ cropTitle }}</h2>
        <div>
          <Button classes="danger icon sm" @click="deleteCrop"><Icon name="delete"></Icon></Button>
          <Button classes="primary icon sm" @click="newEntry"><Icon name="plus"></Icon></Button>
        </div>
      </div>
      <div>
        <Table
          :headers="headers"
          :rows="cropEntriesMapped"
          :actions="{ delete: cropEntriesMapped.length > 1 }"
          @delete="(cropEntry) => deleteCropEntry(cropEntry.id)">
        </Table>
      </div>
    </div>
    <CropEntryForm v-else-if="!embedded" :title="currentEntry.id ? 'Edit Entry' : 'New Entry'" @close="done"/>
  </div>
</template>

<script>
import Card from '../common/Card.vue'
import CropEntryForm from '../forms/CropEntryForm.vue';
import { fetchCrop, fetchPlants, deleteCrop, deleteCropEntry, fetchBed } from '../../utils/api'
import { clone } from '../../utils/helpers'

export default {
  props: {
    embedded: { type: Boolean, default: false }
  },
  name: 'Crops',
  components: {
    Card,
    CropEntryForm
  },
  emits: ['new', 'edit', 'close'],
  data () {
    return {
      loading: true,
      headers: [{ label: 'Date & Time', key: 'datetimestamp' }, { label: 'Stage', key: 'stage' }, { label: 'Action', key: 'action' }, { label: 'Notes', key: 'notes' }]
    }
  },
  mounted () {
    fetchCrop(this, this.$route.params.id || this.current?.id).then(() => {
      this.loading = false
      // fetchBed(this, this.current?.bed_id || this.bed.id).then(() => {
      //   this.loading = false
      // })
    })
  },
  computed: {
    current () {
      return this.$store.state.crops.current
    },
    currentEntry () {
      return this.$store.state.crop_entries.current
    },
    plants () {
      return this.$store.state.plants.list
    },
    bed () {
      return this.$store.state.beds.current
    },
    cropTitle () {
      return `Crop #${this.current.id } ${this.current.plant.name} (${this.current.plant.variety})`
    },
    cropEntriesMapped () {
      return this.current.crop_entries.map(cropEntry => {
        return {
          id: cropEntry.id,
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
      if (this.embedded) {
        this.$emit('new')
        return
      }
      const promises = []
      if (!this.plants.length) {
        promises.push(fetchPlants(this))
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
      deleteCrop(this, this.current.id).then(response => {
        if (!this.embedded) {
          this.$router.push({ name: 'crops' })
        } else {
          this.$emit('close')
        }
      })
    },
    deleteCropEntry (id) {
      this.loading = true
      deleteCropEntry(this, id).then(response => { this.loading = false })
    },
    editCropEntry (id) {
      if (this.embedded) {
        this.$emit('edit', id)
        return
      }
      const promises = []
      if (!this.plants.length) {
        promises.push(fetchPlants(this))
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
  div {
    margin-left: auto;
    > * {
      margin-left: 0.5em;
    }
  }
  h2 {
    margin: 0;
  }
}
</style>