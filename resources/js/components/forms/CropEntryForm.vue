<template>
  <div class="crop-entry-form">
    <h2>{{ title }}</h2>
    <Form @submit="submitForm">
      <template #inputs>
        <Display label="Location" :val="locationName"/>
        <div v-if="canEditPlant" class="plant-edit">
          <Select
            v-model.number="plant_id"
            label="Plant"
            :options="plants.map(p => { return { label: p.name + ' (' + p.variety + ')', value: p.id } })"/>
          <Button class="icon primary" @click="$emit('createPlant')"><Icon name="plus"></Icon></Button>
        </div>
        <Display v-else label="Plant" :val="currPlantName"/>
        <Input type="number" v-model="daysToHarvest" label="Harvest" suffix="&nbsp; days"/>
        <Input v-if="findRule('name', 'enabled')" v-model="name" label="Name" required/>
        <Select v-model="action" label="Action" :options="actionOptions"/>
        <Select v-model="stage" label="Stage" :options="stageOptions"/>
        <Input v-if="findRule('datetimestamp', 'enabled')" v-model="datetimestamp" type="datetime-local" label="Date"/>
        <Input v-model.number="qty" type="number" label="Qty" required :min="1"/>
        <Input v-model.number="spacing_x" type="number" label="Spacing X" :min="findRule('area', 'min') || spacingRule.min" :max="findRule('area', 'max') || spacingRule.max" suffix="&nbsp;cm"/>
        <Input v-model.number="spacing_y" type="number" label="Spacing Y" :min="findRule('area', 'min') || spacingRule.min" :max="findRule('area', 'max') || spacingRule.max" suffix="&nbsp;cm"/>
        <Input v-model="notes" type="textarea" label="Notes"/>
        <Input :modelValue="image" type="file" label="Image" @change="e => image = e.target.value"/>
        <p v-if="error" class="error">{{ error }}</p>
      </template>
      <template #buttons>
        <slot name="buttons"></slot>
      </template>
    </Form>
  </div>
</template>

<script>
import { createCropEntry, updateCropEntry, createCrop, fetchPlants } from '../../utils/api'
import { clone, arrangePlantsInBedWithOverlapCheck } from '../../utils/helpers';

export default {
  name: 'CropEntryForm',
  props: {
    handleSubmit: {
      type: Boolean,
      default: true
    },
    title: String,
    rules: {
      type: Array,
      default: () => { return [{ key: 'name', enabled: false }] }
    }
  },
  emits: ['done', 'createPlant'],
  data () {
    return {
      loading: false,
      savedEntry: null,
      error: null
    }
  },
  mounted () {
    if (!this.currentCrop.id) {
      this.$store.commit('crops/setCurrentCropDaysToHarvest', this.currPlant.days_to_harvest)
    }
    if (!this.plants.length) {
      fetchPlants(this)
    }
    if (this.bed.id) {
      this.bed_id = this.bed.id
    }
  },
  watch: {
    plant_id (val) {
      if (val) {
        this.$nextTick(() => {
          this.$store.commit('crops/setCurrentCropDaysToHarvest', this.currPlant.days_to_harvest)
        })
      }
    }
  },
  computed: {
    plants () {
      return this.$store.state.plants.list
    },
    locationName () {
      return this.$store.state.locations.current.name + ' (' + this.bed.name + ')'
    },
    bed () {
      return this.$store.state.beds.current
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
      return this.$store.state.plants.current || this.plants.find(p => p.id === this.currentCrop?.plant_id)
    },
    currPlantName () {
      return this.currPlant?.name + ' (' + this.currPlant?.variety + ')'
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
        this.$store.commit('crops/setCurrentCropPlant', val)
      }
    },
    daysToHarvest: {
      get () {
        return this.$store.state.crops.current.days_to_harvest
      },
      set (value) {
        this.$store.commit('crops/setCurrentCropDaysToHarvest', value)
      }
    },
    name: {
      get () {
        return this.current?.name
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryName', val)
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
    spacing_x: {
      get () {
        return this.current?.spacing_x
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntrySpacingX', val)
      }
    },
    spacing_y: {
      get () {
        return this.current?.spacing_y
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntrySpacingY', val)
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
    },
    spacingRule () {
      // todo get from bed remaining area
      return { min: 1, max: 999999 }
    }
  },
  methods: {
    findRule (key, prop) {
      const rule = this.rules.find(r => r.key === key)
      if (!rule) {
        return true
      }
      return rule[prop]
    },
    submitForm (e) {
      e.preventDefault()
      if (!this.handleSubmit) {
        this.$emit('submit', e, this.current)
        return
      }
      this.loading = true
      this.error = null
      const bedData = clone(this.bed)
      // set plant positions
      if (!this.current.id) {
        bedData.crop_entries.push(this.current)
      } else {
        bedData.crop_entries = bedData.crop_entries.map(ce => {
          if (ce.id === this.current.id) {
            return this.current
          }
          return ce
        })
      }
      arrangePlantsInBedWithOverlapCheck(bedData, (entries) => {
        if (entries) {
          const entry = entries.find(e => e.id === this.current.id)
          this.$store.commit('crop_entries/setCurrentCropEntryPlantPos', entry.plant_pos)
          if (!this.currentCrop?.id) {
            createCrop(this, { ...this.currentCrop, ...this.current }, false).then(response => {
              if (response.data.status === 'success') {
                this.$store.commit('crops/setCurrentCrop', response.data.crop)
                this.$emit('done')
              } else {
                this.loading = false
                this.error = response.data.message
              }
            })
          } else if (!this.current.id) {
            createCropEntry(this, this.currentCrop.id, { ...this.current, days_to_harvest: this.daysToHarvest }).then((resp) => {
              if (resp.data.status === 'success') {
                this.$emit('done')
              } else {
                this.loading = false
                this.error = resp.data.message
              }
            })
          } else {
            updateCropEntry(this, this.current.id, { ...this.current, days_to_harvest: this.daysToHarvest }).then((resp) => {
              if (resp.data.status === 'success') {
                this.$emit('done')
              } else {
                this.loading = false
                this.error = resp.data.message
              }
            })
          } 
        } else {
          this.loading = false
          this.error = "Not enough space to place all plants."
        }
      })
    }
  }
}
</script>

<style scoped lang="scss">
.plant-edit {
  display: flex;
  align-items: center;
  justify-content: space-between;
  :deep(.select-container) {
    flex: 1;
    margin-right: 1em;
  }
}
</style>