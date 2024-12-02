<template>
  <div class="crop-event-form">
    <h2>{{ title }}</h2>
    <Form @submit="submitForm">
      <template #inputs>
        <Select v-if="canEditPlant" v-model.number="currEvent.plant_id" label="Select a Plant" :options="plants.map(p => { return { label: p.name + ' (' + p.variety + ')', value: p.id } })"/>
        <Display v-else label="Plant" :val="currPlant.name + ' (' + currPlant.variety + ')'"/>
        <Select v-model.number="currEvent.location_id" label="Select a Location" :options="locations.map(l => { return { label: l.name, value: l.id } })"/>
        <Select v-if="currLocation" v-model.number="currEvent.bed_id" label="Select a Bed (Optional)" :options="currLocation.beds.map(b => { return { label: b.name, value: b.id } })"/>
        <Select v-model="currEvent.action" label="Action" :options="['Planned', 'Sowed', 'Transplanted', 'Moved', 'Fertilized', 'Watered', 'Weeded', 'Pesticided', 'Pruned', 'Harvested'].map(a => { return { label: a, value: a } })"/>
        <Select v-model="currEvent.stage" label="Lifecycle Stage" :options="['Planned', 'Germination', 'Seedling', 'Vegetative', 'Flowering', 'Fruiting', 'Complete'].map(s => { return { label: s, value: s } })"/>
        <Input v-model="currEvent.datetimestamp" type="datetime-local" label="Date & Time"/>
        <Input v-model="currEvent.qty" type="number" label="Quantity" required/>
        <Input v-model="currEvent.notes" type="textarea" label="Notes"/>
        <Input :modelValue="currEvent.image" type="file" label="Image" @change="e => currEvent.image = e.target.value"/>
      </template>
      <template #buttons>
        <Button :disabled="loading" @click="$emit('close')">Cancel</Button>
        <Button type="submit" :disabled="loading">Submit</Button>
      </template>
    </Form>
</div>
</template>

<script>
import { createCropEvent, updateCropEvent } from '../../utils/api'
import { isEmpty } from '../../utils/helpers';
export default {
  name: 'CropEventForm',
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
      currEvent: isEmpty(this.val) || this.val ? this.val : {
        plant_id: this.plants[0].id,
        location_id: this.locations[0].id,
        bed_id: null,
        action: 'Sowed',
        stage: 'Planned',
        qty: 1,
        notes: null,
        image: null,
        datetimestamp: new Date().toISOString().slice(0, 16)
      },
      loading: false
    }
  },
  computed: {
    newEvent () {
      return !this.currEvent.id && this.crop
    },
    editingEvent () {
      return this.currEvent.id && this.crop
    },
    canEditPlant () {
      return !this.crop
    },
    currPlant () {
      return this.plants.find(p => p.id === this.crop?.plant_id)
    },
    currLocation () {
      return this.locations.find(l => l.id === this.currEvent?.location_id)
    },
  },
  methods: {
    submitForm (e) {
      e.preventDefault()
      if (!this.handleSubmit) {
        this.$emit('submit', e, this.currEvent)
        return
      }
      this.loading = true
      if (this.newEvent) {
        createCropEvent(this.crop.id, this.currEvent).then(response => {
          this.$emit('add', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      } else if (this.editingEvent) {
        updateCropEvent(this.currEvent.id, this.currEvent).then(response => {
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