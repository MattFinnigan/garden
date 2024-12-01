<template>
  <div class="crop-form">
    <h2>Crop Form</h2>
    <Form @submit="submitForm">
      <template #inputs>
        <Select v-model.number="currCrop.plant_id" label="Select a Plant" :options="plants.map(p => { return { label: p.name + ' (' + p.variety + ')', value: p.id } })"/>
        <Select v-model.number="currCrop.location_id" label="Select a Location" :options="locations.map(l => { return { label: l.name, value: l.id } })"/>
        <Select v-model="currCrop.action" label="Action" :options="['Planned', 'Sowed', 'Transplanted', 'Moved', 'Fertilized', 'Watered', 'Weeded', 'Pesticided', 'Pruned', 'Harvested'].map(a => { return { label: a, value: a } })"/>
        <Select v-model="currCrop.stage" label="Lifecycle Stage" :options="['Planned', 'Germination', 'Seedling', 'Vegetative', 'Flowering', 'Fruiting', 'Complete'].map(s => { return { label: s, value: s } })"/>
        <Input v-model="currCrop.qty" type="number" label="Quantity" required/>
        <Input v-model="currCrop.notes" type="textarea" label="Notes"/>
        <Input :modelValue="currCrop.image" type="file" label="Image" @change="e => currCrop.image = e.target.value"/>
      </template>
      <template #buttons>
        <Button :disabled="loading" @click="$emit('close')">Cancel</Button>
        <Button type="submit" :disabled="loading">Submit</Button>
      </template>
    </Form>
</div>
</template>

<script>
import { createCrop, updateCrop } from '../../utils/api'
export default {
  name: 'CropForm',
  props: {
    plants: Array,
    locations: Array,
    val: Object
  },
  data () {
    return {
      currCrop: this.val || {
        plant_id: this.plants[0].id,
        location_id: this.locations[0].id,
        action: 'Sowed',
        stage: 'Planned',
        qty: 1,
        notes: null,
        image: null
      },
      loading: false
    }
  },
  methods: {
    submitForm (e) {
      this.loading = true
      e.preventDefault()
      if (this.currCrop.id) {
        updateCrop(this.currCrop.id, this.currCrop).then(response => {
          this.$emit('patch', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      } else {
        createCrop(this.currCrop).then(response => {
          this.$emit('add', response.data.data)
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