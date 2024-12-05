<template>
  <div class="crop-entry-form">
    <h2>{{ title }}</h2>
    <Form @submit="submitForm">
      <template #inputs>
        <Select v-if="canEditPlant" v-model.number="currEntry.plant_id" label="Select a Plant" :options="plants.map(p => { return { label: p.name + ' (' + p.variety + ')', value: p.id } })"/>
        <Display v-else label="Plant" :val="currPlant.name + ' (' + currPlant.variety + ')'"/>
        <Select v-model.number="currEntry.location_id" label="Select a Location" :options="locations.map(l => { return { label: l.name, value: l.id } })"/>
        <Select v-if="currLocation" v-model.number="currEntry.bed_id" label="Select a Bed (Optional)" :options="currLocation.beds.map(b => { return { label: b.name, value: b.id } })"/>
        <div class="inputs-row">
          <Select v-model="currEntry.action" label="Action" :options="actionOptions"/>
          <Select v-model="currEntry.stage" label="Lifecycle Stage" :options="stageOptions"/>
        </div>
        <div class="inputs-row">
          <Input v-model="currEntry.datetimestamp" type="datetime-local" label="Date & Time"/>
          <Input v-model="currEntry.qty" type="number" label="Quantity" required/>
        </div>
        <Input v-model="currEntry.area" type="number" label="Spacing"/>
        <Input v-model="currEntry.notes" type="textarea" label="Notes"/>
        <Input :modelValue="currEntry.image" type="file" label="Image" @change="e => currEntry.image = e.target.value"/>
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
    plants: Array,
    locations: Array,
    val: Object,
    crop: Object,
    handleSubmit: {
      type: Boolean,
      default: true
    },
    title: String
  },
  emits: ['add', 'patch', 'close', 'submit'],
  data () {
    return {
      currEntry: isEmpty(this.val) || this.val ? this.val : {
        plant_id: this.plants[0].id,
        location_id: this.locations[0].id,
        bed_id: null,
        action: 'Sowed',
        stage: 'Planned',
        qty: 1,
        notes: null,
        image: null,
        area: 1,
        datetimestamp: new Date().toISOString().slice(0, 16)
      },
      loading: false
    }
  },
  computed: {
    newEntry () {
      return !this.currEntry.id && this.crop
    },
    editingEntry () {
      return this.currEntry.id && this.crop
    },
    canEditPlant () {
      return !this.crop
    },
    currPlant () {
      return this.plants.find(p => p.id === this.crop?.plant_id)
    },
    currLocation () {
      return this.locations.find(l => l.id === this.currEntry?.location_id)
    },
    actionOptions () {
      return ['Planned', 'Sowed', 'Transplanted', 'Moved', 'Fertilized', 'Watered', 'Weeded', 'Damage/Disease detected', 'Sprayed', 'Pruned', 'Harvested', 'Removed', 'No Action'].map(a => { return { label: a, value: a } })
    },
    stageOptions () {
      return ['Planned', 'Germination', 'Seedling', 'Vegetative', 'Flowering', 'Fruiting', 'Complete'].map(s => { return { label: s, value: s } })
    }
  },
  methods: {
    submitForm (e) {
      e.preventDefault()
      if (!this.handleSubmit) {
        this.$emit('submit', e, this.currEntry)
        return
      }
      this.loading = true
      if (this.newEntry) {
        createCropEntry(this.crop.id, this.currEntry).then(response => {
          this.$emit('add', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      } else if (this.editingEntry) {
        updateCropEntry(this.currEntry.id, this.currEntry).then(response => {
          this.$emit('patch', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      } 
    }
  }
}
</script>

<style scoped lang="scss">
</style>