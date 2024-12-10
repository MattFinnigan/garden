<template>
  <div class="crop-entry-form">
    <h2>{{ title }}</h2>
    <Form v-show="!newUnit" @submit="submitForm">
      <template #inputs>
        <Select v-if="canEditPlant" v-model.number="plant_id" label="Select a Plant" :options="plants.map(p => { return { label: p.name + ' (' + p.variety + ')', value: p.id } })"/>
        <Display v-else label="Plant" :val="currPlant.name + ' (' + currPlant.variety + ')'"/>
        <Input type="number" v-model="daysToHarvest" label="Days to Harvest" />
        <Input v-if="findRule('name', 'enabled')" v-model="name" label="Name" required/>
        <Select v-model.number="location_id" label="Select a Location" :options="locations.map(l => { return { label: l.name, value: l.id } })"/>
        <Select v-if="currLocation" v-model.number="bed_id" label="Select a Bed (Optional)" :options="currLocation.beds.map(b => { return { label: b.name, value: b.id } })"/>
        <div class="inputs-row">
          <Select v-model="action" label="Action" :options="actionOptions"/>
          <Select v-model="stage" label="Lifecycle Stage" :options="stageOptions"/>
        </div>
        <div class="inputs-row">
          <Input v-if="findRule('datetimestamp', 'enabled')" v-model="datetimestamp" type="datetime-local" label="Date & Time"/>
          <Input v-model.number="qty" type="number" label="Quantity" required :min="findRule('qty', 'min') || qtyRule.min" :max="findRule('qty', 'max') || qtyRule.max"/>
        </div>
        <Input v-model="area" type="number" label="Spacing (cm)" :min="findRule('area', 'min') || spacingRule.min" :max="findRule('area', 'max') || spacingRule.max"/>
        <Input v-model="notes" type="textarea" label="Notes"/>
        <Input :modelValue="image" type="file" label="Image" @change="e => image = e.target.value"/>
        <p v-if="error" class="error">{{ error }}</p>
      </template>
      <template #buttons>
        <Button :disabled="loading" @click="$emit('close')">Cancel</Button>
        <Button type="submit" :disabled="loading">Submit</Button>
      </template>
    </Form>
    <!-- <Modal v-if="newUnit && savedEntry">
      <template #header>Individualise a crop plant</template>
      <template #content>
        <CropEntryForm
        :rules="[{ key: 'datetimestamp', enabled: false }, { key: 'qty', min: 1, max: savedEntry.qty }, { key: 'area', min: 1, max: savedEntry.qty }]"
        :handleSubmit="false"
        @close="cancelNewUnit"
        @submit="addNewUnit"/>
      </template>
    </Modal> -->
  </div>
</template>

<script>
import { createCropEntry, updateCropEntry } from '../../utils/api'
import { clone } from '../../utils/helpers';

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
  emits: ['close', 'submit'],
  data () {
    return {
      loading: false,
      newUnit: false,
      savedEntry: null,
      error: null
    }
  },
  mounted () {
    if (!this.currentCrop.id) {
      this.$store.commit('crops/setCurrentCropDaysToHarvest', this.currPlant.days_to_harvest)
    }
  },
  watch: {
    plant_id (val) {
      if (val) {
        this.$nextTick(() => {
          this.$store.commit('crops/setCurrentCropDaysToHarvest', this.currPlant.days_to_harvest)
        })
      }
    },
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
    unitsMapped () {
      return this.currentCrop.units.map(u => {
        return {
          id: u.id,
          name: u.name,
          curr_loc: u.location.name + (u.bed ? ' - ' + u.bed.name : ''),
          action: u.action,
          stage: u.stage,
          notes: u.notes
        }
      })
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
    location_id: {
      get () {
        return this.current?.location_id
      },
      set (val) {
        this.$store.commit('crop_entries/setCurrentCropEntryBed', null)
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
    },
    spacingRule () {
      let min = 1
      for (const unit in this.currentCrop.units) {
        if (unit.area) {
          min += unit.area
        }
      }
      return { min, max: 999999 }
    },
    qtyRule () {
      return { min: this.currentCrop.units.length, max: 999999 }
    }
  },
  methods: {
    addUnit () {
      this.savedEntry = clone(this.current)
      this.$store.commit('crop_entries/setCurrentCropEntry', {
        ...this.savedEntry,
        id: null,
        qty: 1,
        area: (this.savedEntry.area / this.savedEntry.qty) || 1,
        notes: '',
        image: '',
        name: this.currPlant.name + ' #' + (this.currentCrop.units.length + 1),
        isUnit: true
      })
      this.newUnit = true
    },
    addNewUnit (e, unit) {
      e.preventDefault()
      const u = clone(unit)
      u.crop_entry_id = this.savedEntry.id || null
      u.crop_entries = [unit]
      u.location = this.locations.find(l => l.id === u.location_id)
      u.bed = u.location.beds.find(b => b.id === u.bed_id)
      this.$store.commit('crop_entries/setCurrentCropEntry', this.savedEntry)
      this.$store.commit('crops/setCurrentCropUnits', [...this.currentCrop.units, clone(u)])
      this.newUnit = false
      this.savedEntry = null
    },
    cancelNewUnit () {
      this.newUnit = false
      this.$store.commit('crop_entries/setCurrentCropEntry', this.savedEntry)
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
      if (!this.handleSubmit) {
        this.$emit('submit', e, this.current)
        return
      }
      this.loading = true
      this.error = null
      if (!this.current.id) {
        createCropEntry(this, this.currentCrop.id, { ...this.current, days_to_harvest: this.daysToHarvest }).then((resp) => {
          if (resp.data.status === 'success') {
            this.$emit('close')
          } else {
            this.loading = false
            this.error = resp.data.message
          }
        })
      } else {
        updateCropEntry(this, this.current.id, { ...this.current, days_to_harvest: this.daysToHarvest }).then((resp) => {
          if (resp.data.status === 'success') {
            this.$emit('close')
          } else {
            this.loading = false
            this.error = resp.data.message
          }
        })
      } 
    }
  }
}
</script>

<style scoped lang="scss">
</style>