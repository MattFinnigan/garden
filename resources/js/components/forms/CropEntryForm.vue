<template>
  <div class="crop-entry-form">
    <h2>{{ title }}</h2>
    <Form @submit="submitForm">
      <template #inputs>
        <Select v-if="canEditPlant" v-model.number="plant_id" label="Select a Plant" :options="plants.map(p => { return { label: p.name + ' (' + p.variety + ')', value: p.id } })"/>
        <Display v-else label="Plant" :val="currPlant.name + ' (' + currPlant.variety + ')'"/>
        <Select v-model.number="location_id" label="Select a Location" :options="locations.map(l => { return { label: l.name, value: l.id } })"/>
        <Select v-if="currLocation" v-model.number="bed_id" label="Select a Bed (Optional)" :options="currLocation.beds.map(b => { return { label: b.name, value: b.id } })"/>
        <div class="inputs-row">
          <Select v-model="action" label="Action" :options="actionOptions"/>
          <Select v-model="stage" label="Lifecycle Stage" :options="stageOptions"/>
        </div>
        <div class="inputs-row">
          <Input v-model="datetimestamp" type="datetime-local" label="Date & Time"/>
          <Input v-model="qty" type="number" label="Quantity" required/>
        </div>
        <Input v-model="area" type="number" label="Spacing"/>
        <Input v-model="notes" type="textarea" label="Notes"/>
        <Input :modelValue="image" type="file" label="Image" @change="e => image = e.target.value"/>
      </template>
      <template #buttons>
        <Button :disabled="loading" @click="$emit('close')">Cancel</Button>
        <Button type="submit" :disabled="loading">Submit</Button>
      </template>
    </Form>
</div>
</template>

<script>
import { createCropEntry, updateCropEntry } from '../../utils/api'
import { isEmpty } from '../../utils/helpers';
export default {
  name: 'CropEntryForm',
  props: {
    handleSubmit: {
      type: Boolean,
      default: true
    },
    title: String
  },
  emits: ['close', 'submit'],
  data () {
    return {
      loading: false
    }
  },
  computed: {
    plants () {
      return this.$store.state.plants.list
    },
    locations () {
      return this.$store.state.locations.list
    },
    current () {
      return this.$store.state.crop_entries.current
    },
    currentCrop () {
      return this.$store.state.crops.current
    },
    canEditPlant () {
      return !this.currentCrop.id
    },
    currPlant () {
      return this.plants.find(p => p.id === this.currentCrop?.plant_id)
    },
    currLocation () {
      return this.locations.find(l => l.id === this.current?.location_id)
    },
    actionOptions () {
      return ['Planned', 'Sowed', 'Transplanted', 'Moved', 'Fertilized', 'Watered', 'Weeded', 'Damage/Disease detected', 'Sprayed', 'Pruned', 'Harvested', 'Removed', 'No Action'].map(a => { return { label: a, value: a } })
    },
    stageOptions () {
      return ['Planned', 'Germination', 'Seedling', 'Vegetative', 'Flowering', 'Fruiting', 'Complete'].map(s => { return { label: s, value: s } })
    },
    plant_id: {
      get () {
        return this.currentCrop?.plant_id
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropPlant', val)
      }
    },
    location_id: {
      get () {
        return this.current?.location_id
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryLocation', val)
      }
    },
    bed_id: {
      get () {
        return this.current?.bed_id
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryBed', val)
      }
    },
    action: {
      get () {
        return this.current?.action
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryAction', val)
      }
    },
    stage: {
      get () {
        return this.current?.stage
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryStage', val)
      }
    },
    datetimestamp: {
      get () {
        return this.current?.datetimestamp
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryDatetimestamp', val)
      }
    },
    qty: {
      get () {
        return this.current?.qty
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryQty', val)
      }
    },
    area: {
      get () {
        return this.current?.area
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryArea', val)
      }
    },
    notes: {
      get () {
        return this.current?.notes
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryNotes', val)
      }
    },
    image: {
      get () {
        return this.current?.image
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryImage', val)
      }
    }
  },
  methods: {
    submitForm (e) {
      e.preventDefault()
      if (!this.handleSubmit) {
        this.$emit('submit', e, this.current)
        return
      }
      this.loading = true
      if (!this.current.id) {
        createCropEntry(this, this.currentCrop.id, this.current).then(() => { this.$emit('close') })
      } else {
        updateCropEntry(this, this.current.id, this.current).then(() => { this.$emit('close') })
      } 
    }
  }
}
</script>

<style scoped lang="scss">
</style>