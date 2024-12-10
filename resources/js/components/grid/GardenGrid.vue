<template>
  <div class="garden-grid">
    <Select v-model.number="loc_id" :options="locations.map(l => ({ label: l.name, value: l.id }))" label="Location" />
    <Grid
      v-if="currentLocation"
      :items="currentLocation.beds.filter(b => b.x && b.y)"
      @newBox="setNewBed">
      <template v-if="currentBed" #form>
        <div class="bed-form" :style="{}">
          <Input v-model="bed_name" label="Name" />
          <Button @click="currentBed = null; formPosition = null">Cancel</Button>
          <Button @click="submitBed">Submit</Button>
        </div>
      </template>
    </Grid>
  </div>
</template>

<script>
import Grid from '../grid/common/Grid.vue'
import { fetchLocations, updateLocation } from '../../utils/api';
import { clone } from '../../utils/helpers';
export default {
  name: 'GardenGrid',
  components: {
    Grid
  },
  data () {
    return {
    }
  },
  mounted () {
    fetchLocations(this).then(() => {
      this.loc_id = this.locations[this.locations.length - 1].id
    })
  },
  computed: {
    locations () {
      return this.$store.state.locations.list
    },
    loc_id: {
      get () {
        return this.$store.state.locations.current?.id
      },
      set (value) {
        this.$store.commit('locations/setCurrentLocation', this.locations.find(l => l.id === value))
      }
    },
    currentBed: {
      get () {
        return this.$store.state.beds.current
      },
      set (value) {
        this.$store.commit('beds/setCurrentBed', value)
      }
    },
    bed_name: {
      get () {
        return this.currentBed.name
      },
      set (value) {
        this.currentBed = { ...this.currentBed, name: value }
      }
    },
    bed_x: {
      get () {
        return this.currentBed.x
      },
      set (value) {
        this.currentBed = { ...this.currentBed, x: value }
      }
    },
    bed_y: {
      get () {
        return this.currentBed.y
      },
      set (value) {
        this.currentBed = { ...this.currentBed, y: value }
      }
    },
    bed_w: {
      get () {
        return this.currentBed.w
      },
      set (value) {
        this.currentBed = { ...this.currentBed, w: value }
      }
    },
    bed_l: {
      get () {
        return this.currentBed.l
      },
      set (value) {
        this.currentBed = { ...this.currentBed, l: value }
      }
    },
    currentLocation () {
      return this.$store.state.locations.current
    }
  },
  methods: {
    setNewBed (pos) {
      this.currentBed = {
        name: '',
        location_id: this.currentLocation.id,
        x: pos.x,
        y: pos.y,
        w: pos.w,
        l: pos.l
      }
    },
    submitBed (e) {
      e.preventDefault()
      let beds = clone(this.currentLocation.beds) || []
      if (this.currentBed.id) {
        beds = beds.map(b => {
          if (b.id === this.currentBed.id) {
            return this.currentBed
          }
          return b
        })
      } else {
        beds = [...beds, this.currentBed]
      }
      const postData = { ...this.currentLocation, beds }
      console.log(postData)
      updateLocation(this, this.currentLocation.id, postData).then(() => {
        this.currentBed = null
        this.formPosition = null
      })
    },
  }
}
</script>

<style scoped>
.garden-grid {
  /* Your component styles go here */
}
</style>