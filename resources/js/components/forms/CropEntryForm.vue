<template>
  <div class="crop-entry-form">
    <Form @submit="submitForm">
      <template #inputs>
        <Display label="Plant" :val="currPlantName"/>
        <Select v-model="action" label="Action" :options="actionOptions"/>
        <Select v-model="stage" label="Stage" :options="stageOptions"/>
        <Input v-if="findRule('datetimestamp', 'enabled')" v-model="datetimestamp" type="datetime-local" label="Date"/>
        <Input v-model="notes" type="textarea" label="Notes"/>
        <Images class="images" :modelValue="images" label="Images" multiple @addImage="addImage" @removeImage="removeImage"/>
        <p v-if="error" class="error">{{ error }}</p>
      </template>
      <template #buttons>
        <slot name="buttons"></slot>
      </template>
    </Form>
  </div>
</template>

<script>
import { createCropEntry, fetchPlants } from '../../utils/api'
import { clone } from '../../utils/helpers'

export default {
  name: 'CropEntryForm',
  props: {
    rules: {
      type: Array,
      default: () => { return [{ key: 'name', enabled: false }] }
    }
  },
  emits: ['done'],
  data () {
    return {
      loading: false,
      savedEntry: null,
      error: null
    }
  },
  computed: {
    current () {
      return this.$store.state.crop_entries.current
    },
    currentCrop () {
      return this.$store.state.crops.current
    },
    currPlantName () {
      return this.currentCrop.plant.name +' (' + this.currentCrop.plant.variety + ')'
    },
    actionOptions () {
      return ['Planned', 'Sowed', 'Transplanted', 'Moved', 'Fertilized', 'Watered', 'Weeded', 'Damage/Disease detected', 'Sprayed', 'Pruned', 'Harvested', 'Removed', 'No Action'].map(a => { return { label: a, value: a } })
    },
    stageOptions () {
      return ['Planned', 'Germination', 'Seedling', 'Vegetative', 'Flowering', 'Fruiting', 'Complete'].map(s => { return { label: s, value: s } })
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
    notes: {
      get () {
        return this.current?.notes
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryNotes', val)
      }
    },
    images: {
      get () {
        return this.current.images
      },
      set (value) {
        this.$store.commit('crop_entries/setCurrentCropEntryImages', value)
      }
    },
  },
  methods: {
    addImage (image) {
      this.$store.commit('crop_entries/addImageToCurrentCropEntry', { crop_entry_id: this.current.id || 0, name: image })
    },
    removeImage (index) {
      this.$store.commit('crop_entries/removeImageFromCropEntry', index)
    },
    findRule (key, prop) {
      const rule = this.rules.find(r => r.key === key)
      if (!rule) {
        return true
      }
      return rule[prop]
    },
    submitForm (e) {
      e.preventDefault()
      this.loading = true
      this.error = null
      createCropEntry(this, this.currentCrop.id, this.current).then(res => {
        this.$emit('done')
      })
      .catch(err => {
        this.error = err.response?.data?.message || 'An error occurred'
      })
      .finally(() => {
        this.loading = false
      })
    }
  }
}
</script>

<style scoped lang="scss">
</style>