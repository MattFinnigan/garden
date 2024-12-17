<template>
  <div class="plant-form">
    <Form @submit="submitForm">
      <template #inputs>
        <Input type="text" v-model="name" label="Species" required minlength="2" maxlength="255"/>
        <Input type="text" v-model="variety" label="Variety" required maxlength="255"/>
        <Input type="textarea" v-model="description" label="Description" maxlength="255"/>
        <Input type="number" v-model="daysToHarvest" label="Days to Harvest" required min="1"/>
        <Select type="number" v-model.number="sow_from" :options="months" label="Sow From" required/>
        <Select type="number" v-model.number="sow_to" :options="months" label="Sow To" required/>
        <Input type="file" label="Image" @change="uploadImage" required/>
        <img v-if="image" :src="'./images/upload/' + image"/>
      </template>
      <template #buttons>
        <slot name="buttons"></slot>
      </template>
    </Form>
  </div>
</template>

<script>
import { createPlant, updatePlant, uploadImage } from '../../utils/api'

export default {
  name: 'PlantForm',
  emits: ['done'],
  data () {
    return {
      loading: false
    }
  },
  methods: {
    uploadImage (e) {
      const file = e.target.files[0]
      uploadImage(file).then(response => {
        this.image = response.data.image
      })
    },
    submitForm (e) {
      this.loading = true
      e.preventDefault()
      if (this.current.id) {
        updatePlant(this, this.current.id, this.current).then(response => {
          this.$store.commit('plants/setCurrentPlant', null)
          this.$emit('done')
        })
      } else {
        createPlant(this, this.current).then(response => {
          this.$store.commit('plants/setCurrentPlant', response.data.plant)
        })
      }
    },
    cancel () {
      this.$store.commit('plants/setCurrentPlant', null)
    }
  },
  computed: {
    current () {
      return this.$store.state.plants.current
    },
    name: {
      get () {
        return this.current.name
      },
      set (value) {
        this.$store.commit('plants/setCurrentPlantName', value)
      }
    },
    variety: {
      get () {
        return this.current.variety
      },
      set (value) {
        this.$store.commit('plants/setCurrentPlantVariety', value)
      }
    },
    description: {
      get () {
        return this.current.description
      },
      set (value) {
        this.$store.commit('plants/setCurrentPlantDescription', value)
      }
    },
    daysToHarvest: {
      get () {
        return this.current.days_to_harvest
      },
      set (value) {
        this.$store.commit('plants/setCurrentPlantDaysToHarvest', value)
      }
    },
    image: {
      get () {
        return this.current.image
      },
      set (value) {
        this.$store.commit('plants/setCurrentPlantImage', value)
      }
    },
    sow_from: {
      get () {
        return this.current.sow_from
      },
      set (value) {
        this.$store.commit('plants/setCurrentPlantSowFrom', value)
      }
    },
    sow_to: {
      get () {
        return this.current.sow_to
      },
      set (value) {
        this.$store.commit('plants/setCurrentPlantSowTo', value)
      }
    },
    months () {
      return [
        { value: 1, label: 'January' },
        { value: 2, label: 'February' },
        { value: 3, label: 'March' },
        { value: 4, label: 'April' },
        { value: 5, label: 'May' },
        { value: 6, label: 'June' },
        { value: 7, label: 'July' },
        { value: 8, label: 'August' },
        { value: 9, label: 'September' },
        { value: 10, label: 'October' },
        { value: 11, label: 'November' },
        { value: 12, label: 'December' }
      ]
    }
  }
}
</script>

<style scoped lang="scss">
h2 {
  margin: 0;
}
img {
  max-width: 100px;
  height: auto;
}
</style>