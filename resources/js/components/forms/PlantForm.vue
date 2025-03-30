<template>
  <div class="plant-form">
    <Form @submit="submitForm">
      <template #inputs>
        <Input type="text" v-model="name" label="Species" required minlength="2" maxlength="255"/>
        <Input type="text" v-model="variety" label="Variety" required maxlength="255"/>
        <Input type="number" v-model="daysToHarvest" label="Harvest" required min="1" suffix="&nbsp;day(s)"/>
        <Select v-model.number="sow_from" :options="monthOptionsLong" label="Sow on" required/>
        <Select v-model.number="sow_to" :options="monthOptionsLong" label="Until" required/>
        <Input v-model.number="spacing" type="number" label="Spacing" min="0" max="9999" suffix="&nbsp;cm" required/>
        <Input type="textarea" v-model="description" label="Notes" maxlength="255"/>
        <Input v-if="image" type="file" label="Image" @change="uploadImage"/>
        <Input v-else type="file" label="Image" @change="uploadImage" required/>
        <img v-if="image" :src="'./images/upload/' + image"/>
      </template>
      <template #buttons>
        <slot name="buttons"></slot>
      </template>
    </Form>
  </div>
</template>

<script>
import { defaultPlant, monthOptionsLong } from '../../utils/consts'
import { createPlant, updatePlant, uploadImage } from '../../utils/api'

export default {
  name: 'PlantForm',
  emits: ['done', 'cancel'],
  data () {
    return {
      monthOptionsLong: monthOptionsLong(),
      loading: false
    }
  },
  created () {
    if (!this.current?.id) {
      this.$store.commit('plants/setCurrentPlant', defaultPlant())
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
          this.$emit('done', response.data.plant)
        })
      } else {
        createPlant(this, this.current).then(response => {
          this.$store.commit('plants/setCurrentPlant')
          this.$emit('done', response.data.plant)
        })
      }
    },
    cancel () {
      this.$emit('cancel')
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
    spacing: {
      get () {
        return this.current.spacing
      },
      set (value) {
        this.$store.commit('plants/setCurrentPlantSpacing', value)
      }
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