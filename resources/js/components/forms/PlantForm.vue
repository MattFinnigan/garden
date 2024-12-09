<template>
  <div class="plant-form">
  <Modal @close="cancel">
    <template #header>
      <h2>Plant Form</h2>
    </template>
    <template #content>
      <Form @submit="submitForm">
        <template #inputs>
          <Input type="text" v-model="name" label="Species" required minlength="2" maxlength="255"/>
          <Input type="text" v-model="variety" label="Variety" required maxlength="255"/>
          <Input type="textarea" v-model="description" label="Description" maxlength="255"/>
          <Input type="number" v-model="daysToHarvest" label="Days to Harvest" required min="1"/>
          <Input type="file" label="Image" @change="uploadImage"/>
          <img v-if="image" :src="'./images/' + image"/>
        </template>
        <template #buttons>
          <Button type="submit" :disabled="loading">Submit</Button>
        </template>
      </Form>
    </template>
  </Modal>
</div>
</template>

<script>
import { createPlant, updatePlant, uploadImage } from '../../utils/api'

export default {
  name: 'PlantForm',
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
        })
      } else {
        createPlant(this, this.current).then(response => {
          this.$store.commit('plants/setCurrentPlant', null)
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