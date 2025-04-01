<template>
  <div class="crop-form">
    <Form @submit="submitForm">
      <template #inputs>
        <div v-if="canEditPlant" class="plant-edit">
          <Select
            v-model.number="plant_id"
            label="Plant"
            required
            :options="plants.map(p => { return { label: p.name + ' (' + p.variety + ')', value: p.id } })"/>
          <Button class="icon primary" @click="$emit('createPlant')"><Icon name="plus"></Icon></Button>
        </div>
        <Display v-else label="Plant" :val="currPlantName"/>
        <Input type="number" v-model="daysToHarvest" label="Harvest" suffix="&nbsp; days" required/>
        <Input type="number" v-model="spacing" label="Spacing" suffix="&nbsp; cm" required/>
        <p v-if="error" class="error">{{ error }}</p>
      </template>
      <template #buttons>
        <slot name="buttons"></slot>
      </template>
    </Form>
  </div>
</template>

<script>
import { updateCrop } from '../../utils/api'
import { dimensionsToQty } from '../../utils/helpers'
export default {
  emits: ['done', 'createPlant'],
  name: 'CropForm',
  data () {
    return {
      loading: false,
      error: null
    }
  },
  computed: {
    current () {
      return this.$store.state.crops.current
    },
    canEditPlant () {
      return this.current && !this.current.id
    },
    currPlantName () {
      return this.$store.state.crops.current?.plant ? `${this.$store.state.crops.current.plant.name}, ${this.$store.state.crops.current.plant.variety}` : ''
    },
    daysToHarvest: {
      get () {
        return this.current?.days_to_harvest
      },
      set (value) {
        this.$store.commit('crops/setCurrentCropDaysToHarvest', value)
      }
    },
    spacing: {
      get () {
        return this.current?.spacing
      },
      set (value) {
        this.$store.commit('crops/setCurrentCropSpacing', value)
      }
    },
    plants () {
      return this.$store.state.plants.list
    },
  },
  methods: {
    submitForm (e) {
      this.loading = true
      e.preventDefault()
      const postObj = {
        ...this.current,
        qty: dimensionsToQty(this.current.width, this.current.height, this.current.spacing)
      }
      updateCrop(this, this.current.id, postObj, false).then(response => {
        this.$emit('done', response.data.crop)
      })
    }
  }
}
</script>

<style scoped lang="scss">
</style>