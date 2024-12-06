<template>
  <div class="location-page">
    <div class="header-contain">
      <h1>Locations</h1>
      <Button classes="sm" @click="newLocation">Add Location</Button>
    </div>
    <div v-if="loading">Loading...</div>
    <div v-else>
      <div v-for="location in locations" :key="location.id">
        <Card :title="location.name" :description="location.description" :image="location.image">
          <template #actions>
            <Button classes="sm" @click="editLocation(location.id)">Edit</Button>
            <Button classes="sm" @click="handleDelete(location.id)">Delete</Button>
          </template>
          <template #content>
            <Table :headers="[{ label: 'Name', key: 'name' }, { label: 'Description', key: 'description' }]" :rows="location.beds">
              <template #header>
                <h3>Beds</h3>
              </template>
            </Table>
          </template>
        </Card>
      </div>
    </div>
    <LocationForm v-if="current"/>
  </div>
</template>

<script>
import Card from '../common/Card.vue'
import LocationForm from '../forms/LocationForm.vue';
import { fetchLocations, deleteLocation } from '../../utils/api'
import { clone } from '../../utils/helpers'

export default {
  name: 'Locations',
  components: {
    Card,
    LocationForm
  },
  data () {
    return {
      loading: true
    }
  },
  mounted () {
    if (!this.locations.length) {
      this.getLocations()
    } else {
      this.loading = false
    }
  },
  computed: {
    locations () {
      return this.$store.state.locations.list
    },
    current () {
      return this.$store.state.locations.current
    }
  },
  methods: {
    getLocations () {
      this.loading = true
      fetchLocations(this).then(() => { this.loading = false })
    },
    handleDelete (id) {
      this.loading = true
      deleteLocation(this, id).then(() => { this.loading = false })
    },
    newLocation () {
      this.$store.commit('locations/setCurrentLocation', { name: '', beds: [] })
    },
    editLocation (id) {
      this.$store.commit('locations/setCurrentLocation', clone(this.locations.find(location => location.id === id)))
    }
  }
}
</script>

<style scoped lang="scss">
.header-contain {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1em;
  h1 {
    margin: 0;
  }
}
</style>