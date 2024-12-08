<template>
  <div class="location-form">
    <Modal @close="cancel">
      <template #header>
        <h2>Location Form</h2>
      </template>
      <template #content>
        <Form @submit="submitForm">
          <template #inputs>
            <div v-show="!currentBed">
              <Input type="text" v-model="name" label="Location name" required minlength="2" maxlength="255"/>
              <Input type="number" v-model.number="l" label="Length (cm)"/>
              <Input type="number" v-model.number="w" label="Width (cm)"/>
            </div>
            <BedsForm @done="addBed" @delete="deleteBed"/>
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
import { createLocation, updateLocation } from '../../utils/api'
import { clone } from '../../utils/helpers';
import BedsForm from './BedsForm.vue'

export default {
  components: {
    BedsForm
  },
  name: 'LocationForm',
  data () {
    return {
      loading: false
    }
  },
  methods: {
    addBed (bed) {
      if (bed.id) {
        this.beds = clone(this.beds).map(b => {
          if (b.id === bed.id) {
            return bed
          }
          return b
        })
      } else if (bed.index !== undefined) {
        this.beds = clone(this.beds).map((b, i) => {
          if (i === bed.index) {
            return bed
          }
          return b
        })
      } else {
        this.beds = [...this.beds, bed]
      }
    },
    deleteBed (index) {
      this.beds = clone(this.beds).filter((_, i) => i !== index)
    },
    submitForm (e) {
      this.loading = true
      e.preventDefault()
      if (this.current.id) {
        updateLocation(this, this.current.id, this.current).then(() => {
          this.$store.commit('locations/setCurrentLocation', null)
        })
      } else {
        createLocation(this, this.current).then(() => {
          this.$store.commit('locations/setCurrentLocation', null)
        })
      }
    },
    cancel () {
      this.$store.commit('locations/setCurrentLocation', null)
    }
  },
  computed: {
    current () {
      return this.$store.state.locations.current
    },
    name: {
      get () {
        return this.current.name
      },
      set (val) {
        this.$store.commit('locations/setCurrentLocationName', val)
      }
    },
    w: {
      get () {
        return this.current.w
      },
      set (val) {
        this.$store.commit('locations/setCurrentLocationWidth', val)
      }
    },
    l: {
      get () {
        return this.current.l
      },
      set (val) {
        this.$store.commit('locations/setCurrentLocationLength', val)
      }
    },
    beds: {
      get () {
        return this.current.beds
      },
      set (val) {
        this.$store.commit('locations/setCurrentLocationsBeds', val)
      }
    },
    currentBed () {
      return this.$store.state.beds.current
    }
  }
}
</script>

<style scoped lang="scss">
h2 {
  margin: 0;
}
.bed-form {
  background: $grey-800;
  padding: 1em;
  margin-bottom: 1.5em;
  border-radius: 0.5em;
}
</style>